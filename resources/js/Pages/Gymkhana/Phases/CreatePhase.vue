<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import PhaseForm from './PhaseForm.vue';

interface Gymkhana {
    id: number;
}

interface PhaseFormFields {
    title: string;
    description: string;
    type: number;
    criteria?: Array<string>;
    colocations?: Array<{ place: string, points: number }>;
    checklist_colocations?: Array<{ place: string, points: number }>;
}

const props = defineProps<{
    gymkhana: Gymkhana;
}>();

const submit = (form: InertiaForm<PhaseFormFields>) => {
    form.post(route('gymkhana.phases.store', props.gymkhana.id));
};
</script>

<template>

    <Head title="Adicionar Fase" />

    <AuthenticatedLayout>
        <PhaseForm :gymkhana-id="gymkhana.id" @form-submitted="submit">
            <template #buttonText>
                Salvar Fase
            </template>
        </PhaseForm>
    </AuthenticatedLayout>
</template>