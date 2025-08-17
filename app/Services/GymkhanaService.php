<?php

namespace App\Services;

use App\Models\Gymkhana;
use App\Models\Team;
use App\Models\Phase;

class GymkhanaService
{
    public function list()
    {
        return Gymkhana::all();
    }

    public function find(int $id): Gymkhana
    {
        return Gymkhana::with(['teams', 'phases'])
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

    public function createPhase(int $gymkhana_id, array $data): Phase
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);

        if (isset($data['type'])) {
            if ((int) $data['type'] !== Phase::TYPE_CRITERIA) {
                unset($data['criteria']);
            }
            if ((int) $data['type'] !== Phase::TYPE_COLOCATION) {
                unset($data['colocations']);
            }
        }

        $phase = $gymkhana->phases()->create($data);

        return $phase;
    }

    public function prepareForEditPhase(int $gymkhana_id, int $phase_id): array
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $phase = $gymkhana->phases()->findOrFail($phase_id);

        return [
            'gymkhana' => $gymkhana,
            'phase' => $phase
        ];
    }

    public function updatePhase(int $gymkhana_id, int $phase_id, array $data): Phase
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $phase = $gymkhana->phases()->findOrFail($phase_id);

        if (isset($data['type'])) {
            if ((int) $data['type'] !== Phase::TYPE_CRITERIA) {
                $data['criteria'] = null;
            }
            if ((int) $data['type'] !== Phase::TYPE_COLOCATION) {
                $data['colocations'] = null;
            }
        }

        $phase->update($data);

        return $phase;
    }

    public function deletePhase(int $gymkhana_id, int $phase_id)
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $phase = $gymkhana->phases()->findOrFail($phase_id);
        $phase->delete();
    }
}
