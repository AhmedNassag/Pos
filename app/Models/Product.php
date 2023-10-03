<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'products';
    protected $guarded = [];

    protected $casts = [
        'category_id'      => 'integer',
        'sub_category_id'  => 'integer',
        'unit_id'          => 'integer',
        'unit_sale_id'     => 'integer',
        'unit_purchase_id' => 'integer',
        'is_variant'       => 'integer',
        'brand_id'         => 'integer',
        'is_active'        => 'integer',
        'cost'             => 'double',
        'price'            => 'double',
        'stock_alert'      => 'double',
        'TaxNet'           => 'double',
    ];

    //start relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function unitPurchase()
    {
        return $this->belongsTo(Unit::class, 'unit_purchase_id');
    }

    public function unitSale()
    {
        return $this->belongsTo(Unit::class, 'unit_sale_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function PurchaseDetail()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function PurchaseReturnDetails()
    {
        return $this->hasMany(PurchaseReturnDetails::class);
    }

    public function SaleDetail()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function SaleReturnDetails()
    {
        return $this->hasMany(SaleReturnDetails::class);
    }

    public function AdjustmentDetail()
    {
        return $this->hasMany(AdjustmentDetail::class);
    }

    public function QuotationDetail()
    {
        return $this->hasMany(QuotationDetail::class);
    }
}