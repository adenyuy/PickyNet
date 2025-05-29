<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <-- Tambahkan ini!

class Score extends Model
{
    use HasFactory;

    protected $primaryKey = 'score_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'alternative_id',
        'kriteria_id',
        'subkriteria_id',
        'value',
    ];

    /**
     * Boot method untuk menghasilkan UUID secara otomatis.
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

    public function alternative()
    {
        return $this->belongsTo(Alternative::class, 'alternative_id');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'kriteria_id');
    }

    public function subCriteria()
    {
        return $this->belongsTo(SubCriteria::class, 'subkriteria_id');
    }
}