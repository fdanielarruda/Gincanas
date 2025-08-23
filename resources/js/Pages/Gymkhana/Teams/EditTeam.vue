<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import TeamForm from './TeamForm.vue';

interface Gymkhana {
    id: number;
}

interface Team {
    id: number;
    title: string;
}

interface TeamFormFields {
    title: string;
    participants: Array<string>;
}

const props = defineProps<{
    gymkhana: Gymkhana;
    team: Team;
}>();

const submit = (form: InertiaForm<TeamFormFields>) => {
    form.put(route('gymkhana.teams.update', [props.gymkhana.id, props.team.id]));
};
</script>

<template>

    <Head :title="`Editar Equipe: ${props.team.title}`" />

    <AuthenticatedLayout>
        <TeamForm :initial-data="props.team" :gymkhana-id="gymkhana.id" @form-submitted="submit">
            <template #buttonText>
                Atualizar Equipe
            </template>
        </TeamForm>
    </AuthenticatedLayout>
</template>