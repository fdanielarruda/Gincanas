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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <ResultForm :form="form" :gymkhanas="props.gymkhanas" @form-submitted="submit">
                            <template #buttonText>
                                Iniciar resultados
                            </template>
                        </ResultForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
