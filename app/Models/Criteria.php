<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk boot() method UUID

class Criteria extends Model
{
    use HasFactory;

    protected $primaryKey = 'kriteria_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kriteria_id',
        'name',
        'type',
        'input_method', // <-- BAGUS, ini perlu ditambahkan setelah migrasi
        'weight',
    ];

    // Tambahkan casts untuk tipe data yang spesifik
    protected $casts = [
        'weight' => 'decimal:2', // Sesuai migrasi decimal(5,2)
        // 'input_method' => 'string', // Tidak wajib, enum akan dihandle sbg string
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function subCriterias()
    {
        return $this->hasMany(SubCriteria::class, 'kriteria_id', 'kriteria_id');
    }
}