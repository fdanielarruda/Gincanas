<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import TextButton from '@/Components/Itens/TextButton.vue';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Phase {
    id: number;
    title: string;
    type: number;
    criteria: string[] | null;
    description: string;
}

interface ResultData {
    [teamId: number]: {
        [phaseId: number]: any;
    };
}

const props = defineProps<{
    id: number;
    gincana: {
        id: number;
        title: string;
        description: string;
    };
    teams: Team[];
    phases: Phase[];
    results: ResultData;
}>();

const TYPE_CRITERIA = 1;
const TYPE_CHECKLIST = 4;

const calculateTeamTotalScore = (teamId: number) => {
    let total = 0;
    const teamResults = props.results[teamId];

    if (teamResults) {
        for (const phaseId in teamResults) {
            const phase = props.phases.find(p => p.id === Number(phaseId));
            if (!phase) continue;

            const phaseResult = teamResults[phaseId];

            if (phase.type === TYPE_CRITERIA) {
                if (typeof phaseResult === 'object' && phaseResult !== null) {
                    for (const judgeId in phaseResult) {
                        const judgeScores = phaseResult[judgeId];
                        if (Array.isArray(judgeScores)) {
                            for (const score of judgeScores) {
                                total += Number(score) || 0;
                            }
                        }
                    }
                }
            } else if (phase.type === TYPE_CHECKLIST && phase.colocations) {
                if (typeof phaseResult === 'object' && phaseResult !== null) {
                    for (const colocationPlace in phaseResult) {
                        const colocation = phase.colocations.find(c => c.place === colocationPlace);
                        if (colocation) {
                            const quantity = Number(phaseResult[colocationPlace]) || 0;
                            total += quantity * colocation.points;
                        }
                    }
                }
            } else {
                total += Number(phaseResult) || 0;
            }
        }
    }
    return total;
};

const rankedTeams = computed(() => {
    const teamsWithScores = props.teams.map(team => ({
        ...team,
        totalScore: calculateTeamTotalScore(team.id),
    }));

    // Ordena as equipes por pontuação total em ordem decrescente
    return teamsWithScores.sort((a, b) => b.totalScore - a.totalScore);
});

</script>

<template>

    <Head :title="`Classificação - ${props.gincana.title}`" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <TextButton :href="route('results.manager', props.id)" class="p-4">
                                Resultados
                            </TextButton>
                        </div>

                        <h2 class="text-xl font-bold mb-4">Classificação Geral - {{ props.gincana.title }}</h2>
                        <div v-if="rankedTeams.length > 0">
                            <table class="w-full text-left table-auto">
                                <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="py-2 px-4 font-bold">Posição</th>
                                        <th class="py-2 px-4 font-bold">Equipe</th>
                                        <th class="py-2 px-4 font-bold text-right">Pontuação Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(team, index) in rankedTeams" :key="team.id"
                                        :class="{ 'bg-gray-50 dark:bg-gray-700': index % 2 === 0, 'bg-white dark:bg-gray-800': index % 2 !== 0 }">
                                        <td class="py-2 px-4">{{ index + 1 }}º</td>
                                        <td class="py-2 px-4">{{ team.title }}</td>
                                        <td class="py-2 px-4 text-right font-bold">{{ team.totalScore }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center text-gray-500 dark:text-gray-400">
                            <p>Ainda não há resultados para gerar a classificação.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>