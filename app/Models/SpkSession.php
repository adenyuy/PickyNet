<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Menggunakan trait Laravel untuk PK UUID 'id'
// atau gunakan boot() method jika PK bukan 'id' atau preferensi Anda

class SpkSession extends Model
{
    use HasFactory, HasUuids; // Jika PK 'id' adalah UUID

    // Jika PK bukan 'id' tapi UUID, misal 'spk_session_id':
    // protected $primaryKey = 'spk_session_id';
    // public $incrementing = false;
    // protected $keyType = 'string';
    // protected static function boot() { /* ... seperti di SubCriteria ... */ }


    protected $fillable = [
        'user_id',
        'session_name',
        'selected_alternatives',
        'criteria_ranking_and_weights',
        'user_scores',
        'normalized_matrix',
        'q1_values',
        'q2_values',
        'final_qi_ranking',
    ];

    protected $casts = [
        'selected_alternatives' => 'array',
        'criteria_ranking_and_weights' => 'array',
        'user_scores' => 'array',
        'normalized_matrix' => 'array',
        'q1_values' => 'array',
        'q2_values' => 'array',
        'final_qi_ranking' => 'array',
    ];

    public function user()
    {
        // Foreign key di spk_sessions adalah 'user_id'
        // Owner key (PK) di users adalah 'user_id'
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}