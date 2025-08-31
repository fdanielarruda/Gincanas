<script setup lang="ts">
import { Phase } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

interface Team {
    id: number;
    title: string;
    participants: string[];
}

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    form: ReturnType<typeof useForm>;
}>();
</script>

<template>
    <div class="overflow-x-auto mt-4">
        <div v-for="team in props.teams" :key="team.id" class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-4">
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ team.title }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Participantes: {{ team.participants.join(', ') }}
            </p>

            <div class="overflow-x-auto">
                <select
                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    v-model="form.results[team.id][phase.id]">
                    <option :value="null">Selecione uma colocação</option>
                    <option v-for="colocation in phase.colocations" :key="colocation.place" :value="colocation.points">
                        {{ colocation.place }} ({{ colocation.points }} pts)
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>