<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import GynkhanaForm from './GynkhanaForm.vue';

interface Gymkhana {
    id: number;
    title: string;
}

interface GymkhanaFormFields {
    title: string;
    start_date: string;
}

const props = defineProps<{
    gymkhana: Gymkhana;
}>();

const submit = (form: InertiaForm<GymkhanaFormFields>) => {
    form.put(route('gymkhana.update', props.gymkhana.id));
};
</script>

<template>

    <Head title="Editar gincana" />

    <AuthenticatedLayout>
        <GynkhanaForm :initial-data="gymkhana" @form-submitted="submit">
            <template #buttonText>
                Atualizar Gincana
            </template>
        </GynkhanaForm>
    </AuthenticatedLayout>
</template>