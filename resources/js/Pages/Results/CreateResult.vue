<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ResultForm from './ResultForm.vue';

interface Gymkhana {
    id: number;
    title: string;
}

interface ResultFormFields {
    gymkhana_id: number | null;
}

const props = defineProps<{
    gymkhanas: Gymkhana[];
}>();

const form = useForm<ResultFormFields>({
    gymkhana_id: null,
});

const submit = () => {
    form.post(route('results.store'));
};
</script>

<template>
    <Head title="Adicionar Resultado" />

    <AuthenticatedLayout>
        <ResultForm :form="form" :gymkhanas="props.gymkhanas" @form-submitted="submit">
            <template #buttonText>
                Iniciar resultados
            </template>
        </ResultForm>
    </AuthenticatedLayout>
</template>
