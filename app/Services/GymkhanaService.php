<?php

namespace App\Services;

use App\Models\Gymkhana;
use App\Models\Team;

class GymkhanaService
{
    public function list()
    {
        return Gymkhana::all();
    }

    public function find(int $id): Gymkhana
    {
        return Gymkhana::with(['teams'])
            ->findOrFail($id);
    }

    public function create(array $data): Gymkhana
    {
        return Gymkhana::create($data);
    }

    public function createTeam(int $id, array $data): Team
    {
        $gymkhana = Gymkhana::findOrFail($id);

        $team = $gymkhana->teams()->create($data);

        return $team;
    }

    public function update(int $id, array $data): Gymkhana
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $gymkhana->update($data);

        return $gymkhana;
    }

    public function updateTeam(int $id, int $team_id, array $data): Team
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $team = $gymkhana->teams()->findOrFail($team_id);
        $team->update($data);

        return $team;
    }

    public function delete(int $id)
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $gymkhana->delete();
    }

    public function deleteTeam(int $id, int $team_id)
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $team = $gymkhana->teams()->findOrFail($team_id);
        $team->delete();
    }

    public function prepareForEdit(int $id, int $team_id): array
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $team = $gymkhana->teams()->findOrFail($team_id);

        return [
            'gymkhana' => $gymkhana,
            'team' => $team
        ];
    }
}
