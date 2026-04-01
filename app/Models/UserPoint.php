<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'total_earned',
        'month',
        'year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
