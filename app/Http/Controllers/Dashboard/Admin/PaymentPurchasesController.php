<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\Payment_Purchase_Export;
use App\Mail\Payment_Purchase;
use App\Models\PaymentPurchase;
use App\Models\Provider;
use App\Models\Purchase;
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

class PaymentPurchasesController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:إضافة دفع المشتريات', ['only' => ['store']]);
        $this->middleware('permission:تعديل دفع المشتريات', ['only' => ['update']]);
        $this->middleware('permission:حذف دفع المشتريات', ['only' => ['destroy']]);
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
                $purchase = Purchase::findOrFail($request['id']);
        
                $total_paid = $purchase->paid_amount + $request['montant'];
                $due        = $purchase->GrandTotal - $total_paid;

                if ($due < 0.0)
                {
                    session()->flash('paymentGreaterThanDue');
                    return redirect()->back();
                }
                else if ($due === 0.0) 
                {
                    $payment_status = 'paid';
                } 
                else if ($due !== $purchase->GrandTotal) 
                {
                    $payment_status = 'partial';
                } 
                else if ($due === $purchase->GrandTotal) 
                {
                    $payment_status = 'unpaid';
                }

                $payment_purchase = PaymentPurchase::create([
                    'purchase_id' => $request['id'],
                    'Ref'         => $this->getNumberOrder(),
                    'date'        => $request['date'],
                    'Reglement'   => 'cach',
                    'montant'     => $request['montant'],
                    // 'change'      => $request['change'],
                    'notes'       => $request['notes'],
                    'user_id'     => Auth::user()->id,
                ]);

                $purchase->update([
                    'paid_amount'    => $total_paid,
                    'payment_status' => $payment_status,
                ]);

            }

            //send notification mail
            // $user = User::where('id', Auth::user()->id)->get();
            // Notification::send($user, new PaymentPurchaseAdded($purchase->id));

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

            $payment  = PaymentPurchase::findOrFail($request->id);
            $purchase = Purchase::whereId($payment['purchase_id'])->first();

            $old_total_paid = $purchase->paid_amount - $payment->montant;
            $new_total_paid = $old_total_paid + $request['montant'];
            $due            = $purchase->GrandTotal - $new_total_paid;
            
            if ($due < 0.0)
            {
                session()->flash('paymentGreaterThanDue');
                return redirect()->back();
            }
            else if ($due === 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $purchase->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $purchase->GrandTotal) 
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

            $purchase->paid_amount    = $new_total_paid;
            $purchase->payment_status = $payment_status;
            $purchase->save();

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function destroy(Request $request)
    {
        try {
            $payment  = PaymentPurchase::findOrFail($request->id);
            $purchase = Purchase::find($payment->purchase_id);
            
            $total_paid = $purchase->paid_amount - $payment->montant;
            $due        = $purchase->GrandTotal - $total_paid;

            if ($due === 0.0 || $due < 0.0) 
            {
                $payment_status = 'paid';
            } 
            else if ($due !== $purchase->GrandTotal) 
            {
                $payment_status = 'partial';
            } 
            else if ($due === $purchase->GrandTotal) 
            {
                $payment_status = 'unpaid';
            }

            $paymentPurchase = PaymentPurchase::whereId($request->id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            $purchase->update([
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
            $last = DB::table('payment_purchases')->latest('id')->first();
            if ($last) 
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            } 
            else 
            {
                $code = 'INV/PR_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
