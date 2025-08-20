<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { useDeleter } from '@/Composables/useDeleter';
import { formatDateForDisplay } from '@/Utils/dateUtils';
import { Head } from '@inertiajs/vue3';
import { PencilSquareIcon, RectangleStackIcon, ShieldCheckIcon, TrashIcon, UserGroupIcon } from '@heroicons/vue/24/solid';

interface Gymkhana {
    id: number;
    title: string;
    start_date: string;
    is_active: boolean;
}

const props = defineProps<{
    gymkhanas: Gymkhana[];
}>();

const {
    confirmingDeletion,
    itemToDelete,
    openConfirmModal,
    closeConfirmModal,
    performDeletion,
} = useDeleter<Gymkhana>(
    'gymkhana.destroy',
    (gymkhana) => [gymkhana.id]
);
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
                                            Título</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Data de Início</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Status</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="gymkhana in props.gymkhanas" :key="gymkhana.id">
                                        <td class="px-6 py-4 whitespace-normal">{{ gymkhana.title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ formatDateForDisplay(gymkhana.start_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="gymkhana.is_active"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Ativo
                                            </span>
                                            <span v-else
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pendente
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('gymkhana.judges.index', gymkhana.id)"
                                                color="gray" title="Juízes">
                                                <ShieldCheckIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton :href="route('gymkhana.teams.index', gymkhana.id)" class="ml-1"
                                                color="blue" title="Equipes">
                                                <UserGroupIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton :href="route('gymkhana.phases.index', gymkhana.id)" class="ml-1"
                                                color="green" title="Fases">
                                                <RectangleStackIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton :href="route('gymkhana.edit', gymkhana.id)" class="ml-1"
                                                color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton as="button" color="red" title="Deletar" class="ml-1"
                                                @click="openConfirmModal(gymkhana)">
                                                <TrashIcon class="h-5 w-5" />
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

    <ConfirmDeletionModal :show="confirmingDeletion" title="Deletar Gincana"
        :message="`Tem certeza que deseja deletar a gincana '${itemToDelete?.title}'? Todos os dados associados, incluindo equipes, serão removidos.`"
        @close="closeConfirmModal" @confirm="performDeletion" />
</template>