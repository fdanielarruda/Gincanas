<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    const TYPE_CRITERIA = 1;
    const TYPE_QUIZ = 2;
    const TYPE_COLOCATION = 3;

    const TYPES = [
        self::TYPE_CRITERIA,
        self::TYPE_QUIZ,
        self::TYPE_COLOCATION
    ];

    protected $fillable = [
        'gymkhana_id',
        'title',
        'description',
        'type',
        'criteria',
        'colocations'
    ];

    protected $casts = [
        'criteria' => 'array',
        'colocations' => 'array'
    ];

    public function gymkhana()
    {
        return $this->hasMany(Gymkhana::class);
    }
}
