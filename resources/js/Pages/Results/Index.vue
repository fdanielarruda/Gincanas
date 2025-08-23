<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { useDeleter } from '@/Composables/useDeleter';
import TextButton from '@/Components/Itens/TextButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { formatDateTimeForDisplay } from '@/Utils/dateUtils';
import { Head } from '@inertiajs/vue3';
import { PlayCircleIcon, TrashIcon, TrophyIcon } from '@heroicons/vue/24/solid';
import LinkButton from '@/Components/LinkButton.vue';

const USER_TYPE_ADMIN = 1;
const USER_TYPE_JUDGE = 2;

interface Gymkhana {
    id: number;
    title: string;
}

interface GymkhanaResult {
    id: number;
    gymkhana: Gymkhana;
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
                            Atualizado em</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="result in props.results" :key="result.id">
                        <td class="px-6 py-4 whitespace-normal">
                            {{ result.gymkhana.title }}
                        </td>
                        <td class="px-6 py-4 whitespace-normal">
                            {{ formatDateTimeForDisplay(result.updated_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <IconButton :href="route('results.manager', result.id)" as="button" color="green"
                                title="Resultados">
                                <PlayCircleIcon class="h-5 w-5" />
                            </IconButton>

                            <template v-if="user_type === USER_TYPE_ADMIN">
                                <LinkButton :link="route('ranking.generate', { 'result_id': result.id })" title="Classificação" class="ml-1" color="indigo">
                                    <TrophyIcon class="h-5 w-5" />
                                </LinkButton>

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
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Deletar Resutado"
        message="Tem certeza que deseja deletar estes resutados?" @close="closeConfirmModal"
        @confirm="performDeletion" />
</template>