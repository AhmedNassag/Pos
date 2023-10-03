<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\Payment_Sale_Export;
use App\Mail\Payment_Sale;
use App\Models\Client;
use App\Models\PaymentSale;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PaymentWithCreditCard;
use Twilio\Rest\Client as Client_Twilio;
use Stripe;
use DB;
use PDF;
use App\Http\Controllers\Controller;

class PaymentSalesController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:إضافة دفع المبيعات', ['only' => ['store']]);
        $this->middleware('permission:تعديل دفع المبيعات', ['only' => ['update']]);
        $this->middleware('permission:حذف دفع المبيعات', ['only' => ['destroy']]);
    }
    


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'date'    => 'required',
                'montant' => 'required',
            ]);

            $sale = Sale::findOrFail($request['id']);

            $total_paid = $sale->paid_amount + $request['montant'];
            $due        = $sale->GrandTotal - $total_paid;

            if ($due < 0.0)
            {
                session()->flash('paymentGreaterThanDue');
                return redirect()->back();
            }
            else if ($due === 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $sale->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $sale->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            if($request['montant'] > 0)
            {
                // Paying Method cach
                $PaymentSale = PaymentSale::create([
                    'sale_id'   => $request['id'],
                    'Ref'       => $this->getNumberOrder(),
                    'date'      => $request['date'],
                    'Reglement' => 'cach',
                    'montant'   => $request['montant'],
                    // 'change' => $request['change'],
                    'notes'     => $request['notes'],
                    'user_id'   => Auth::user()->id,
                ]);

                $sale->update([
                    'paid_amount'    => $total_paid,
                    'payment_status' => $payment_status,
                ]);
            }

            //send notification mail
            // $user = User::where('id', Auth::user()->id)->get();
            // Notification::send($user, new PaymentSaleReturnAdded($sale->id));

            session()->flash('success');
            return redirect()->back();

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'date'    => 'required',
                'montant' => 'required',
            ]);

            $payment = PaymentSale::findOrFail($request->id);
            $sale    = Sale::find($payment->sale_id);

            $old_total_paid = $sale->paid_amount - $payment->montant;
            $new_total_paid = $old_total_paid + $request['montant'];
            $due            = $sale->GrandTotal - $new_total_paid;

            if ($due < 0.0)
            {
                session()->flash('paymentGreaterThanDue');
                return redirect()->back();
            }
            else if ($due === 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $sale->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $sale->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            
            // Paying Method cach
            $payment->update([
                'date'      => $request['date'],
                'Reglement' => 'cach',
                'montant'   => $request['montant'],
                // 'change'    => $request['change'],
                'notes'     => $request['notes'],
            ]);

            $sale->update([
                'paid_amount'    => $new_total_paid,
                'payment_status' => $payment_status,
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    

    public function destroy(Request $request)
    {
        try{
            $payment = PaymentSale::findOrFail($request->id);
            $sale    = Sale::find($payment->sale_id);

            $total_paid = $sale->paid_amount - $payment->montant;
            $due        = $sale->GrandTotal - $total_paid;

            if ($due === 0.0 || $due < 0.0) 
            {
                $payment_status = 'paid';
            }
            else if ($due !== $sale->GrandTotal)
            {
                $payment_status = 'partial';
            }
            else if ($due === $sale->GrandTotal)
            {
                $payment_status = 'unpaid';
            }

            $PaymentSale = PaymentSale::whereId($request->id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            $sale->update([
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
            $last = DB::table('payment_sales')->latest('id')->first();
            if ($last) 
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            } 
            else 
            {
                $code = 'INV/SL_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
