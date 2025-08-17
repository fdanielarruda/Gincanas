<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gymkhana extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date'
    ];

    protected $casts = [
        'start_date' => 'date'
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
