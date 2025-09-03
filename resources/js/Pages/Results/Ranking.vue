<script setup lang="ts">
import { ref, computed, watch, onMounted, reactive, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';
import ExternalLayout from '@/Layouts/ExternalLayout.vue';
import { InformationCircleIcon } from '@heroicons/vue/24/solid';
import { calculatePhaseScore, getRankedTeams } from '@/Utils/scoreCalculator';
import TableCriteriaDetails from './TableCriteriaDetails.vue';
import TableColocationDetails from './TableColocationDetails.vue';
import { Phase, Team, ResultData, Judge } from '@/types';

const TYPE_CRITERIA = 1;
const TYPE_COLOCATION = 3;

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
    judges: Judge[];
}>();

const showPhaseScores = ref(false);

const collapsedPhases = reactive<{ [key: number]: boolean }>({});

const isPrinting = ref(false);

onMounted(() => {
    const savedState = sessionStorage.getItem('showPhaseScores');
    if (savedState !== null) {
        showPhaseScores.value = JSON.parse(savedState);
    }
    props.phases.forEach(phase => {
        collapsedPhases[phase.id] = true;
    });

    window.addEventListener('beforeprint', handleBeforePrint);
    window.addEventListener('afterprint', handleAfterPrint);
});

onBeforeUnmount(() => {
    window.removeEventListener('beforeprint', handleBeforePrint);
    window.removeEventListener('afterprint', handleAfterPrint);
});

watch(showPhaseScores, (newValue) => {
    sessionStorage.setItem('showPhaseScores', JSON.stringify(newValue));
});

const rankedTeams = computed(() => {
    return getRankedTeams(props.teams, props.phases, props.results);
});

const hasAnyScores = computed(() => {
    return rankedTeams.value.some(team => team.totalScore > 0);
});

/**
 * Alterna o estado de colapso de uma fase.
 * @param phaseId O ID da fase a ser alternada.
 */
const toggleCollapse = (phaseId: number) => {
    collapsedPhases[phaseId] = !collapsedPhases[phaseId];
};

/**
 * Lida com o evento antes da impressão, garantindo que os detalhes de pontuação estejam visíveis.
 */
const handleBeforePrint = () => {
    isPrinting.value = true;
    props.phases.forEach(phase => {
        collapsedPhases[phase.id] = false;
    });
};

/**
 * Lida com o evento após a impressão, retornando o estado ao normal.
 */
const handleAfterPrint = () => {
    isPrinting.value = false;
    props.phases.forEach(phase => {
        collapsedPhases[phase.id] = true;
    });
};
</script>

<template>

    <Head :title="`Classificação - ${props.gincana.title}`" />

    <ExternalLayout>
        <h2 class="text-xl font-bold mb-4">Classificação Geral - {{ props.gincana.title }}</h2>

        <div v-if="hasAnyScores" class="flex items-center mb-4 no-print">
            <input type="checkbox" id="showPhaseScores" v-model="showPhaseScores"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
            <label for="showPhaseScores" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Exibir pontuação por prova
            </label>
        </div>

        <div v-if="rankedTeams.length > 0">
            <div class="overflow-x-auto print-page-break">
                <table class="w-full text-left table-auto">
                    <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-1 px-2 font-bold min-w-[60px]"></th>
                            <th class="py-1 px-2 font-bold min-w-[150px]">Equipe</th>

                            <template v-if="showPhaseScores || isPrinting" v-for="(phase, index) in props.phases"
                                :key="`header-${phase.id}`">
                                <th class="py-1 px-2 font-bold text-right min-w-[80px] relative">
                                    <div class="flex items-center justify-end">
                                        <span>{{ phase.abbreviation }}</span>
                                        <div class="group relative ml-1 no-print">
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

                            <th class="py-1 px-2 font-bold text-right min-w-[100px]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(team, index) in rankedTeams" :key="team.id"
                            :class="{ 'bg-gray-50 dark:bg-gray-700': index % 2 === 0, 'bg-white dark:bg-gray-800': index % 2 !== 0 }">
                            <td class="py-1 px-2 text-center">{{ index + 1 }}º</td>
                            <td class="px-2 py-2 whitespace-normal">
                                <div class="flex flex-col">
                                    <span>{{ team.title }}</span>
                                    <span class="text-gray-500 dark:text-gray-300 text-sm">
                                        {{ team.participants.join(', ') }}
                                    </span>
                                </div>
                            </td>

                            <template v-if="showPhaseScores || isPrinting" v-for="phase in props.phases"
                                :key="`row-${team.id}-${phase.id}`">
                                <td class="py-1 px-2 text-right">
                                    {{ calculatePhaseScore(team.id, phase, props.results, props.teams) }}
                                </td>
                            </template>

                            <td class="py-1 px-2 text-right font-bold">{{ team.totalScore }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            <p>Ainda não há resultados para gerar a classificação.</p>
        </div>

        <div v-if="hasAnyScores" class="mt-8">
            <h3 class="text-xl font-bold mb-4 no-print">Detalhes da Pontuação por Prova</h3>
            <div v-for="phase in props.phases" :key="`details-${phase.id}`"
                :class="{ 'print-page-break': phase.type === TYPE_CRITERIA || phase.type === TYPE_COLOCATION }">
                <div v-if="phase.type === TYPE_CRITERIA || phase.type === TYPE_COLOCATION" class="mb-4">
                    <div class="print-phase-header-only">
                        <h3>{{ phase.title }} ({{ phase.abbreviation }})</h3>
                    </div>

                    <div @click="toggleCollapse(phase.id)"
                        class="flex items-center justify-between p-4 rounded-md cursor-pointer bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 no-print">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ phase.title }} ({{ phase.abbreviation }})
                        </h4>

                        <svg class="w-5 h-5 transition-transform duration-200 transform"
                            :class="{ 'rotate-180': !collapsedPhases[phase.id] }" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <div v-show="!collapsedPhases[phase.id] || isPrinting">
                        <TableCriteriaDetails v-if="phase.type === TYPE_CRITERIA" :phase="phase" :teams="props.teams"
                            :judges="props.judges" :results="props.results" />
                        <TableColocationDetails v-else-if="phase.type === TYPE_COLOCATION" :phase="phase"
                            :teams="props.teams" :judges="props.judges" :results="props.results" />
                    </div>
                </div>
            </div>
        </div>
    </ExternalLayout>
</template>

<style>
.print-phase-header-only {
    display: none;
    text-align: center;
}

@media print {
    @page {
        size: landscape;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .no-print {
        display: none !important;
    }

    .print-page-break {
        break-after: page;
    }

    h4 {
        font-size: 12pt !important;
    }

    table {
        width: 100% !important;
        font-size: 10pt !important;
        border-collapse: collapse;
        table-layout: fixed !important;
        text-align: center;
    }

    th,
    td {
        padding: 1px !important;
        word-wrap: break-word;
        white-space: normal !important;
        border: 1px solid #ccc;
        text-align: center !important;
    }

    th:first-child,
    td:first-child {
        text-align: left;
    }

    th:nth-child(2),
    td:nth-child(2) {
        text-align: left !important;
    }

    th:nth-child(1) {
        width: 5%;
    }

    th:nth-child(2) {
        width: 25%;
    }

    th:not(:nth-child(1)):not(:nth-child(2)),
    td:not(:nth-child(1)):not(:nth-child(2)) {
        width: 7%;
    }

    .text-sm {
        font-size: 7px !important;
    }

    .flex.items-center.justify-end {
        display: block;
        text-align: center !important;
    }

    .table-criteria-details-print {
        width: 100% !important;
        table-layout: fixed !important;
    }

    .table-criteria-details-print th,
    .table-criteria-details-print td {
        word-wrap: break-word;
        white-space: normal !important;
        border: 1px solid #ccc;
        text-align: center !important;
    }

    .table-criteria-details-print th,
    .table-criteria-details-print td {
        width: 100px !important;
    }

    .print-phase-header-only {
        display: block;
        page-break-before: auto;
        font-size: 14pt;
        font-weight: bold;
        color: #333;
        margin-top: 30px;
    }

    .border {
        border: none;
    }
}
</style>