<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'sales';
    protected $guarded = [];

    protected $casts = [
        'is_pos'       => 'integer',
        'GrandTotal'   => 'double',
        'qte_return'   => 'double',
        'total_return' => 'double',
        'user_id'      => 'integer',
        'client_id'    => 'integer',
        'warehouse_id' => 'integer',
        'discount'     => 'double',
        'shipping'     => 'double',
        'TaxNet'       => 'double',
        'tax_rate'     => 'double',
        'paid_amount'  => 'double',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function facture()
    {
        return $this->hasMany(PaymentSale::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
