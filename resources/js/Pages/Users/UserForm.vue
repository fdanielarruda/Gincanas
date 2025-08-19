<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';

const TYPE_JUDGE = 2;

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            name: '',
            email: '',
            type: TYPE_JUDGE,
            is_active: true
        }),
    },
});

const emit = defineEmits(['form-submitted']);

const form = useForm({
    name: props.initialData.name,
    email: props.initialData.email,
    is_active: props.initialData.is_active,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    emit('form-submitted', form);
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="space-y-3">
            <div>
                <InputLabel for="name" value="Nome do Juiz" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail do Juiz" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex space-x-4">
                <div class="flex-1">
                    <InputLabel for="password" value="Senha" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex-1">
                    <InputLabel for="password_confirmation" value="Confirme a Senha" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                        v-model="form.password_confirmation" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="mt-4">
                <div class="flex items-center mt-2">
                    <input type="checkbox" id="is_active" v-model="form.is_active"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Ativo
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.is_active" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <TextButton :href="route('users.index')" class="p-4 mr-2" color="gray">
                Voltar
            </TextButton>

            <TextButton :disabled="form.processing" class="p-4">
                <slot name="buttonText">Salvar</slot>
            </TextButton>
        </div>
    </form>
</template>