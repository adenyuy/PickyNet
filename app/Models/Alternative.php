<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;

    protected $primaryKey = 'alternative_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'image_path',
    ];

    public function scores()
    {
        return $this->hasMany(Score::class, 'alternative_id');
    }

    public function calculations()
    {
        return $this->hasMany(Calculation::class, 'alternative_id');
    }
}