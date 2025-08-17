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
    criteria: string[];
}

interface PhaseFormFields {
    title: string;
    description: string;
    criteria: Array<string>;
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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <PhaseForm :initial-data="props.phase" :gymkhana-id="gymkhana.id" @form-submitted="submit">
                            <template #buttonText>
                                Atualizar Fase
                            </template>
                        </PhaseForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>