<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gymkhanas\GymkhanaStoreRequest;
use App\Http\Requests\Teams\TeamStoreRequest;
use App\Services\GymkhanaService;
use Inertia\Inertia;

class GymkhanaController extends Controller
{
    public function __construct(
        protected GymkhanaService $service
    ) {}

    public function index()
    {
        $gymkhanas = $this->service->list();

        return Inertia::render('Gymkhana/Index', [
            'gymkhanas' => $gymkhanas
        ]);
    }

    public function create()
    {
        return Inertia::render('Gymkhana/CreateGymkhana');
    }

    public function store(GymkhanaStoreRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('gymkhana.index')
            ->with('success', 'Gincana cadastrada com sucesso!');
    }

    public function edit(int $id)
    {
        $gymkhana = $this->service->find($id);

        return Inertia::render('Gymkhana/EditGymkhana', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function update(GymkhanaStoreRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('gymkhana.index')
            ->with('success', 'Gincana atualizada com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('gymkhana.index')
            ->with('success', 'Gincana deletada com sucesso!');
    }

    public function teams(int $id)
    {
        $gymkhana = $this->service->find($id);

        return Inertia::render('Gymkhana/Teams/Index', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function createTeam(int $id)
    {
        $gymkhana = $this->service->find($id);

        return Inertia::render('Gymkhana/Teams/CreateTeam', [
            'gymkhana' => $gymkhana
        ]);
    }

    public function storeTeam(TeamStoreRequest $request, int $id)
    {
        $this->service->createTeam($id, $request->validated());

        return redirect()->route('gymkhana.teams', $id)
            ->with('success', 'Equipe cadastrada com sucesso!');
    }

    public function editTeam(int $id, int $team_id)
    {
        $data = $this->service->prepareForEdit($id, $team_id);

        return Inertia::render('Gymkhana/Teams/EditTeam', $data);
    }

    public function updateTeam(TeamStoreRequest $request, int $id, int $team_id)
    {
        $this->service->updateTeam($id, $team_id, $request->validated());

        return redirect()->route('gymkhana.teams', $id)
            ->with('success', 'Equipe atualizada com sucesso!');
    }

    public function destroyTeam(int $id, int $team_id)
    {
        $this->service->deleteTeam($id, $team_id);

        return redirect()->route('gymkhana.teams', $id)
            ->with('success', 'Equipe deletada com sucesso!');
    }
}
