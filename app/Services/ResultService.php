<?php

namespace App\Services;

use App\Models\Gymkhana;

class ResultService
{
    public function listStartedGymkhana()
    {
        return Gymkhana::where('is_active', true)
            ->get();
    }
}
