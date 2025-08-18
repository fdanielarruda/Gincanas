<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymkhanaResultJudge extends Model
{
    use HasFactory;

    protected $fillable = [
        'gymkhana_result_id',
        'judge_id'
    ];
}
