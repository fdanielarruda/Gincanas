<script setup lang="ts">
import { defineProps } from 'vue';
import { Phase, Team, Judge, ResultData } from '@/types';

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    judges: Judge[];
    results: ResultData;
}>();

/**
 * Retorna os pontos de colocação atribuídos por um juiz específico para uma equipe.
 * @param teamId O ID da equipe.
 * @param judgeId O ID do juiz.
 * @returns Os pontos de colocação, ou `null` se não houver resultado.
 */
const getJudgeColocationPoints = (teamId: number, judgeId: number) => {
    return props.results?.[teamId]?.[props.phase.id]?.[judgeId] ?? null;
};
</script>

<template>
    <div class="overflow-x-auto mt-4">
        <div class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left table-criteria-details-print">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="py-3 px-6">Equipe</th>
                            <th v-for="judge in props.judges" :key="judge.id" scope="col" class="py-3 px-6 text-center">
                                {{ judge.name }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="team in props.teams" :key="team.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ team.title }}
                            </td>
                            <td v-for="judge in props.judges" :key="judge.id" class="py-4 px-6 text-center">
                                <span class="font-bold">{{ getJudgeColocationPoints(team.id, judge.id) ?? '-' }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>