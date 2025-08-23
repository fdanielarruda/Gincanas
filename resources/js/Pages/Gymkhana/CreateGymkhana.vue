<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import GynkhanaForm from './GynkhanaForm.vue';

interface GymkhanaFormFields {
    title: string;
}

const submit = (form: InertiaForm<GymkhanaFormFields>) => {
    form.post(route('gymkhana.store'));
};
</script>

<template>

    <Head title="Adicionar Gincana" />

    <AuthenticatedLayout>
        <GynkhanaForm @form-submitted="submit">
            <template #buttonText>
                Salvar Gincana
            </template>
        </GynkhanaForm>
    </AuthenticatedLayout>
</template>