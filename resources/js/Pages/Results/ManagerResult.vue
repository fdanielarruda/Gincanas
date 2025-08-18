<script setup lang="ts">
import { reactive, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TableCriteria from './TableCriteria.vue';
import TableColocation from './TableColocation.vue';
import TableChecklist from './TableChecklist.vue';
import TableDefault from './TableDefault.vue';
import TableCriteriaResults from './TableCriteriaResults.vue';

const TYPE_CRITERIA = 1;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4;
const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

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
    description: string;
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
    id: number;
    teams: Team[];
    phases: Phase[];
    results: ResultData | null;
    user_id: number;
    user_type: number;
    judges: Judge[];
}>();

const state = reactive({
    activePhase: props.phases[0] ? props.phases[0].id : null,
});

const initialResults: { [key: number]: any } = {};
const judgeId = props.user_id;

if (props.teams && props.phases) {
    props.teams.forEach(team => {
        initialResults[team.id] = {};
        props.phases.forEach(phase => {
            if (phase.type === TYPE_CRITERIA) {
                if (props.user_type === USER_TYPE_ADMIN) {
                    initialResults[team.id][phase.id] = props.results?.[team.id]?.[phase.id] || {};
                } else {
                    const existingValues = props.results?.[team.id]?.[phase.id]?.[judgeId];
                    initialResults[team.id][phase.id] = { [judgeId]: existingValues || Array(phase.criteria?.length).fill(null) };
                }
            } else if (phase.type === TYPE_CHECKLIST) {
                const initialPhaseResults: { [key: string]: number | null } = {};
                phase.colocations?.forEach(colocation => {
                    const existingValue = (props.results?.[team.id]?.[phase.id] as { [key: string]: number })?.[colocation.place];
                    initialPhaseResults[colocation.place] = existingValue !== undefined ? existingValue : null;
                });
                initialResults[team.id][phase.id] = initialPhaseResults;
            } else {
                const existingValue = props.results?.[team.id]?.[phase.id];
                initialResults[team.id][phase.id] = existingValue !== undefined ? existingValue : null;
            }
        });
    });
}

const calculateTotalScore = (teamId: number) => {
    let total = 0;
    const teamResults = form.results[teamId];

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

const currentPhase = computed(() => {
    return props.phases.find(p => p.id === state.activePhase);
});

const form = useForm({
    results: initialResults,
});

const submit = () => {
    form.put(route('results.update', { id: props.id }), {
        onSuccess: () => {
            console.log('Resultados salvos com sucesso!');
        },
        onError: (errors) => {
            console.log('Erro ao salvar resultados:', errors);
        }
    });
};
</script>

<template>

    <Head title="Gerenciar Resultados" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <TextButton :href="route('results.ranking', props.id)" class="p-4">
                                Ver Classificação
                            </TextButton>
                        </div>

                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Gerenciar Resultados da Gincana
                        </h2>

                        <div v-if="props.phases.length > 0">
                            <div class="flex space-x-2 mb-6">
                                <button v-for="phase in props.phases" :key="phase.id"
                                    @click="state.activePhase = phase.id" :class="[
                                        'px-4 py-2 rounded-md transition-colors duration-200',
                                        state.activePhase === phase.id ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'
                                    ]">
                                    {{ phase.title }}
                                </button>
                            </div>

                            <form @submit.prevent="submit">
                                <div v-if="currentPhase">
                                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">
                                        {{ currentPhase.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        {{ currentPhase.description }}
                                    </p>

                                    <TableCriteria
                                        v-if="currentPhase.type === TYPE_CRITERIA && user_type === USER_TYPE_JUDGE"
                                        :phase="currentPhase" :teams="props.teams" :form="form" :user_id="props.user_id"
                                        :user_type="props.user_type" :judges="props.judges" />

                                    <TableCriteriaResults
                                        v-else-if="currentPhase.type === TYPE_CRITERIA && user_type === USER_TYPE_ADMIN"
                                        :phase="currentPhase" :teams="props.teams" :results="results"
                                        :judges="props.judges" />

                                    <TableColocation v-else-if="currentPhase.type === TYPE_COLOCATION"
                                        :phase="currentPhase" :teams="props.teams" :form="form" />

                                    <TableChecklist v-else-if="currentPhase.type === TYPE_CHECKLIST"
                                        :phase="currentPhase" :teams="props.teams" :form="form" />

                                    <TableDefault v-else :phase="currentPhase" :teams="props.teams" :form="form"
                                        :calculateTotalScore="calculateTotalScore" />
                                </div>

                                <div class="flex items-center justify-end mt-6 space-x-4">
                                    <TextButton :disabled="form.processing" class="p-4">
                                        Salvar Resultados
                                    </TextButton>
                                </div>
                            </form>
                        </div>

                        <div v-else class="text-center text-gray-500 dark:text-gray-400">
                            <p>Esta gincana não possui fases ou equipes cadastradas para gerenciamento de resultados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>