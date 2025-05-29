<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $primaryKey = 'kriteria_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'type',
        'weight',
    ];

    public function subCriterias()
    {
        return $this->hasMany(SubCriteria::class, 'kriteria_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'kriteria_id');
    }

    public function calculations()
    {
        return $this->hasMany(Calculation::class, 'kriteria_id');
    }
}