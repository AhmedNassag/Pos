<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'clients';
    protected $guarded = [];

    protected $casts = [
        'code' => 'integer',
    ];

    //start relations
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }
}
