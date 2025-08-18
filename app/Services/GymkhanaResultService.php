<?php

namespace App\Services;

use App\Models\Gymkhana;
use App\Models\GymkhanaResult;

class GymkhanaResultService
{
    public function list()
    {
        return GymkhanaResult::with('gymkhana')->get();
    }

    public function getActiveGimkhanas()
    {
        return Gymkhana::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);
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

    public function deleteResult(int $id)
    {
        $result = GymkhanaResult::findOrFail($id);
        $result->delete();
    }
}
