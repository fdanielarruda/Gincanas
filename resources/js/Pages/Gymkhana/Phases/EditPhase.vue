<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import PhaseForm from './PhaseForm.vue';

interface Gymkhana {
    id: number;
}

interface Phase {
    id: number;
    title: string;
    description: string;
    type: number;
    criteria?: string[];
    colocations?: Array<{ place: string, points: number }>;
}

interface PhaseFormFields {
    title: string;
    description: string;
    type: number;
    criteria?: Array<string>;
    colocations?: Array<{ place: string, points: number }>;
}

const props = defineProps<{
    gymkhana: Gymkhana;
    phase: Phase;
}>();

const submit = (form: InertiaForm<PhaseFormFields>) => {
    form.put(route('gymkhana.phases.update', [props.gymkhana.id, props.phase.id]));
};
</script>

<template>

    <Head :title="`Editar Fase: ${props.phase.title}`" />

    <AuthenticatedLayout>
        <PhaseForm :initial-data="props.phase" :gymkhana-id="gymkhana.id" @form-submitted="submit">
            <template #buttonText>
                Atualizar Fase
            </template>
        </PhaseForm>
    </AuthenticatedLayout>
</template>