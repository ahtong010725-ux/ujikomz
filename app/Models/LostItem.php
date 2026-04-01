<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
protected $fillable = [
    'user_id',
    'name',
    'brand_name',
    'item_name',
    'item_type',
    'location',
    'date',
    'description',
    'reward_offered',
    'photo',
    'status'
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function claims()
{
    return $this->hasMany(Claim::class, 'item_id')->where('item_type', 'lost');
}

}
