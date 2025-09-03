<script setup lang="ts">
import { reactive, computed, watch, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TableCriteria from './TableCriteria.vue';
import TableColocation from './TableColocation.vue';
import TableChecklist from './TableChecklist.vue';
import TableCriteriaResults from './TableCriteriaResults.vue';
import { Phase, Team, Judge, ResultData } from '@/types';
import { validateForm } from '@/Utils/formValidationUtils';

const TYPE_CRITERIA = 1;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4;
const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

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

const judgeId = props.user_id;
const localStorageKey = `form_results_event_${props.id}_judge_${judgeId}`;

const getInitialResults = () => {
    const savedResults = localStorage.getItem(localStorageKey);
    if (savedResults) {
        try {
            const parsedResults = JSON.parse(savedResults);
            if (Object.keys(parsedResults).length === props.teams.length) {
                return parsedResults;
            }
        } catch (e) {
            console.error('Erro ao analisar dados do localStorage:', e);
            localStorage.removeItem(localStorageKey);
        }
    }
    return initializeFromProps();
};

const initializeFromProps = () => {
    const initialResults: { [key: number]: any } = {};
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
                    phase.checklist_colocations?.forEach(colocation => {
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
    return initialResults;
};

const form = useForm({
    results: getInitialResults(),
});

const currentPhase = computed(() => {
    return props.phases.find(p => p.id === state.activePhase);
});

const isFormIncomplete = computed(() => {
    if (!currentPhase.value) {
        return true;
    }
    if (currentPhase.value.type === TYPE_CRITERIA) {
        return validateForm(
            props.teams,
            currentPhase.value,
            form.results,
            props.user_type,
            judgeId
        );
    }
    return false;
});

watch(() => form.results, (newResults) => {
    localStorage.setItem(localStorageKey, JSON.stringify(newResults));
}, { deep: true });

const submit = () => {
    form.put(route('results.update', { id: props.id }), {
        onSuccess: (page) => {
            console.log('Resultados salvos com sucesso!');
            localStorage.removeItem(localStorageKey);
            form.results = page.props.results;
        },
        onError: (errors) => console.log('Erro ao salvar resultados:', errors)
    });
};

const handleBeforeUnload = (event: BeforeUnloadEvent) => {
    if (form.isDirty) {
        event.preventDefault();
        event.returnValue = '';
    }
};

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>

<template>

    <Head title="Gerenciar Resultados" />

    <AuthenticatedLayout>
        <div v-if="props.phases.length > 0">
            <div class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex space-x-2 overflow-x-auto">
                    <button v-for="(phase, index) in props.phases" :key="phase.id" @click="state.activePhase = phase.id"
                        :class="[
                            'px-4 py-2 rounded-md transition-colors duration-200',
                            state.activePhase === phase.id ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'
                        ]" :title="phase.title">
                        {{ phase.abbreviation }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div v-if="currentPhase">
                    <div class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mt-4">
                        <h3 class="text-xl font-semibold mb-1 text-gray-900 dark:text-gray-100">
                            {{ currentPhase.title }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" v-html="currentPhase.description"></p>
                    </div>

                    <div v-if="form.isDirty && user_type === USER_TYPE_JUDGE"
                        class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 rounded-md text-sm font-medium border border-yellow-200 dark:border-yellow-700">
                        Aviso: Existem resultados não salvos. Salve antes de sair para não perder os dados.
                    </div>

                    <TableCriteria v-if="currentPhase.type === TYPE_CRITERIA && user_type === USER_TYPE_JUDGE"
                        :phase="currentPhase" :teams="props.teams" :form="form" :user_id="props.user_id"
                        :user_type="props.user_type" :judges="props.judges" :is_form_incomplete="isFormIncomplete" />

                    <TableCriteriaResults v-if="currentPhase.type === TYPE_CRITERIA && user_type === USER_TYPE_ADMIN"
                        :phase="currentPhase" :teams="props.teams" :results="results" :judges="props.judges" />

                    <TableColocation v-if="currentPhase.type === TYPE_COLOCATION" :phase="currentPhase"
                        :teams="props.teams" :form="form" />

                    <TableChecklist v-if="currentPhase.type === TYPE_CHECKLIST" :phase="currentPhase"
                        :teams="props.teams" :form="form" />

                    <div class="flex items-center justify-end mt-6 space-x-4">
                        <TextButton v-if="currentPhase.type !== TYPE_CRITERIA || user_type === USER_TYPE_JUDGE"
                            :disabled="form.processing || isFormIncomplete" class="p-4">
                            Salvar Resultados
                        </TextButton>
                    </div>
                </div>
            </form>
        </div>

        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            <p>Esta gincana não possui provas ou equipes cadastradas para gerenciamento de resultados.</p>
        </div>
    </AuthenticatedLayout>
</template>