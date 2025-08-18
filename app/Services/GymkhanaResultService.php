<?php

namespace App\Services;

use App\Models\Gymkhana;
use App\Models\GymkhanaResult;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GymkhanaResultService
{
    public function list()
    {
        return GymkhanaResult::with('gymkhana')
            ->get();
    }

    public function find(int $id)
    {
        return GymkhanaResult::with('gymkhana')
            ->findOrFail($id);
    }

    public function getActiveGimkhanas()
    {
        return Gymkhana::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function getResultToManager(int $id)
    {
        $result = GymkhanaResult::findOrFail($id);
        $user = Auth::user();

        $allPhases = collect($result->phases);
        $filteredPhases = collect([]);

        if ($user && $user->type == User::TYPE_JUDGE) {
            $filteredPhases = $allPhases->filter(fn($phase) => $phase['type'] === 1);
        }

        if ($user && $user->type == User::TYPE_ADMIN) {
            // Agora, para administradores, você precisa de todas as fases.
            // O filtro original estava removendo a fase 1, o que não é o comportamento desejado.
            // Um administrador deve ver todas as fases.
            $filteredPhases = $allPhases;
        }

        // Adicione a lista de juízes e o tipo de usuário no retorno
        return [
            'id' => $result->id,
            'teams' => $result->teams,
            'phases' => $filteredPhases->values()->toArray(),
            'results' => $result->results,
            'user_id' => $user->id,
            'user_type' => $user->type,
            'judges' => $result->judges, // Relacionamento com os juízes da gincana
        ];
    }

    public function createResult(array $data)
    {
        $gymkhana = Gymkhana::with(['judges', 'teams', 'phases'])
            ->findOrFail($data['gymkhana_id']);

        if ($gymkhana->judges->isEmpty()) {
            return back()->with('error', 'A gincana deve ter pelo menos um juiz cadastrado.');
        }

        if ($gymkhana->teams->isEmpty()) {
            return back()->with('error', 'A gincana deve ter pelo menos uma equipe cadastrada.');
        }

        if ($gymkhana->phases->isEmpty()) {
            return back()->with('error', 'A gincana deve ter pelo menos uma fase cadastrada.');
        }

        $gymkhanaResult = new GymkhanaResult();
        $gymkhanaResult->gymkhana_id = $gymkhana->id;
        $gymkhanaResult->teams = $gymkhana->teams->toArray();
        $gymkhanaResult->phases = $gymkhana->phases->toArray();
        $gymkhanaResult->results = null;
        $gymkhanaResult->save();

        $gymkhanaResult->judges()->attach($gymkhana->judges->pluck('id'));
    }

    public function update(int $id, array $data)
    {
        $gymkhanaResult = GymkhanaResult::findOrFail($id);

        $user = Auth::user();
        $userType = $user->type;

        $newResults = $data['results'];

        $currentResults = $gymkhanaResult->results ?? [];

        foreach ($newResults as $teamId => $teamResults) {
            foreach ($teamResults as $phaseId => $phaseResults) {
                $phase = collect($gymkhanaResult->phases)->firstWhere('id', $phaseId);

                if (!$phase) {
                    continue;
                }

                if ($userType === User::TYPE_JUDGE && $phase['type'] !== 1) {
                    continue;
                }

                if ($userType === User::TYPE_ADMIN) {
                    $currentResults[$teamId][$phaseId] = $phaseResults;
                    continue;
                }

                if ($userType === User::TYPE_JUDGE && $phase['type'] === 1) {
                    $judgeId = $user->id;
                    $currentResults[$teamId][$phaseId][$judgeId] = $phaseResults[$judgeId];
                }
            }
        }

        $gymkhanaResult->results = $currentResults;
        $gymkhanaResult->save();
    }

    public function deleteResult(int $id)
    {
        $result = GymkhanaResult::findOrFail($id);
        $result->delete();
    }
}
