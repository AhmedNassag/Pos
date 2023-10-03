<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentPurchaseReturns extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'payment_purchase_returns';
    protected $guarded = [];

    protected $casts = [
        'montant'            => 'double',
        'change'             => 'double',
        'purchase_return_id' => 'integer',
        'user_id'            => 'integer',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function PurchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

}
