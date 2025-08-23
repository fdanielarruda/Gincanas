<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const TYPE_CHECKLIST = 4;

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
    type: number;
    colocations: Colocation[] | null;
}

const props = defineProps<{
    phase: Phase;
    teams: Team[];
    form: ReturnType<typeof useForm>;
}>();
</script>

<template>
    <div class="overflow-x-auto mt-6">
        <div v-for="team in props.teams" :key="team.id" class="p-4 rounded-lg border mb-6">
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ team.title }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
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