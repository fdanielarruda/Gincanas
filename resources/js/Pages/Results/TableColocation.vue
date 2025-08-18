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
                <tr v-for="team in teams" :key="team.id">
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
</template>