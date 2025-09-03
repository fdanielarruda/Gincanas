<script setup lang="ts">
import { defineProps, computed } from 'vue';
import { Phase, Team, Judge, ResultData } from '@/types';

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    results: ResultData | null;
    judges: Judge[];
}>();

const getJudgeColocationPoints = (teamId: number, judgeId: number) => {
    const judgeResult = props.results?.[teamId]?.[props.phase.id]?.[judgeId];
    return judgeResult !== undefined ? judgeResult : null;
};

const getPlaceByPoints = (points: number | null) => {
    if (points === null) {
        return 'N/A';
    }
    const colocation = props.phase.colocations?.find(c => c.points === points);
    return colocation ? colocation.place : 'N/A';
};

const getPlacePoints = (place: string | null) => {
    if (place === null) {
        return 'N/A';
    }
    const colocation = props.phase.colocations?.find(c => c.place === place);
    return colocation ? colocation.points : 'N/A';
};
</script>

<template>
    <div class="overflow-x-auto mt-4">
        <div v-if="props.teams.length > 0 && props.judges.length > 0">
            <div v-for="judge in props.judges" :key="judge.id"
                class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 pl-1 pb-2">{{ judge.name }}</h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="py-3 px-6">Equipe</th>
                                <th scope="col" class="py-3 px-6 text-center">Colocação</th>
                                <th scope="col" class="py-3 px-6 text-center">Pontos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="team in props.teams" :key="team.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ team.title }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold">
                                    {{ getPlaceByPoints(getJudgeColocationPoints(team.id, judge.id)) }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold">
                                    {{ getJudgeColocationPoints(team.id, judge.id) !== null ?
                                    getJudgeColocationPoints(team.id, judge.id) : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            <p>Nenhuma equipe ou juiz encontrado para exibir resultados.</p>
        </div>
    </div>
</template>