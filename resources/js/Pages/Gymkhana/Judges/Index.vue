<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head } from '@inertiajs/vue3';
import { TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { useDeleter } from '@/Composables/useDeleter';

interface Gymkhana {
    id: number;
    title: string;
    judges: Judge[];
}

interface Judge {
    id: number;
    name: string;
    email: string;
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
} = useDeleter<Judge>(
    'gymkhana.judges.destroy',
    (judge) => [props.gymkhana.id, judge.id]
);

const openConfirmRemoveModal = openConfirmModal;
const closeModal = closeConfirmModal;
const removeJudge = performDeletion;
</script>

<template>

    <Head title="Juízes" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <div class="flex items-center">
                                <TextButton :href="route('gymkhana.judges.create', gymkhana.id)" class="p-4">
                                    Adicionar Juiz
                                </TextButton>
                                <TextButton :href="route('gymkhana.index')" class="p-4 ml-2" color="gray">
                                    Voltar
                                </TextButton>
                            </div>
                        </div>

                        <div v-if="gymkhana.judges?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Nome</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Email</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="judge in gymkhana.judges" :key="judge.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ judge.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ judge.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton as="button" color="red" title="Remover Juiz"
                                                @click.stop="openConfirmRemoveModal(judge)">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhum juiz associado a esta gincana.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Remover Juiz"
        :message="`Tem certeza que deseja remover o juiz '${itemToDelete?.name}' desta gincana? Esta ação não pode ser desfeita.`"
        @close="closeModal" @confirm="removeJudge" />
</template>