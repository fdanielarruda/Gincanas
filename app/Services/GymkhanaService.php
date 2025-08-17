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

    public function update(int $id, array $data): Gymkhana
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $gymkhana->update($data);

        return $gymkhana;
    }

    public function delete(int $id)
    {
        $gymkhana = Gymkhana::findOrFail($id);
        $gymkhana->delete();
    }

    public function createTeam(int $gymkhana_id, array $data): Team
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);

        $team = $gymkhana->teams()->create($data);

        return $team;
    }

    public function prepareForEditTeam(int $gymkhana_id, int $team_id): array
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $team = $gymkhana->teams()->findOrFail($team_id);

        return [
            'gymkhana' => $gymkhana,
            'team' => $team
        ];
    }

    public function updateTeam(int $gymkhana_id, int $team_id, array $data): Team
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $team = $gymkhana->teams()->findOrFail($team_id);
        $team->update($data);

        return $team;
    }

    public function deleteTeam(int $gymkhana_id, int $team_id)
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $team = $gymkhana->teams()->findOrFail($team_id);
        $team->delete();
    }
}
