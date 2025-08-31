<script setup lang="ts">
import { defineProps } from 'vue';
import { Phase } from '@/types';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Judge {
    id: number;
    name: string;
}

interface ResultData {
    [teamId: number]: {
        [phaseId: number]: any;
    };
}

const props = defineProps<{
    phase: Phase;
    judges: Judge[];
    teams: Team[];
    results: ResultData | null;
}>();

const getJudgeScore = (teamId: number, judgeId: number, criteriaIndex: number) => {
    const teamPhaseResults = props.results?.[teamId]?.[props.phase.id];
    if (teamPhaseResults && teamPhaseResults[judgeId]) {
        const score = parseFloat(teamPhaseResults[judgeId][criteriaIndex]);
        return isNaN(score) ? 0 : score;
    }
    return 0;
};

const getCriteriaTotalByJudge = (judgeId: number, criteriaIndex: number) => {
    let total = 0;
    props.teams.forEach(team => {
        total += getJudgeScore(team.id, judgeId, criteriaIndex);
    });
    return total;
};

const getJudgeTotal = (judgeId: number) => {
    let total = 0;
    if (props.phase.criteria) {
        props.phase.criteria.forEach((_, criteriaIndex) => {
            total += getCriteriaTotalByJudge(judgeId, criteriaIndex);
        });
    }
    return total;
};

const getTeamTotalByJudge = (teamId: number, judgeId: number) => {
    let total = 0;
    if (props.phase.criteria) {
        props.phase.criteria.forEach((_, criteriaIndex) => {
            total += getJudgeScore(teamId, judgeId, criteriaIndex);
        });
    }
    return total;
};

</script>

<template>
    <div class="overflow-x-auto mt-4">
        <div v-if="props.teams.length > 0 && props.phase.criteria">
            <div v-for="judge in props.judges" :key="judge.id"
                class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
                <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 pl-1 pb-2">{{ judge.name }}</h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="py-3 px-6">Critério</th>
                                <th v-for="team in props.teams" :key="team.id" scope="col"
                                    class="py-3 px-6 text-center">
                                    {{ team.title }}
                                </th>
                                <th scope="col" class="py-3 px-6 text-center font-bold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(criteria, criteriaIndex) in props.phase.criteria" :key="criteriaIndex"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6 font-medium text-gray-900 whitespace-normal dark:text-white">
                                    {{ criteria }}
                                </td>
                                <td v-for="team in props.teams" :key="team.id" class="py-4 px-6 text-center">
                                    <span class="font-bold">
                                        {{ getJudgeScore(team.id, judge.id, criteriaIndex) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-black dark:text-white">
                                    {{ getCriteriaTotalByJudge(judge.id, criteriaIndex) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-200 dark:bg-gray-700 font-semibold text-gray-900 dark:text-gray-100">
                            <tr>
                                <td class="py-3 px-6">Total do Juiz</td>
                                <td v-for="team in props.teams" :key="team.id"
                                    class="py-3 px-6 text-center text-black dark:text-white font-bold">
                                    {{ getTeamTotalByJudge(team.id, judge.id) }}
                                </td>
                                <td class="py-3 px-6 text-center text-black dark:text-white font-bold">
                                    {{ getJudgeTotal(judge.id) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            <p>Nenhuma equipe encontrada para exibir resultados.</p>
        </div>
    </div>
</template>