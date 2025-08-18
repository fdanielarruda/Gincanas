<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymkhanaResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'gymkhana_id',
        'teams',
        'phases',
        'results'
    ];

    protected $casts = [
        'teams' => 'array',
        'phases' => 'array',
        'results' => 'array'
    ];

    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class);
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'gymkhana_result_judges', 'gymkhana_result_id', 'judge_id');
    }
}
