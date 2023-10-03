<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'purchase_returns';
    protected $guarded = [];

    protected $casts = [
        'GrandTotal'   => 'double',
        'user_id'      => 'integer',
        'provider_id'  => 'integer',
        'warehouse_id' => 'integer',
        'discount'     => 'double',
        'shipping'     => 'double',
        'TaxNet'       => 'double',
        'tax_rate'     => 'double',
        'paid_amount'  => 'double',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseReturnDetails::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function facture()
    {
        return $this->hasMany(PaymentPurchaseReturns::class);
    }
}
