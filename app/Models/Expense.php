<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'expenses';
    protected $guarded = [];

    protected $casts = [
        'user_id'             => 'integer',
        'expense_category_id' => 'integer',
        'warehouse_id'        => 'integer',
        'amount'              => 'double',
    ];

    //start relations
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
