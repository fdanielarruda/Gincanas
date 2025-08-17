<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Gymkhana {
    id: number;
    title: string;
}

interface Judge {
    id: number;
    name: string;
}

const props = defineProps<{
    gymkhana: Gymkhana;
    availableJudges: Judge[];
}>();

const form = useForm({
    judge_ids: [],
});

const submit = () => {
    form.post(route('gymkhana.judges.store', props.gymkhana.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>

    <Head title="Adicionar Juiz" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Adicionar Juiz(es) à
                            Gincana: "{{ gymkhana.title }}"</h2>

                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="judges" value="Selecione os Juízes" />
                                <select id="judges" multiple v-model="form.judge_ids"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    size="10">
                                    <option v-for="judge in availableJudges" :key="judge.id" :value="judge.id">
                                        {{ judge.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.judge_ids" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <TextButton :href="route('gymkhana.judges.index', gymkhana.id)" class="p-4 mr-2"
                                    color="gray">
                                    Voltar
                                </TextButton>
                                <TextButton :disabled="form.processing" class="p-4">
                                    Salvar Juiz(es)
                                </TextButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>