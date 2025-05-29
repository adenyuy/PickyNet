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
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'kriteria_id', 'kriteria_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'subkriteria_id');
    }
}