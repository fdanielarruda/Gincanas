<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head } from '@inertiajs/vue3';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { useDeleter } from '@/Composables/useDeleter';

const TYPE_CRITERIA = 1;
const TYPE_QUIZ = 2;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4;

interface Gymkhana {
    id: number;
    title: string;
    phases: Phase[];
}

interface Phase {
    id: number;
    title: string;
    description: string;
    type: number;
    criteria: string[];
    colocations: { place: string, points: number }[];
}

const props = defineProps<{
    gymkhana: Gymkhana;
}>();

const {
    confirmingDeletion,
    itemToDelete,
    openConfirmModal,
    closeConfirmModal,
    performDeletion,
} = useDeleter<Phase>(
    'gymkhana.phases.destroy',
    (phase) => [props.gymkhana.id, phase.id]
);

const openConfirmRemoveModal = openConfirmModal;
const closeModal = closeConfirmModal;
const removePhase = performDeletion;
</script>

<template>

    <Head title="Fases" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <TextButton :href="route('gymkhana.phases.create', gymkhana.id)" class="p-4">
                                Nova Fase
                            </TextButton>
                            <TextButton :href="route('gymkhana.index')" class="p-4 ml-2" color="gray">
                                Voltar
                            </TextButton>
                        </div>

                        <div v-if="gymkhana.phases?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Título</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Descrição</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Detalhes</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="phase in gymkhana.phases" :key="phase.id">
                                        <td class="px-6 py-4 whitespace-normal">
                                            <div class="flex flex-col">
                                                <span class="font-bold">{{ phase.title }}</span>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{
                                                    phase.description }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="phase.type == TYPE_CRITERIA">
                                                <ul v-if="phase.criteria && phase.criteria.length > 0"
                                                    class="list-disc list-inside">
                                                    <li v-for="(criterion, index) in phase.criteria" :key="index">
                                                        {{ criterion }}
                                                    </li>
                                                </ul>
                                                <span v-else>Nenhum critério</span>
                                            </div>
                                            <div
                                                v-else-if="phase.type == TYPE_COLOCATION || phase.type == TYPE_CHECKLIST">
                                                <ul v-if="phase.colocations && phase.colocations.length > 0"
                                                    class="list-disc list-inside">
                                                    <li v-for="(colocation, index) in phase.colocations" :key="index">
                                                        {{ colocation.place }}: {{ colocation.points }} pts
                                                    </li>
                                                </ul>
                                                <span v-else>Nenhuma colocação definida</span>
                                            </div>
                                            <div v-else>
                                                <span>Não aplicável</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('gymkhana.phases.edit', [gymkhana.id, phase.id])"
                                                color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>
                                            <IconButton as="button" color="red" title="Remover Fase" class="ml-1"
                                                @click.stop="openConfirmRemoveModal(phase)">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma fase encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Remover Fase"
        :message="`Tem certeza que deseja remover a fase '${itemToDelete?.title}' desta gincana?`" @close="closeModal"
        @confirm="removePhase" />
</template>