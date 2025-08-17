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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <GynkhanaForm :initial-data="gymkhana" @form-submitted="submit">
                            <template #buttonText>
                                Atualizar Gincana
                            </template>
                        </GynkhanaForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>