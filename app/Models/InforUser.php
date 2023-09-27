<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class InforUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_user',
        'date_of_birth',
        'google_id',
        'gender',
    ];

    public function notifies()
    {
        return $this->hasMany(Notify::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}