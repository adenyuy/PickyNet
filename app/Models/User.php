<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Jika Anda menggunakannya
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str; // Untuk boot() method UUID

class User extends Authenticatable // Pertimbangkan MustVerifyEmail jika perlu
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', // Baik ada di sini jika Anda set manual di Seeder/kode
        'username',
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        // 'has_seen_description', // Kita tunda dulu
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'has_seen_description' => 'boolean', // Jika nanti ditambahkan
    ];

    // Metode untuk auto-generate UUID jika tidak diisi manual
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi BARU ke SpkSession
    public function spkSessions()
    {
        return $this->hasMany(SpkSession::class, 'user_id', 'user_id');
    }

    // Relasi LAMA (kemungkinan akan dihapus/tidak dipakai)
    // public function logs() { /* ... */ }
    // public function scores() { /* ... */ }
    // public function calculations() { /* ... */ }
}