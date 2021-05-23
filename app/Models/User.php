<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'exp',
        'rank',
        'is_admin',
        'photo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function checkRank()
    {
        if ($this->exp >= 10 && $this->exp < 50) {
            $this->rank = 'Level 1';
        } elseif ($this->exp >= 50 && $this->exp < 100) {
            $this->rank = 'Level 2';
        } elseif ($this->exp >= 100 && $this->exp < 200) {
            $this->rank = 'Level 3';
        }
        $this->save();
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'user_id')->latest();
    }
}
