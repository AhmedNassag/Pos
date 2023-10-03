<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'purchase_details';
    protected $guarded = [];

    protected $casts = [
        'total'              => 'double',
        'cost'               => 'double',
        'TaxNet'             => 'double',
        'discount'           => 'double',
        'quantity'           => 'double',
        'purchase_id'        => 'integer',
        'purchase_unit_id'   => 'integer',
        'product_id'         => 'integer',
        'product_variant_id' => 'integer',
    ];

    //start relations
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
