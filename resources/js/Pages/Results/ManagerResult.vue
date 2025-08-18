<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Constantes para os tipos de fases
const TYPE_CRITERIA = 1;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4; // Tipo 4 agora usa a estrutura de 'colocations'

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Colocation {
    place: string;
    points: number;
}

// A fase do tipo 4 usa a mesma estrutura de colocations
interface Phase {
    id: number;
    title: string;
    type: number;
    criteria: string[] | null;
    colocations: Colocation[] | null;
    description: string;
}

interface ResultData {
    [teamId: number]: {
        [phaseId: number]: number | { [criterionIndex: number]: number } | { [colocationPlace: string]: number };
    };
}

interface GymkhanaResult {
    id: number;
    teams: Team[];
    phases: Phase[];
    results: ResultData | null;
}

const props = defineProps<{
    result: GymkhanaResult;
}>();

const state = reactive({
    activePhase: props.result.phases[0] ? props.result.phases[0].id : null,
});

const initialResults = {};
if (props.result.teams && props.result.phases) {
    props.result.teams.forEach(team => {
        initialResults[team.id] = {};
        props.result.phases.forEach(phase => {
            if (phase.type === TYPE_CRITERIA) {
                const initialPhaseResults = {};
                phase.criteria?.forEach((criterion, index) => {
                    const existingValue = props.result.results?.[team.id]?.[phase.id]?.[index];
                    initialPhaseResults[index] = existingValue !== undefined ? existingValue : null;
                });
                initialResults[team.id][phase.id] = initialPhaseResults;
            } else if (phase.type === TYPE_CHECKLIST) {
                // A nova lógica para o tipo 4, usando 'colocations'
                const initialPhaseResults = {};
                phase.colocations?.forEach(colocation => {
                    const existingValue = props.result.results?.[team.id]?.[phase.id]?.[colocation.place];
                    initialPhaseResults[colocation.place] = existingValue !== undefined ? existingValue : null;
                });
                initialResults[team.id][phase.id] = initialPhaseResults;
            } else {
                const existingValue = props.result.results?.[team.id]?.[phase.id];
                initialResults[team.id][phase.id] = existingValue !== undefined ? existingValue : null;
            }
        });
    });
}

const form = useForm({
    results: initialResults,
});

const calculateTotalScore = (teamId: number) => {
    let total = 0;
    if (form.results && form.results[teamId]) {
        for (const phaseId in form.results[teamId]) {
            const phase = props.result.phases.find(p => p.id === Number(phaseId));
            if (!phase) continue;

            const phaseResult = form.results[teamId][phaseId];

            if (phase.type === TYPE_CRITERIA) {
                for (const criterionIndex in phaseResult) {
                    total += Number(phaseResult[criterionIndex]) || 0;
                }
            } else if (phase.type === TYPE_CHECKLIST && phase.colocations) {
                for (const colocationPlace in phaseResult) {
                    const colocation = phase.colocations.find(c => c.place === colocationPlace);
                    if (colocation) {
                        const quantity = Number(phaseResult[colocationPlace]) || 0;
                        total += quantity * colocation.points;
                    }
                }
            } else {
                total += Number(phaseResult) || 0;
            }
        }
    }
    return total;
};

const calculateItemScore = (teamId: number, phaseId: number, colocationPlace: string) => {
    const phase = props.result.phases.find(p => p.id === Number(phaseId));
    const colocation = phase?.colocations?.find(c => c.place === colocationPlace);
    const quantity = form.results[teamId]?.[phaseId]?.[colocationPlace] || 0;
    return Number(quantity) * (colocation?.points || 0);
};

const submit = () => {
    form.put(route('results.update', props.result.id), {
        onSuccess: () => {
            // ...
        },
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
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Gerenciar Resultados da Gincana
                        </h2>
                        
                        <div v-if="props.result.phases.length > 0">
                            <div class="flex space-x-2 mb-6">
                                <button
                                    v-for="phase in props.result.phases"
                                    :key="phase.id"
                                    @click="state.activePhase = phase.id"
                                    :class="[
                                        'px-4 py-2 rounded-md transition-colors duration-200',
                                        state.activePhase === phase.id ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600'
                                    ]"
                                >
                                    {{ phase.title }}
                                </button>
                            </div>
                            
                            <form @submit.prevent="submit">
                                <div v-for="phase in props.result.phases" :key="phase.id">
                                    <div v-if="state.activePhase === phase.id">
                                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ phase.title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ phase.description }}</p>

                                        <div v-if="phase.type === TYPE_CHECKLIST && phase.colocations">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Item (Pontos)
                                                            </th>
                                                            <th v-for="team in props.result.teams" :key="team.id" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                {{ team.title }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        <tr v-for="colocation in phase.colocations" :key="colocation.place">
                                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                                                {{ colocation.place }} ({{ colocation.points }} pts)
                                                            </td>
                                                            <td v-for="team in props.result.teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                                                                <input
                                                                    type="number"
                                                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                                    v-model="form.results[team.id][phase.id][colocation.place]"
                                                                    :id="`score-${team.id}-${phase.id}-${colocation.place}`"
                                                                    min="0"
                                                                />
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                                            <td class="px-6 py-4 whitespace-nowrap text-right">Total da Fase:</td>
                                                            <td v-for="team in props.result.teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                                                                {{ Object.keys(form.results[team.id][phase.id]).reduce((sum, colocationPlace) => {
                                                                    const colocation = phase.colocations?.find(c => c.place === colocationPlace);
                                                                    const quantity = Number(form.results[team.id][phase.id][colocationPlace]) || 0;
                                                                    return sum + (quantity * (colocation?.points || 0));
                                                                }, 0) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div v-else-if="phase.type === TYPE_CRITERIA && phase.criteria">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Critério
                                                            </th>
                                                            <th v-for="team in props.result.teams" :key="team.id" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                {{ team.title }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        <tr v-for="(criterion, index) in phase.criteria" :key="index">
                                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                                                {{ criterion }}
                                                            </td>
                                                            <td v-for="team in props.result.teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                                                                <input
                                                                    type="number"
                                                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                                    v-model="form.results[team.id][phase.id][index]"
                                                                    :id="`score-${team.id}-${phase.id}-${index}`"
                                                                    min="0"
                                                                />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div v-else-if="phase.type === TYPE_COLOCATION && phase.colocations">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Equipe
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Colocação
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Pontos
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        <tr v-for="team in props.result.teams" :key="team.id">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ team.title }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <select
                                                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                                    v-model="form.results[team.id][phase.id]"
                                                                >
                                                                    <option :value="null">Selecione uma colocação</option>
                                                                    <option v-for="colocation in phase.colocations" :key="colocation.place" :value="colocation.points">
                                                                        {{ colocation.place }} ({{ colocation.points }} pts)
                                                                    </option>
                                                                </select>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                                                {{ form.results[team.id][phase.id] || 0 }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div v-else>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Equipe
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Pontuação
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                                Total
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        <tr v-for="team in props.result.teams" :key="team.id">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ team.title }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <input
                                                                    type="number"
                                                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                                    v-model="form.results[team.id][phase.id]"
                                                                    :id="`score-${team.id}-${phase.id}`"
                                                                    min="0"
                                                                />
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                                                {{ calculateTotalScore(team.id) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-end mt-6">
                                    <TextButton :disabled="form.processing" class="p-4">
                                        Salvar Resultados
                                    </TextButton>
                                </div>
                            </form>
                        </div>
                        
                        <div v-else class="text-center text-gray-500 dark:text-gray-400">
                            <p>Esta gincana não possui fases ou equipes cadastradas para gerenciamento de resultados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>