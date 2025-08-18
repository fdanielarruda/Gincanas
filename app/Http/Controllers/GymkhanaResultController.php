<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GymkhanaResults\ResultStoreRequest;
use App\Http\Requests\GymkhanaResults\ResultUpdateRequest;
use App\Services\GymkhanaResultService;
use Inertia\Inertia;

class GymkhanaResultController extends Controller
{
    public function __construct(
        protected GymkhanaResultService $service
    ) {}

    public function index()
    {
        $results = $this->service->list();

        return Inertia::render('Results/Index', [
            'results' => $results
        ]);
    }

    public function create()
    {
        $gymkhanas = $this->service->getActiveGimkhanas();

        return Inertia::render('Results/CreateResult', [
            'gymkhanas' => $gymkhanas,
        ]);
    }

    public function store(ResultStoreRequest $request)
    {
        $this->service->createResult($request->validated());

        return redirect()->route('results.index')
            ->with('success', 'Resultados da Gincana iniciados com sucesso!');
    }

    public function manager(int $id)
    {
        $data = $this->service->getResultToManager($id);

        return Inertia::render('Results/ManagerResult', $data);
    }

    public function update(ResultUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->back()->with('success', 'Resultados atualizados com sucesso!');
    }

    public function ranking(int $id)
    {
        $data = $this->service->makeRanking($id);

        return Inertia::render('Results/Ranking', $data);
    }

    public function destroy(int $id)
    {
        $this->service->deleteResult($id);

        return redirect()->route('results.index')
            ->with('success', 'Resultado deletado com sucesso!');
    }
}
