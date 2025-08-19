<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { useDeleter } from '@/Composables/useDeleter';
import TextButton from '@/Components/Itens/TextButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { formatDateForDisplay } from '@/Utils/DateUtils';
import { Head } from '@inertiajs/vue3';
import { PlayCircleIcon, TrashIcon, TrophyIcon } from '@heroicons/vue/24/solid';

const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

interface Gymkhana {
    id: number;
    tite: string;
}

interface GymkhanaResult {
    id: number;
    gimkhana: Gymkhana;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    results: GymkhanaResult[];
    user_type: number;
}>();

const {
    confirmingDeletion,
    itemToDelete,
    openConfirmModal,
    closeConfirmModal,
    performDeletion,
} = useDeleter<GymkhanaResult>(
    'results.destroy',
    (result) => [result.id]
);
</script>

<template>

    <Head title="Gincanas" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="user_type === USER_TYPE_ADMIN" class="flex justify-end items-center mb-4">
                            <TextButton :href="route('results.create')" class="p-4">
                                Inserir Resultados
                            </TextButton>
                        </div>

                        <div v-if="props.results.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Gincana</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Criado em</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Atualizado em</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="result in props.results" :key="result.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ result.gymkhana.title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ formatDateForDisplay(result.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ formatDateForDisplay(result.updated_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('results.manager', result.id)" as="button"
                                                color="green" title="Resultados">
                                                <PlayCircleIcon class="h-5 w-5" />
                                            </IconButton>

                                            <template v-if="user_type === USER_TYPE_ADMIN">
                                                <IconButton :href="route('ranking.generate', { 'result_id': result.id })"
                                                    :target="target" class="ml-1" color="indigo" title="Classificação">
                                                    <TrophyIcon class="h-5 w-5" />
                                                </IconButton>

                                                <IconButton as="button" color="red" title="Deletar" class="ml-1"
                                                    @click="openConfirmModal(result)">
                                                    <TrashIcon class="h-5 w-5" />
                                                </IconButton>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhum resultado encontrado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Deletar Resutado"
        message="Tem certeza que deseja deletar estes resutados?" @close="closeConfirmModal"
        @confirm="performDeletion" />
</template>