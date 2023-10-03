<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'purchases';
    protected $guarded = [];

    protected $casts = [
        'provider_id'  => 'integer',
        'warehouse_id' => 'integer',
        'GrandTotal'   => 'double',
        'discount'     => 'double',
        'shipping'     => 'double',
        'TaxNet'       => 'double',
        'tax_rate'     => 'double',
        'paid_amount'  => 'double',
    ];

    //start relations
    public function purchase_details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function facture()
    {
        return $this->hasMany(PaymentPurchase::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
