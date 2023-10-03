<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentSale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'payment_sales';
    protected $guarded = [];

    protected $casts = [
        'montant' => 'double',
        'change'  => 'double',
        'sale_id' => 'integer',
        'user_id' => 'integer',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}
