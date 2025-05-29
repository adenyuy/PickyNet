<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Penting: ini harus ada untuk UUID

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'log_id', // Tambahkan ini agar bisa diisi (meskipun auto-generated)
        'user_id',
        'result', // Kolom JSON untuk menyimpan hasil akhir perhitungan WASPAS
    ];

    protected $casts = [
        'result' => 'json', // Ini sudah benar
    ];

    /**
     * Boot method untuk menghasilkan UUID secara otomatis untuk primary key.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculations() // Relasi ke model Calculation
    {
        return $this->hasMany(Calculation::class, 'log_id', 'log_id'); // Pastikan FK sesuai
    }
}