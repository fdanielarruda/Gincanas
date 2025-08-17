<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';
import { TrashIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            title: '',
            participants: [''],
        }),
    },
});

const emit = defineEmits(['form-submitted']);

const form = useForm({
    title: props.initialData.title,
    participants: props.initialData.participants,
});

const addParticipant = () => {
    form.participants.push('');
};

const removeParticipant = (index: number) => {
    if (form.participants.length > 1) {
        form.participants.splice(index, 1);
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
                <InputLabel for="title" value="Título da Equipe" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel value="Participantes" />
                <div v-for="(participant, index) in form.participants" :key="index"
                    class="flex items-center gap-2 mt-2">
                    <TextInput :id="'participant-' + index" type="text" class="block w-full"
                        v-model="form.participants[index]" :placeholder="'Participante ' + (index + 1)" />

                    <button type="button" @click="removeParticipant(index)"
                        class="text-red-500 hover:text-red-700 font-bold" :disabled="form.participants.length <= 1">
                        <TrashIcon class="h-5 w-5" />
                    </button>

                    <InputError class="mt-2" :message="form.errors[`participants.${index}`]" />
                </div>

                <button type="button" @click="addParticipant" class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Participante
                </button>
                <InputError class="mt-2" :message="form.errors.participants" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <slot name="buttonText">Salvar</slot>
            </PrimaryButton>
        </div>
    </form>
</template>