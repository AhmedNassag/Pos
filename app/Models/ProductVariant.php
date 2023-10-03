<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'product_variants';
    protected $guarded = [];

    protected $casts = [
        'product_id' => 'integer',
        'qty'        => 'double',
    ];

}
