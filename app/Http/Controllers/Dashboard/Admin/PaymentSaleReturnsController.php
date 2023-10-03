<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\Payment_Sale_Return_Export;
use Twilio\Rest\Client as Client_Twilio;
use App\Mail\PaymentReturn;
use App\Models\Client;
use App\Models\PaymentSaleReturns;
use App\Models\Role;
use App\Models\SaleReturn;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use PDF;
use App\Http\Controllers\Controller;

class PaymentSaleReturnsController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:إضافة دفع مرتجع المبيعات', ['only' => ['store']]);
        $this->middleware('permission:تعديل دفع مرتجع المبيعات', ['only' => ['update']]);
        $this->middleware('permission:حذف دفع مرتجع المبيعات', ['only' => ['destroy']]);
    }
    


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'date'    => 'required',
                'montant' => 'required',
            ]);
        
            if($request['montant'] > 0)
            {
                $SaleReturn = SaleReturn::findOrFail($request['id']);

                $total_paid = $SaleReturn->paid_amount + $request['montant'];
                $due        = $SaleReturn->GrandTotal - $total_paid;

                if ($due < 0.0)
                {
                    session()->flash('paymentGreaterThanDue');
                    return redirect()->back();
                }
                else if ($due === 0.0) 
                {
                    $payment_status = 'paid';
                } 
                else if ($due !== $SaleReturn->GrandTotal) 
                {
                    $payment_status = 'partial';
                } 
                else if ($due === $SaleReturn->GrandTotal) 
                {
                    $payment_status = 'unpaid';
                }

                $PaymentSaleReturns = PaymentSaleReturns::create([
                    'sale_return_id' => $request['id'],
                    'Ref'            => $this->getNumberOrder(),
                    'date'           => $request['date'],
                    'Reglement'      => 'cach',
                    'montant'        => $request['montant'],
                    // 'change'         => $request['change'],
                    'notes'          => $request['notes'],
                    'user_id'        => Auth::user()->id,
                ]);

                $SaleReturn->update([
                    'paid_amount'    => $total_paid,
                    'payment_status' => $payment_status,
                ]);

            }

            //send notification mail
            // $user = User::where('id', Auth::user()->id)->get();
            // Notification::send($user, new PaymentSaleeReturnAdded($SaleReturn->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'date'    => 'required',
                'montant' => 'required',
            ]);

            $payment    = PaymentSaleReturns::findOrFail($request->id);
            $SaleReturn = SaleReturn::find($payment->sale_return_id);
            
            $old_total_paid = $SaleReturn->paid_amount - $payment->montant;
            $new_total_paid = $old_total_paid + $request['montant'];
            $due            = $SaleReturn->GrandTotal - $new_total_paid;

            if ($due < 0.0)
            {
                session()->flash('paymentGreaterThanDue');
                return redirect()->back();
            }
            else if ($due === 0.0)
            {
                $payment_status = 'paid';
            }
            else if ($due !== $SaleReturn->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $SaleReturn->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            $payment->update([
                'date'      => $request['date'],
                'Reglement' => 'cach',
                'montant'   => $request['montant'],
                // 'change'    => $request['change'],
                'notes'     => $request['notes'],
            ]);
    
            $SaleReturn->update([
                'paid_amount'    => $new_total_paid,
                'payment_status' => $payment_status,
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function destroy(Request $request)
    {
        try {
            $payment    = PaymentSaleReturns::findOrFail($request->id);
            $SaleReturn = SaleReturn::find($payment->sale_return_id);
            
            $total_paid = $SaleReturn->paid_amount - $payment->montant;
            $due        = $SaleReturn->GrandTotal - $total_paid;

            if ($due === 0.0 || $due < 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $SaleReturn->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $SaleReturn->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            $PaymentSaleReturns = PaymentSaleReturns::whereId($request->id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            $SaleReturn->update([
                'paid_amount'    => $total_paid,
                'payment_status' => $payment_status,
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function getNumberOrder()
    {
        try {
            $last = DB::table('payment_sale_returns')->latest('id')->first();
            if ($last) 
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            }
            else
            {
                $code = 'INV/RT_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
