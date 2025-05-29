<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $primaryKey = 'calculation_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'alternative_id',
        'kriteria_id',
        'score_raw',
        'score_normalized',
        'qi_add',
        'qi_multi',
        'final_qi',
    ];

    protected $casts = [
        'score_raw' => 'decimal:2',
        'score_normalized' => 'decimal:4',
        'qi_add' => 'decimal:4',
        'qi_multi' => 'decimal:4',
        'final_qi' => 'decimal:4',
    ];

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
}