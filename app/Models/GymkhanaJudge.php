<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymkhanaJudge extends Model
{
    use HasFactory;

    protected $fillable = [
        'gymkhana_id',
        'judge_id'
    ];

    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class);
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }
}
