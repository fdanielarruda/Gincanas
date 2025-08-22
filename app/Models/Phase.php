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
    const TYPE_CHECKLIST = 4;

    const TYPES = [
        self::TYPE_CRITERIA,
        self::TYPE_QUIZ,
        self::TYPE_COLOCATION,
        self::TYPE_CHECKLIST
    ];

    protected $fillable = [
        'gymkhana_id',
        'title',
        'description',
        'type',
        'order',
        'criteria',
        'colocations',
        'checklist_colocations'
    ];

    protected $casts = [
        'criteria' => 'array',
        'colocations' => 'array',
        'checklist_colocations' => 'array'
    ];

    public function gymkhana()
    {
        return $this->hasMany(Gymkhana::class);
    }
}
