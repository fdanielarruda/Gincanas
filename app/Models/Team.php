<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'gymkhana_id',
        'title',
        'participants'
    ];

    protected $casts = [
        'participants' => 'array'
    ];

    public function gymkhana()
    {
        return $this->belongsTo(Gymkhana::class);
    }
}
