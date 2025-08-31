<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PencilSquareIcon, TrashIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { useDeleter } from '@/Composables/useDeleter';
import { Phase } from '@/types';

interface Gymkhana {
    id: number;
    title: string;
    phases: Phase[];
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

const form = useForm({});

const movePhase = (phase: Phase, direction: 'up' | 'down') => {
    form.put(route('gymkhana.phases.reorder', {
        gymkhana_id: props.gymkhana.id,
        phase_id: phase.id,
        direction: direction
    }), {
        preserveState: true,
        preserveScroll: true
    });
};

</script>

<template>

    <Head title="Provas" />

    <AuthenticatedLayout>
        <div class="flex justify-end items-center mb-4">
            <TextButton :href="route('gymkhana.phases.create', gymkhana.id)" class="p-4">
                Nova Prova
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
                            Prova</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Abv.</th>
                        <th scope="col" class="relative px-6 py-3 text-right"><span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="(phase, index) in gymkhana.phases" :key="phase.id">
                        <td class="px-6 py-4 whitespace-normal">
                            <div class="flex flex-col">
                                <span class="font-bold">{{ phase.title }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400" v-html="phase.description">
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-normal">
                            <div class="flex flex-col">
                                <span class="font-bold">{{ phase.abbreviation }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <IconButton as="button" color="gray" title="Subir" class="mr-1" :disabled="index === 0"
                                @click.stop="movePhase(phase, 'up')">
                                <ArrowUpCircleIcon class="h-5 w-5" />
                            </IconButton>
                            <IconButton as="button" color="gray" title="Descer" class="mr-1"
                                :disabled="index === gymkhana.phases.length - 1" @click.stop="movePhase(phase, 'down')">
                                <ArrowDownCircleIcon class="h-5 w-5" />
                            </IconButton>
                            <IconButton :href="route('gymkhana.phases.edit', [gymkhana.id, phase.id])" color="yellow"
                                title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>
                            <IconButton as="button" color="red" title="Remover Prova" class="ml-1"
                                @click.stop="openConfirmRemoveModal(phase)">
                                <TrashIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>Nenhuma prova encontrada.</p>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Remover Prova"
        :message="`Tem certeza que deseja remover a prova '${itemToDelete?.title}' desta gincana?`" @close="closeModal"
        @confirm="removePhase" />
</template>