<script setup lang="ts">
import { Phase } from '@/types';
import { useForm, Link } from '@inertiajs/vue3';
import { defineProps, ref, onMounted, onUnmounted } from 'vue';

const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

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
                            class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ team.title }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-if="user_type === USER_TYPE_JUDGE" v-for="(criterion, index) in phase.criteria" :key="index">
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
            </table>
        </div>

        <div v-else class="mt-4">
            <div v-if="user_type === USER_TYPE_JUDGE" v-for="team in teams" :key="team.id"
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
            </div>
        </div>
    </div>
</template>