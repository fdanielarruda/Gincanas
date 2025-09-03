<script setup lang="ts">
import { Phase } from '@/types';
import { InformationCircleIcon } from '@heroicons/vue/24/solid';
import { useForm } from '@inertiajs/vue3';
import { defineProps, ref, onMounted, onUnmounted, computed } from 'vue';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Judge {
    id: number;
    name: string;
}

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    form: ReturnType<typeof useForm>;
    user_id: number;
    user_type: number;
    judges: Judge[];
}>();

const isMobile = ref(false);

const checkIsMobile = () => {
    isMobile.value = window.innerWidth <= 640;
};

onMounted(() => {
    window.addEventListener('resize', checkIsMobile);
    checkIsMobile();
});

onUnmounted(() => {
    window.removeEventListener('resize', checkIsMobile);
});

const getTeamTotalByJudge = (teamId: number, judgeId: number) => {
    const total = computed(() => {
        let sum = 0;

        if (props.form.results[teamId] && props.form.results[teamId][props.phase.id] && props.form.results[teamId][props.phase.id][judgeId]) {
            props.form.results[teamId][props.phase.id][judgeId].forEach((score: number) => {
                sum += score || 0;
            });
        }
        return sum;
    });
    return total.value;
};

const isTeamIncomplete = (teamId: number): boolean => {
    const judgeScores = props.form.results?.[teamId]?.[props.phase.id]?.[props.user_id];

    if (!judgeScores) {
        return true;
    }

    for (const score of judgeScores) {
        if (score === null || score === undefined || score === '') {
            return true;
        }
    }

    return false;
};
</script>

<template>
    <div>
        <div v-if="!isMobile" class="overflow-x-auto mt-4">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border">
                <thead>
                    <tr>
                        <th
                            class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Critérios
                        </th>
                        <th v-for="team in teams" :key="team.id"
                            class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider relative">
                            <div class="flex items-center justify-center">
                                {{ team.title }}

                                <div class="group relative ml-1">
                                    <InformationCircleIcon
                                        class="h-4 w-4 text-gray-400 dark:text-gray-500 cursor-pointer" />
                                    <div
                                        class="absolute right-0 top-full mt-2 w-max p-2 text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10">
                                        {{ team.participants.join(', ') }}
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="(criterion, index) in phase.criteria" :key="index">
                        <td class="px-6 py-4 whitespace-normal font-medium text-gray-900 dark:text-gray-100">
                            {{ criterion }}
                        </td>
                        <td v-for="team in teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-center">
                                <input type="number"
                                    class="w-20 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-center"
                                    v-model="form.results[team.id][phase.id][props.user_id][index]"
                                    :id="`score-${team.id}-${phase.id}-${index}`" min="0" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-200 dark:bg-gray-700 font-semibold text-gray-900 dark:text-gray-100">
                    <tr>
                        <td class="py-3 px-6">Total</td>
                        <td v-for="team in props.teams" :key="team.id"
                            class="py-3 px-6 text-center text-black dark:text-white font-bold">
                            <span v-if="!isTeamIncomplete(team.id)">
                                {{ getTeamTotalByJudge(team.id, props.user_id) }}
                            </span>
                            <span v-else>-</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div v-else class="mt-4">
            <div v-for="team in teams" :key="team.id"
                class="bg-white dark:bg-gray-800 border dark:border-gray-700 p-2 rounded-lg shadow-sm mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ team.title }}
                    <p class="text-sm text-gray-400 dark:text-gray-400">{{ team.participants.join(', ') }}</p>
                    <hr class="mt-2">
                </h3>

                <div v-for="(criterion, index) in phase.criteria" :key="index"
                    class="flex items-center justify-between py-2 border-b last:border-b-0">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ criterion }}</span>
                    <input type="number"
                        class="w-20 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-center"
                        v-model="form.results[team.id][phase.id][props.user_id][index]"
                        :id="`score-${team.id}-${phase.id}-${index}`" min="0" />
                </div>
                <div class="mt-2 p-1 font-semibold flex justify-between">
                    <span class="text-gray-900 dark:text-gray-100">Total:</span>
                    <span class="text-black dark:text-white font-bold" v-if="!isTeamIncomplete(team.id)">
                        {{ getTeamTotalByJudge(team.id, props.user_id) }}
                    </span>
                    <span v-else>
                        -
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>