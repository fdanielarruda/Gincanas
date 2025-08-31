<?php

namespace App\Services;

use App\Models\Gymkhana;
use App\Models\Team;
use App\Models\Phase;
use App\Models\User;

class GymkhanaService
{
    public function list()
    {
        return Gymkhana::all();
    }

    public function find(int $id): Gymkhana
    {
        return Gymkhana::with(['teams', 'phases', 'judges'])
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

        $maxOrder = $gymkhana->phases()->max('order');
        $data['order'] = ($maxOrder) ? $maxOrder + 1 : 1;

        if (isset($data['type'])) {
            if ((int) $data['type'] === Phase::TYPE_CRITERIA) {
                unset($data['colocations']);
                unset($data['checklist_colocations']);
            }
            if ((int) $data['type'] === Phase::TYPE_COLOCATION) {
                unset($data['criteria']);
                unset($data['checklist_colocations']);
            }
            if ((int) $data['type'] === Phase::TYPE_CHECKLIST) {
                unset($data['criteria']);
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
            if ((int) $data['type'] === Phase::TYPE_CRITERIA) {
                $data['colocations'] = null;
                $data['checklist_colocations'] = null;
            }
            if ((int) $data['type'] === Phase::TYPE_COLOCATION) {
                $data['criteria'] = null;
                $data['checklist_colocations'] = null;
            }
            if ((int) $data['type'] === Phase::TYPE_CHECKLIST) {
                $data['criteria'] = null;
            }
        }

        $phase->update($data);

        return $phase;
    }

    public function reorderPhase(int $gymkhana_id, int $phase_id, string $direction)
    {
        $gymkhana = Gymkhana::with('phases')->findOrFail($gymkhana_id);
        $phaseToReorder = $gymkhana->phases()->findOrFail($phase_id);

        $this->ensurePhasesHaveOrder($gymkhana);

        $gymkhana = Gymkhana::with(['phases' => function ($query) {
            $query->orderBy('order');
        }])->findOrFail($gymkhana_id);

        $phases = $gymkhana->phases;
        $currentIndex = $phases->search(function ($phase) use ($phase_id) {
            return $phase->id === $phase_id;
        });

        if ($currentIndex === false) {
            throw new \Exception('Prova não encontrada na lista ordenada.');
        }

        $newIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if ($newIndex < 0 || $newIndex >= $phases->count()) {
            return;
        }

        $phaseToSwap = $phases[$newIndex];

        $currentOrder = $phaseToReorder->order;
        $phaseToReorder->order = $phaseToSwap->order;
        $phaseToSwap->order = $currentOrder;

        $phaseToReorder->save();
        $phaseToSwap->save();
    }

    public function deletePhase(int $gymkhana_id, int $phase_id)
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $phase = $gymkhana->phases()->findOrFail($phase_id);
        $phase->delete();
    }

    public function getAvailableJudges(int $gymkhana_id): array
    {
        $judges = User::where('type', User::TYPE_JUDGE)
            ->where('is_active', true)
            ->get();

        $assignedJudgeIds = Gymkhana::findOrFail($gymkhana_id)->judges()->pluck('users.id')->toArray();

        $availableJudges = $judges->reject(function ($judge) use ($assignedJudgeIds) {
            return in_array($judge->id, $assignedJudgeIds);
        });

        return $availableJudges->map(function ($judge) {
            return [
                'id' => $judge->id,
                'name' => $judge->name,
            ];
        })->values()->all();
    }

    public function associateJudges(int $gymkhana_id, array $judgeIds)
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $gymkhana->judges()->attach($judgeIds);
    }

    public function removeJudge(int $gymkhana_id, int $judgeId)
    {
        $gymkhana = Gymkhana::findOrFail($gymkhana_id);
        $gymkhana->judges()->detach($judgeId);
    }

    private function ensurePhasesHaveOrder(Gymkhana $gymkhana)
    {
        $phases = $gymkhana->phases()->orderBy('order')->get();

        $hasNullOrder = $phases->contains(fn($phase) => $phase->order === null);
        $ordersAreConsecutive = $phases->pluck('order')->sort()->values()->toArray() === range(1, $phases->count());

        if (!$hasNullOrder && $ordersAreConsecutive) {
            return;
        }

        foreach ($phases as $index => $phase) {
            if ($phase->order !== $index + 1) {
                $phase->order = $index + 1;
                $phase->save();
            }
        }
    }
}
