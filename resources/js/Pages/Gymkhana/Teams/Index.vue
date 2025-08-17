<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head } from '@inertiajs/vue3';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { useDeleter } from '@/Composables/useDeleter';

interface Gymkhana {
    id: number;
    title: string;
    teams: Team[];
}

interface Team {
    id: number;
    title: string;
    participants: string[];
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
} = useDeleter<Team>(
    'gymkhana.teams.destroy',
    (team) => [props.gymkhana.id, team.id]
);

const openConfirmRemoveModal = openConfirmModal;
const closeModal = closeConfirmModal;
const removeTeam = performDeletion;
</script>

<template>

    <Head title="Equipes" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <TextButton :href="route('gymkhana.teams.create', gymkhana.id)" class="p-4">
                                Nova Equipe
                            </TextButton>

                            <TextButton :href="route('gymkhana.index')" class="p-4 ml-2" color="gray">
                                Voltar
                            </TextButton>
                        </div>

                        <div v-if="gymkhana.teams?.length > 0" class="overflow-x-auto">
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
                                            Participantes</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="team in gymkhana.teams" :key="team.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ team.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ team.title }}</td>
                                        <td class="px-6 py-4">
                                            <span v-if="team.participants && team.participants.length > 0">
                                                {{ team.participants.join(', ') }}
                                            </span>
                                            <span v-else>
                                                Nenhum participante
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('gymkhana.teams.edit', [gymkhana.id, team.id])"
                                                color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton as="button" color="red" title="Remover Inscrição" class="ml-1"
                                                @click.stop="openConfirmRemoveModal(team)">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma equipe encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${itemToDelete?.title}' desta gincana?`"
        @close="closeModal" @confirm="removeTeam" />
</template>