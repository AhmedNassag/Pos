<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\Payment_Purchase_Return_Export;
use App\Mail\PaymentReturn;
use App\Models\PaymentPurchaseReturns;
use App\Models\Provider;
use App\Models\PurchaseReturn;
use App\Models\Role;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Client as Client_Twilio;
use DB;
use PDF;
use App\Http\Controllers\Controller;

class PaymentPurchaseReturnsController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:إضافة دفع مرتجع المشتريات', ['only' => ['store']]);
        $this->middleware('permission:تعديل دفع مرتجع المشتريات', ['only' => ['update']]);
        $this->middleware('permission:حذف دفع مرتجع المشتريات', ['only' => ['destroy']]);
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
                $purchaseReturn = PurchaseReturn::findOrFail($request['id']);
                
                $total_paid     = $purchaseReturn->paid_amount + $request['montant'];
                $due            = $purchaseReturn->GrandTotal - $total_paid;

                if ($due < 0.0)
                {
                    session()->flash('paymentGreaterThanDue');
                    return redirect()->back();
                }
                else if ($due === 0.0) 
                {
                    $payment_status = 'paid';
                } 
                else if ($due !== $purchaseReturn->GrandTotal) 
                {
                    $payment_status = 'partial';
                } 
                else if ($due === $purchaseReturn->GrandTotal) 
                {
                    $payment_status = 'unpaid';
                }

                $payment_purchase_return = PaymentPurchaseReturns::create([
                    'purchase_return_id' => $request['id'],
                    'Ref'                => $this->getNumberOrder(),
                    'date'               => $request['date'],
                    'Reglement'          => 'cach',
                    'montant'            => $request['montant'],
                    // 'change'             => $request['change'],
                    'notes'              => $request['notes'],
                    'user_id'            => Auth::user()->id,
                ]);

                $purchaseReturn->update([
                    'paid_amount'    => $total_paid,
                    'payment_status' => $payment_status,
                ]);
            }

            //send notification mail
            // $user = User::where('id', Auth::user()->id)->get();
            // Notification::send($user, new PaymentPurchaseReturnAdded($purchaseReturn->id));

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

            $payment        = PaymentPurchaseReturns::findOrFail($request->id);
            $purchaseReturn = PurchaseReturn::find($payment->purchase_return_id);

            $old_total_paid = $purchaseReturn->paid_amount - $payment->montant;
            $new_total_paid = $old_total_paid + $request['montant'];
            $due            = $purchaseReturn->GrandTotal - $new_total_paid;

            if ($due < 0.0)
            {
                session()->flash('paymentGreaterThanDue');
                return redirect()->back();
            }
            else if ($due === 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $purchaseReturn->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $purchaseReturn->GrandTotal) 
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

            $purchaseReturn->update([
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
            $payment        = PaymentPurchaseReturns::findOrFail($request->id);
            $purchaseReturn = PurchaseReturn::find($payment->purchase_return_id);
            
            $total_paid = $purchaseReturn->paid_amount - $payment->montant;
            $due        = $purchaseReturn->GrandTotal - $total_paid;

            if ($due === 0.0 || $due < 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $purchaseReturn->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $purchaseReturn->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            $paymentPurchaseReturn = PaymentPurchaseReturns::whereId($request->id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            $purchaseReturn->update([
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
            $last = DB::table('payment_purchase_returns')->latest('id')->first();
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
