<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   protected $fillable = [
    'nisn',
    'name',
    'kelas',
    'phone',
    'tanggal_lahir',
    'jenis_kelamin',
    'photo',
    'password',
    'role',
    'registration_status',
    'rejection_reason',
    'is_online',
    'last_seen'
];

public function messages()
{
    return $this->hasMany(Message::class, 'sender_id')
        ->orWhere('receiver_id', $this->id);
}

public function points()
{
    return $this->hasOne(UserPoint::class);
}

public function claims()
{
    return $this->hasMany(Claim::class, 'claimer_id');
}

public function getPointsCount()
{
    return $this->points ? $this->points->points : 0;
}

public function getTotalEarned()
{
    return $this->points ? $this->points->total_earned : 0;
}

public function getEarnedBadges()
{
    $totalEarned = $this->getTotalEarned();
    return Badge::where('points_required', '<=', $totalEarned)->orderBy('points_required', 'desc')->get();
}

public function getHighestBadge()
{
    $totalEarned = $this->getTotalEarned();
    return Badge::where('points_required', '<=', $totalEarned)->orderBy('points_required', 'desc')->first();
}
}
