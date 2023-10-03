<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleReturnDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'sale_return_details';
    protected $guarded = [];

    protected $casts = [
        'total'              => 'double',
        'quantity'           => 'double',
        'sale_return_id'     => 'integer',
        'product_id'         => 'integer',
        'sale_unit_id'       => 'integer',
        'product_variant_id' => 'integer',
        'price'              => 'double',
        'TaxNet'             => 'double',
        'discount'           => 'double',
    ];

    //start relations
    public function SaleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
