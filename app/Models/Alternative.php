<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk boot() method UUID

class Alternative extends Model
{
    use HasFactory;

    protected $primaryKey = 'alternative_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'alternative_id', // Tambahkan jika Anda set UUID secara manual saat create
        'name',
        'image_path',
    ];

    // Metode untuk auto-generate UUID
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi LAMA (kemungkinan akan dihapus/tidak dipakai)
    // public function scores() { /* ... */ }
    // public function calculations() { /* ... */ }
}