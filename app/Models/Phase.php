<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'gymkhana_id',
        'title',
        'description',
        'criteria'
    ];

    protected $casts = [
        'criteria' => 'array'
    ];

    public function gymkhana()
    {
        return $this->hasMany(Gymkhana::class);
    }
}
