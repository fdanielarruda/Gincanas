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
        <TeamForm :gymkhana-id="gymkhana.id" @form-submitted="submit">
            <template #buttonText>
                Salvar Equipe
            </template>
        </TeamForm>
    </AuthenticatedLayout>
</template>