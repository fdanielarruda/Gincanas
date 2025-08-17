<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { formatDateForDisplay } from '@/Utils/DateUtils';
import { Head } from '@inertiajs/vue3';
import { PencilSquareIcon, UserGroupIcon } from '@heroicons/vue/24/solid';

interface Gymkhana {
    id: number;
    title: string;
    start_date: string;
}

const props = defineProps<{
    gymkhanas: Gymkhana[];
}>();
</script>

<template>

    <Head title="Gincanas" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <TextButton :href="route('gymkhana.create')" class="p-4">
                                Nova Gincana
                            </TextButton>
                        </div>

                        <div v-if="props.gymkhanas.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Título</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Data de Início</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="gymkhana in props.gymkhanas" :key="gymkhana.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ gymkhana.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ gymkhana.title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ formatDateForDisplay(gymkhana.start_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('gymkhana.edit', gymkhana.id)" color="yellow"
                                                title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton :href="route('gymkhana.teams', gymkhana.id)" color="blue"
                                                title="Times" class="ml-1">
                                                <UserGroupIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma gincana encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>