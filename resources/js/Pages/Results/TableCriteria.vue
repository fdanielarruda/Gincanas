<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

interface Team {
    id: number;
    title: string;
}

interface Phase {
    id: number;
    criteria: string[] | null;
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
</script>

<template>
    <div class="overflow-x-auto mt-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border">
            <thead>
                <tr>
                    <th
                        class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Critérios
                    </th>
                    <th v-for="team in teams" :key="team.id"
                        class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
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
</template>