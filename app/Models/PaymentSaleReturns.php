<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentSaleReturns extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'payment_sale_returns';
    protected $guarded = [];

    protected $casts = [
        'montant'        => 'double',
        'change'         => 'double',
        'sale_return_id' => 'integer',
        'user_id'        => 'integer',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function SaleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

}
