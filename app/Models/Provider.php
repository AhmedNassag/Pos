<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'providers';
    protected $guarded = [];

    protected $casts = [
        'code' => 'integer',
    ];

    //start relations
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
