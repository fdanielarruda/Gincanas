<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { defineProps, computed } from 'vue';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Colocation {
    place: string;
    points: number;
}

interface Phase {
    id: number;
    title: string;
    type: number;
    criteria: string[] | null;
    colocations: Colocation[] | null;
    checklist_colocations: Colocation[] | null;
    description: string;
}

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    form: ReturnType<typeof useForm>;
}>();

const calculateTeamPhaseScore = (teamId: number): number => {
    let total = 0;
    if (props.phase.checklist_colocations) {
        props.phase.checklist_colocations.forEach(colocation => {
            const quantity = props.form.results[teamId]?.[props.phase.id]?.[colocation.place] || 0;
            total += Number(quantity) * colocation.points;
        });
    }
    return total;
};

const rankedTeams = computed(() => {
    const teamsWithScores = props.teams.map(team => ({
        ...team,
        score: calculateTeamPhaseScore(team.id),
    }));

    const allScoresAreZero = teamsWithScores.every(team => team.score === 0);

    if (allScoresAreZero) {
        return teamsWithScores.map(team => ({
            ...team,
            championshipPoints: 0,
        }));
    }

    const sortedTeams = teamsWithScores.sort((a, b) => b.score - a.score);

    return sortedTeams.map((team, index) => {
        const championshipPoints = props.phase.colocations?.[index]?.points || 0;
        return {
            ...team,
            championshipPoints: championshipPoints,
        };
    });
});
</script>

<template>
    <div class="space-y-4 mt-4">
        <div v-for="team in teams" :key="team.id"
            class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ team.title }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Participantes: {{ team.participants.join(', ') }}
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Item (pts)
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Quantidade
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total/Item
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="colocation in phase.checklist_colocations" :key="colocation.place">
                            <td class="px-6 py-4 whitespace-normal font-medium text-gray-900 dark:text-gray-100">
                                {{ colocation.place }} ({{ colocation.points }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.results[team.id][phase.id][colocation.place]"
                                    :id="`score-${team.id}-${phase.id}-${colocation.place}`" min="0" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                {{ (form.results[team.id]?.[phase.id]?.[colocation.place] || 0) * colocation.points }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-right">Total da Equipe:</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ calculateTeamPhaseScore(team.id) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
            <h5 class="text-lg font-bold text-left text-gray-900 dark:text-gray-100 mb-2">
                Pontuação das Equipes
            </h5>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                #
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Equipe
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                PTS Prova
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                PTS Geral
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(team, index) in rankedTeams" :key="team.id">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                {{ index + 1 }}º
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                {{ team.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-gray-100">
                                {{ team.score }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-gray-100">
                                {{ team.championshipPoints }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>