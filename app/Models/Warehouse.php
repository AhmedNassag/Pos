<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'warehouses';
    protected $guarded = [];

    //start relations
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    
    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }
    
    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }
    
}
