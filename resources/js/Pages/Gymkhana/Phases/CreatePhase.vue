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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <PhaseForm :gymkhana-id="gymkhana.id" @form-submitted="submit">
                            <template #buttonText>
                                Salvar Fase
                            </template>
                        </PhaseForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>