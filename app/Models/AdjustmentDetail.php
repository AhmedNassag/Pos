<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AdjustmentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'adjustment_details';
    protected $guarded = [];

    protected $casts = [
        'adjustment_id'      => 'integer',
        'product_id'         => 'integer',
        'quantity'           => 'double',
        'product_variant_id' => 'integer',
    ];

    //start relations
    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
