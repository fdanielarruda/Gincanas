<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\TeamStoreRequest;
use App\Services\GymkhanaService;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function __construct(
        protected GymkhanaService $gymkhanaService
    ) {}

    public function index(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);

        return Inertia::render('Gymkhana/Teams/Index', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function create(int $gymkhana_id)
    {
        $gymkhana = $this->gymkhanaService->find($gymkhana_id);

        return Inertia::render('Gymkhana/Teams/CreateTeam', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function store(TeamStoreRequest $request, int $gymkhana_id)
    {
        $this->gymkhanaService->createTeam($gymkhana_id, $request->validated());

        return redirect()->route('gymkhana.teams.index', $gymkhana_id)
            ->with('success', 'Equipe cadastrada com sucesso!');
    }

    public function edit(int $gymkhana_id, int $team_id)
    {
        $data = $this->gymkhanaService->prepareForEditTeam($gymkhana_id, $team_id);

        return Inertia::render('Gymkhana/Teams/EditTeam', $data);
    }

    public function update(TeamStoreRequest $request, int $gymkhana_id, int $team_id)
    {
        $this->gymkhanaService->updateTeam($gymkhana_id, $team_id, $request->validated());

        return redirect()->route('gymkhana.teams.index', $gymkhana_id)
            ->with('success', 'Equipe atualizada com sucesso!');
    }

    public function destroy(int $gymkhana_id, int $team_id)
    {
        $this->gymkhanaService->deleteTeam($gymkhana_id, $team_id);

        return redirect()->route('gymkhana.teams.index', $gymkhana_id)
            ->with('success', 'Equipe deletada com sucesso!');
    }
}
