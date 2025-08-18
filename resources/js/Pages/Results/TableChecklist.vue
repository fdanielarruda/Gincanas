<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

interface Team {
    id: number;
    title: string;
}

interface Colocation {
    place: string;
    points: number;
}

interface Phase {
    id: number;
    colocations: Colocation[] | null;
}

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    form: ReturnType<typeof useForm>;
}>();
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Item (Pontos)
                    </th>
                    <th v-for="team in teams" :key="team.id" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ team.title }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="colocation in phase.colocations" :key="colocation.place">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                        {{ colocation.place }} ({{ colocation.points }} pts)
                    </td>
                    <td v-for="team in teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                        <input
                            type="number"
                            class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.results[team.id][phase.id][colocation.place]"
                            :id="`score-${team.id}-${phase.id}-${colocation.place}`"
                            min="0"
                        />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                    <td class="px-6 py-4 whitespace-nowrap text-left">Total da Fase:</td>
                    <td v-for="team in teams" :key="team.id" class="px-6 py-4 whitespace-nowrap">
                        {{ Object.keys(form.results[team.id][phase.id]).reduce((sum, colocationPlace) => {
                            const colocation = phase.colocations?.find(c => c.place === colocationPlace);
                            const quantity = Number(form.results[team.id][phase.id][colocationPlace]) || 0;
                            return sum + (quantity * (colocation?.points || 0));
                        }, 0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>