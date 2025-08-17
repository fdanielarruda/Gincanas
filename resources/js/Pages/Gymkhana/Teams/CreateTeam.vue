<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import TeamForm from './TeamForm.vue';

interface Gymkhana {
    id: number;
}

interface TeamFormFields {
    title: string;
    participants: Array<string>;
}

const props = defineProps<{
    gymkhana: Gymkhana;
}>();

const submit = (form: InertiaForm<TeamFormFields>) => {
    form.post(route('gymkhana.teams.store', props.gymkhana.id));
};
</script>

<template>

    <Head title="Adicionar Equipe" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <TeamForm @form-submitted="submit">
                            <template #buttonText>
                                Salvar Equipe
                            </template>
                        </TeamForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>