<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gymkhana extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function phases()
    {
        return $this->hasMany(Phase::class)->orderBy('order');
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'gymkhana_judges', 'gymkhana_id', 'judge_id')
            ->orderBy('name');
    }
}
