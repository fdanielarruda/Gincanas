<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Phases\PhaseStoreRequest;
use App\Services\GymkhanaService;
use Inertia\Inertia;

class PhaseController extends Controller
{
    public function __construct(
        protected GymkhanaService $gymkhanaService
    ) {}

    public function index(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);

        return Inertia::render('Gymkhana/Phases/Index', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function create(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);

        return Inertia::render('Gymkhana/Phases/CreatePhase', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function store(PhaseStoreRequest $request, int $gymkhana_id)
    {
        $this->gymkhanaService->createPhase($gymkhana_id, $request->validated());

        return redirect()->route('gymkhana.phases.index', $gymkhana_id)
            ->with('success', 'Fase cadastrada com sucesso!');
    }

    public function edit(int $gymkhana_id, int $phase_id)
    {
        $data = $this->gymkhanaService->prepareForEditPhase($gymkhana_id, $phase_id);

        return Inertia::render('Gymkhana/Phases/EditPhase', $data);
    }

    public function update(PhaseStoreRequest $request, int $gymkhana_id, int $phase_id)
    {
        $this->gymkhanaService->updatePhase($gymkhana_id, $phase_id, $request->validated());

        return redirect()->route('gymkhana.phases.index', $gymkhana_id)
            ->with('success', 'Fase atualizada com sucesso!');
    }

    public function destroy(int $gymkhana_id, int $phase_id)
    {
        $this->gymkhanaService->deletePhase($gymkhana_id, $phase_id);

        return redirect()->route('gymkhana.phases.index', $gymkhana_id)
            ->with('success', 'Fase deletada com sucesso!');
    }
}
