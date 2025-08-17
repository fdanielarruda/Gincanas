<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\GymkhanaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JudgeController extends Controller
{
    public function __construct(
        protected GymkhanaService $gymkhanaService
    ) {}

    public function index(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);

        return Inertia::render('Gymkhana/Judges/Index', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function create(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);
        $availableJudges = $this->gymkhanaService->getAvailableJudges($gymkhana_id);

        return Inertia::render('Gymkhana/Judges/AddJudge', [
            'gymkhana' => $gymkhana,
            'availableJudges' => $availableJudges
        ]);
    }

    public function store(Request $request, int $gymkhana_id)
    {
        $request->validate([
            'judge_ids' => ['required', 'array'],
            'judge_ids.*' => ['required', 'integer', 'exists:users,id']
        ]);

        $this->gymkhanaService->associateJudges($gymkhana_id, $request->get('judge_ids'));

        return redirect()->route('gymkhana.judges.index', $gymkhana_id)
            ->with('success', 'Juízes associados com sucesso!');
    }

    public function destroy(int $gymkhana_id, int $judge_id)
    {
        $this->gymkhanaService->removeJudge($gymkhana_id, $judge_id);

        return redirect()->route('gymkhana.judges.index', $gymkhana_id)
            ->with('success', 'Juiz removido com sucesso!');
    }
}
