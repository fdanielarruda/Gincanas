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
            participants: [''],
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
        <div class="space-y-3">
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
            <TextButton :href="route('gymkhana.teams.index', gymkhanaId)" class="p-4 mr-2" color="gray">
                Voltar
            </TextButton>

            <TextButton :disabled="form.processing" class="p-4">
                <slot name="buttonText">Salvar</slot>
            </TextButton>
        </div>
    </form>
</template>