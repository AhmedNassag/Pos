<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'adjustments';
    protected $guarded = [];

    protected $casts = [
        'user_id'      => 'integer',
        'warehouse_id' => 'integer',
    ];

    //start relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(AdjustmentDetail::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
