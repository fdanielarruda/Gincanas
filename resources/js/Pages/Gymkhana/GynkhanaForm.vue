<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { formatForInput } from '@/Utils/DateUtils';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            title: '',
            start_date: '',
        }),
    },
});

const emit = defineEmits(['form-submitted']);

const form = useForm({
    title: props.initialData.title,
    start_date: formatForInput(props.initialData.start_date),
});

const submit = () => {
    emit('form-submitted', form);
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="space-y-6">
            <div>
                <InputLabel for="title" value="Título da Gincana" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel for="start_date" value="Data de Início" />
                <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <TextButton :href="route('gymkhana.index')" class="p-4 mr-2" color="gray">
                Voltar
            </TextButton>

            <TextButton :disabled="form.processing" class="p-4">
                <slot name="buttonText">Salvar</slot>
            </TextButton>
        </div>
    </form>
</template>