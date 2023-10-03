<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'units';
    protected $guarded = [];

    protected $casts = [
        'base_unit'      => 'integer',
        'operator_value' => 'float',
        'is_active'      => 'integer',
    ];

    //start relations
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
