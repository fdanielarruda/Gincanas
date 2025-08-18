<script setup lang="ts">
import { defineProps } from 'vue';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Phase {
    id: number;
    title: string;
    criteria: string[] | null;
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
        // Usa `parseFloat` para garantir que o valor é um número
        const score = parseFloat(teamPhaseResults[judgeId][criteriaIndex]);
        return isNaN(score) ? 0 : score;
    }
    return 0;
};

const getCriteriaTotal = (teamId: number, criteriaIndex: number) => {
    let total = 0;
    // Itera sobre todos os jurados e soma a nota do critério
    props.judges.forEach(judge => {
        total += getJudgeScore(teamId, judge.id, criteriaIndex);
    });
    return total;
};

const getTeamTotal = (teamId: number) => {
    let total = 0;
    if (props.phase.criteria) {
        // Itera sobre cada critério e usa a função de totalização do critério
        props.phase.criteria.forEach((_, criteriaIndex) => {
            total += getCriteriaTotal(teamId, criteriaIndex);
        });
    }
    return total;
};

</script>

<template>
    <div class="overflow-x-auto mt-6">
        <div v-if="props.teams.length > 0 && props.phase.criteria">
            <div v-for="team in props.teams" :key="team.id"
                 class="p-4 rounded-lg border mb-6">
                <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ team.title }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Participantes: {{ team.participants.join(', ')
                    }}</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="py-3 px-6">Critério</th>
                                <th v-for="judge in props.judges" :key="judge.id" scope="col"
                                    class="py-3 px-6 text-center">
                                    {{ judge.name }}
                                </th>
                                <th scope="col" class="py-3 px-6 text-center font-bold">Total por Critério</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(criteria, criteriaIndex) in props.phase.criteria" :key="criteriaIndex"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ criteria }}
                                </td>
                                <td v-for="judge in props.judges" :key="judge.id" class="py-4 px-6 text-center">
                                    <span class="font-bold">
                                        {{ getJudgeScore(team.id, judge.id, criteriaIndex) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-black dark:text-black">
                                    {{ getCriteriaTotal(team.id, criteriaIndex) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-200 dark:bg-gray-700 font-semibold text-gray-900 dark:text-gray-100">
                            <tr>
                                <th scope="row" class="py-3 px-6">Total da Equipe</th>
                                <td :colspan="props.judges.length" class="py-3 px-6 text-center"></td>
                                <td class="py-3 px-6 text-center text-black dark:text-black font-bold">
                                    {{ getTeamTotal(team.id) }}
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