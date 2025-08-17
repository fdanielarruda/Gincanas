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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <GynkhanaForm @form-submitted="submit">
                            <template #buttonText>
                                Salvar Gincana
                            </template>
                        </GynkhanaForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>