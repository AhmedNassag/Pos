<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'categories';
    protected $guarded = [];

    //start relations
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
