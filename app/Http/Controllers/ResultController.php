<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ResultService;
use Inertia\Inertia;

class ResultController extends Controller
{
    public function __construct(
        protected ResultService $service
    ) {}

    public function index()
    {
        $gymkhanas = $this->service->listStartedGymkhana();

        return Inertia::render('Results/Index', [
            'gymkhanas' => $gymkhanas
        ]);
    }
}
