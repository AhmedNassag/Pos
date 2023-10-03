<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'purchase_return_details';
    protected $guarded = [];

    protected $casts = [
        'total'              => 'double',
        'quantity'           => 'double',
        'purchase_return_id' => 'integer',
        'purchase_unit_id'   => 'integer',
        'product_id'         => 'integer',
        'product_variant_id' => 'integer',
        'cost'               => 'double',
        'TaxNet'             => 'double',
        'discount'           => 'double',
    ];

    //start relations
    public function PurchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
