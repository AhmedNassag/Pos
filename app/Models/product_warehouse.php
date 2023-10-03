<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class product_warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'product_warehouse';
    protected $guarded = [];

    protected $casts = [
        'product_id'   => 'integer',
        'warehouse_id' => 'integer',
        'qte'          => 'double',
    ];

    //start relations
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function productVariant()
    {
        return $this->belongsTo('App\Models\ProductVariant');
    }

}
