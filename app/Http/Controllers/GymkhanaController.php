<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gymkhanas\GymkhanaStoreRequest;
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
}
