<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Jika Anda menggunakan Sanctum, biarkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    // use HasApiTokens; // Jika Anda menggunakan Sanctum

    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', // Tambahkan ini agar user_id bisa diisi saat create
        'username',
        'name',     // <<< Tambahkan ini untuk nama lengkap dari Google
        'email',
        'password',
        'google_id', // <<< Tambahkan ini
        'avatar',    // <<< Tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed', // Jika password Anda di-cast hashed
    ];

    public function logs()
    {
        return $this->hasMany(Log::class, 'user_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'user_id');
    }

    public function calculations()
    {
        return $this->hasMany(Calculation::class, 'user_id');
    }
}