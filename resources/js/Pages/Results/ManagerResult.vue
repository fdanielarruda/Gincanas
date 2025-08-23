<script setup lang="ts">
import { reactive, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TableCriteria from './TableCriteria.vue';
import TableColocation from './TableColocation.vue';
import TableChecklist from './TableChecklist.vue';
import TableCriteriaResults from './TableCriteriaResults.vue';

// Definições de tipos e constantes
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
    checklist_colocations: Colocation[] | null;
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

// Lógica de inicialização do formulário
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
                // CORREÇÃO AQUI: Acessa 'checklist_colocations' para inicializar
                const initialPhaseResults: { [key: string]: number | null } = {};
                phase.checklist_colocations?.forEach(colocation => {
                    const existingValue = (props.results?.[team.id]?.[phase.id] as { [key: string]: number })?.[colocation.place];
                    initialPhaseResults[colocation.place] = existingValue !== undefined ? existingValue : null;
                });
                initialResults[team.id][phase.id] = initialPhaseResults;
            } else { // Para TYPE_COLOCATION e outros
                const existingValue = props.results?.[team.id]?.[phase.id];
                initialResults[team.id][phase.id] = existingValue !== undefined ? existingValue : null;
            }
        });
    });
}

const currentPhase = computed(() => {
    return props.phases.find(p => p.id === state.activePhase);
});

// Inicialização do formulário
const form = useForm({
    results: initialResults,
});

const submit = () => {
    // CORREÇÃO AQUI: Clona o formulário para modificar antes de enviar
    const dataToSend = JSON.parse(JSON.stringify(form.results));

    // Remove o campo 'colocations' das fases do tipo 'checklist'
    if (currentPhase.value && currentPhase.value.type === TYPE_CHECKLIST) {
        for (const teamId in dataToSend) {
            if (dataToSend[teamId][currentPhase.value.id]) {
                // A lógica de remoção não é mais necessária com a abordagem de filtragem,
                // mas você pode usá-la para sanar dados se precisar.
                // Exemplo:
                // delete dataToSend[teamId][currentPhase.value.id].colocations;
            }
        }
    }

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
                        <div v-if="props.user_type === USER_TYPE_ADMIN" class="flex justify-end items-center mb-4">
                            <TextButton :href="route('ranking.generate', { 'result_id': props.id })" class="p-4">
                                Ver Classificação
                            </TextButton>
                        </div>

                        <div v-if="props.phases.length > 0">
                            <div class="flex space-x-2 mb-6 overflow-x-auto">
                                <button v-for="(phase, index) in props.phases" :key="phase.id"
                                    @click="state.activePhase = phase.id" :class="[
                                        'px-4 py-2 rounded-md transition-colors duration-200',
                                        state.activePhase === phase.id ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'
                                    ]" :title="phase.title">
                                    Fase {{ index + 1 }}
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
                                        v-if="currentPhase.type === TYPE_CRITERIA && user_type === USER_TYPE_ADMIN"
                                        :phase="currentPhase" :teams="props.teams" :results="results"
                                        :judges="props.judges" />

                                    <TableColocation v-if="currentPhase.type === TYPE_COLOCATION" :phase="currentPhase"
                                        :teams="props.teams" :form="form" />

                                    <TableChecklist v-if="currentPhase.type === TYPE_CHECKLIST" :phase="currentPhase"
                                        :teams="props.teams" :form="form" />
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