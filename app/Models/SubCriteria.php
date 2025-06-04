<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCriteria extends Model
{
    use HasFactory;

    protected $primaryKey = 'subkriteria_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subkriteria_id',
        'kriteria_id',
        'name',
        'value',
        'range_min', // <-- BAGUS, ini perlu ditambahkan setelah migrasi
        'range_max', // <-- BAGUS, ini perlu ditambahkan setelah migrasi
    ];

    // Tambahkan casts untuk tipe data yang spesifik
    protected $casts = [
        'value' => 'integer', // Sesuai migrasi Anda
        'range_min' => 'decimal:2', // Sesuaikan presisi jika berbeda
        'range_max' => 'decimal:2', // Sesuaikan presisi jika berbeda
    ];

    public $timestamps = true; // Sesuai migrasi Anda

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'kriteria_id', 'kriteria_id');
    }
}