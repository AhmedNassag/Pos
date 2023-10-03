<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table   = 'expense_categories';
    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
    ];

    //start relations
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
