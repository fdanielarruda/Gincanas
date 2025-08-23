<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import ExternalLayout from '@/Layouts/ExternalLayout.vue';
import { InformationCircleIcon } from '@heroicons/vue/24/solid';
import { calculatePhaseScore, getRankedTeams } from '@/Utils/scoreCalculator';

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

const showPhaseScores = ref(false);

onMounted(() => {
    const savedState = sessionStorage.getItem('showPhaseScores');
    if (savedState !== null) {
        showPhaseScores.value = JSON.parse(savedState);
    }
});

watch(showPhaseScores, (newValue) => {
    sessionStorage.setItem('showPhaseScores', JSON.stringify(newValue));
});

const rankedTeams = computed(() => {
    return getRankedTeams(props.teams, props.phases, props.results);
});
</script>

<template>

    <Head :title="`Classificação - ${props.gincana.title}`" />

    <ExternalLayout>
        <h2 class="text-xl font-bold mb-4">Classificação Geral - {{ props.gincana.title }}</h2>

        <div class="flex items-center mb-4">
            <input type="checkbox" id="showPhaseScores" v-model="showPhaseScores"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
            <label for="showPhaseScores" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Exibir pontuação por fase
            </label>
        </div>

        <div v-if="rankedTeams.length > 0">
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto">
                    <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2 px-4 font-bold min-w-[60px]"></th>
                            <th class="py-2 px-4 font-bold min-w-[150px]">Equipe</th>

                            <template v-if="showPhaseScores" v-for="(phase, index) in props.phases"
                                :key="`header-${phase.id}`">
                                <th class="py-2 px-4 font-bold text-right min-w-[80px] relative">
                                    <div class="flex items-center justify-end">
                                        <span>{{ index + 1 }}</span>
                                        <div class="group relative ml-1">
                                            <InformationCircleIcon
                                                class="h-4 w-4 text-gray-400 dark:text-gray-500 cursor-pointer" />
                                            <div
                                                class="absolute right-0 top-full mt-2 w-max p-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10">
                                                {{ phase.title }}
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </template>

                            <th class="py-2 px-4 font-bold text-right min-w-[100px]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(team, index) in rankedTeams" :key="team.id"
                            :class="{ 'bg-gray-50 dark:bg-gray-700': index % 2 === 0, 'bg-white dark:bg-gray-800': index % 2 !== 0 }">
                            <td class="py-2 px-4">{{ index + 1 }}º</td>
                            <td class="py-2 px-4 relative">
                                <div class="flex items-center">
                                    <span class="mr-1">{{ team.title }}</span>
                                    <div class="group relative">
                                        <InformationCircleIcon
                                            class="h-4 w-4 text-gray-400 dark:text-gray-500 cursor-pointer" />
                                        <div
                                            class="absolute left-0 bottom-full mb-2 w-max p-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10">
                                            {{ team.participants.join(', ') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <template v-if="showPhaseScores" v-for="phase in props.phases"
                                :key="`row-${team.id}-${phase.id}`">
                                <td class="py-2 px-4 text-right">{{ calculatePhaseScore(team.id, phase,
                                    props.results, props.teams)
                                }}
                                </td>
                            </template>
                            <td class="py-2 px-4 text-right font-bold">{{ team.totalScore }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            <p>Ainda não há resultados para gerar a classificação.</p>
        </div>
    </ExternalLayout>
</template>