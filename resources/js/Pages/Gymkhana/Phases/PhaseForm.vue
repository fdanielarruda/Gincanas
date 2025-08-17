<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';
import { TrashIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            title: '',
            description: '',
            criteria: [''],
        }),
    },
    gymkhanaId: {
        type: Number,
        required: true,
    }
});

const emit = defineEmits(['form-submitted']);

const form = useForm({
    title: props.initialData.title,
    description: props.initialData.description,
    criteria: props.initialData.criteria,
});

const addCriterion = () => {
    form.criteria.push('');
};

const removeCriterion = (index: number) => {
    if (form.criteria.length > 1) {
        form.criteria.splice(index, 1);
    }
};

const submit = () => {
    emit('form-submitted', form);
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="space-y-6">
            <div>
                <InputLabel for="title" value="Título da Fase" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel for="description" value="Descrição da Fase" />
                <textarea id="description"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    v-model="form.description" rows="4"></textarea>
                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div>
                <InputLabel value="Critérios de Avaliação" />
                <div v-for="(criterion, index) in form.criteria" :key="index" class="flex items-center gap-2 mt-2">
                    <TextInput :id="'criterion-' + index" type="text" class="block w-full"
                        v-model="form.criteria[index]" :placeholder="'Critério ' + (index + 1)" />

                    <button type="button" @click="removeCriterion(index)"
                        class="text-red-500 hover:text-red-700 font-bold" :disabled="form.criteria.length <= 1">
                        <TrashIcon class="h-5 w-5" />
                    </button>

                    <InputError class="mt-2" :message="form.errors[`criteria.${index}`]" />
                </div>

                <button type="button" @click="addCriterion" class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Critério
                </button>
                <InputError class="mt-2" :message="form.errors.criteria" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <TextButton :href="route('gymkhana.phases.index', gymkhanaId)" class="p-4 mr-2" color="gray">
                Voltar
            </TextButton>

            <TextButton :disabled="form.processing" class="p-4">
                <slot name="buttonText">Salvar</slot>
            </TextButton>
        </div>
    </form>
</template>