<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits } from 'vue';
import { TrashIcon } from '@heroicons/vue/24/solid';

const TYPE_CRITERIA = 1;
const TYPE_QUIZ = 2;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4;

interface Colocation {
    place: string;
    points: number | string;
}

interface ChecklistColocation {
    place: string;
    points: number | string;
}

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({
            title: '',
            description: '',
            type: TYPE_CRITERIA,
            criteria: [''],
            colocations: [],
            checklist_colocations: [],
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
    type: props.initialData.type,
    criteria: props.initialData.criteria,
    colocations: props.initialData.colocations as Colocation[],
    checklist_colocations: props.initialData.checklist_colocations as ChecklistColocation[],
});

const addRow = (type: string) => {
    if (type === 'criteria') {
        form.criteria.push('');
    } else if (type === 'colocation') {
        form.colocations.push({ place: '', points: '' });
    } else if (type === 'checklist_colocation') {
        form.checklist_colocations.push({ place: '', points: '' });
    }
};

const removeRow = (type: string, index: number) => {
    if (type === 'criteria' && form.criteria.length > 1) {
        form.criteria.splice(index, 1);
    } else if (type === 'colocation' && form.colocations.length > 0) {
        form.colocations.splice(index, 1);
    } else if (type === 'checklist_colocation' && form.checklist_colocations.length > 0) {
        form.checklist_colocations.splice(index, 1);
    }
};

const getPlaceText = (index: number) => {
    return `${index + 1}º lugar`;
};

const submit = () => {
    if (form.type == TYPE_COLOCATION) {
        form.colocations.forEach((item, index) => {
            item.place = getPlaceText(index);
        });
    } else if (form.type == TYPE_CHECKLIST) {
        form.checklist_colocations.forEach((item, index) => {
            item.place = getPlaceText(index);
        });
    }

    emit('form-submitted', form);
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="space-y-3">
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
                <InputLabel for="type" value="Tipo de Fase" />
                <SelectInput id="type" v-model="form.type" class="mt-1 block w-full">
                    <option :value="TYPE_CRITERIA">Critérios</option>
                    <option :value="TYPE_COLOCATION">Colocação</option>
                    <option :value="TYPE_CHECKLIST">Checklist</option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div v-if="form.type == TYPE_CRITERIA">
                <InputLabel value="Critérios de Avaliação" />
                <div v-for="(criterion, index) in form.criteria" :key="index" class="flex items-center gap-2 mt-2">
                    <TextInput :id="'criterion-' + index" type="text" class="block w-full"
                        v-model="form.criteria[index]" :placeholder="`Critério ${index + 1}`" />
                    <button type="button" @click="removeRow('criteria', index)"
                        class="text-red-500 hover:text-red-700 font-bold" :disabled="form.criteria.length <= 1">
                        <TrashIcon class="h-5 w-5" />
                    </button>
                    <InputError class="mt-2" :message="form.errors[`criteria.${index}`]" />
                </div>
                <button type="button" @click="addRow('criteria')"
                    class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Critério
                </button>
                <InputError class="mt-2" :message="form.errors.criteria" />
            </div>

            <div v-if="form.type == TYPE_COLOCATION">
                <InputLabel value="Colocação e Pontos" />
                <div v-for="(colocation, index) in form.colocations" :key="index" class="flex items-center gap-2 mt-2">
                    <div
                        class="block w-1/2 p-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm text-left">
                        {{ getPlaceText(index) }}
                    </div>
                    <TextInput :id="`colocation-points-${index}`" type="number" class="block w-1/2"
                        v-model="form.colocations[index].points" placeholder="Pontos" />
                    <button type="button" @click="removeRow('colocation', index)"
                        class="text-red-500 hover:text-red-700 font-bold">
                        <TrashIcon class="h-5 w-5" />
                    </button>
                    <InputError class="mt-2" :message="form.errors[`colocations.${index}.points`]" />
                </div>
                <button type="button" @click="addRow('colocation')"
                    class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Colocação
                </button>
                <InputError class="mt-2" :message="form.errors.colocations" />
            </div>

            <div v-if="form.type == TYPE_CHECKLIST">
                <InputLabel value="Atributo e pontos" />
                <div v-for="(colocation, index) in form.colocations" :key="index" class="flex items-center gap-2 mt-2">
                    <TextInput :id="`colocation-place-${index}`" type="text" class="block w-1/2"
                        v-model="form.colocations[index].place" placeholder="Atributo (ex: Arroz)" />
                    <TextInput :id="`colocation-points-${index}`" type="number" class="block w-1/2"
                        v-model="form.colocations[index].points" placeholder="Pontos" />
                    <button type="button" @click="removeRow('colocation', index)"
                        class="text-red-500 hover:text-red-700 font-bold">
                        <TrashIcon class="h-5 w-5" />
                    </button>
                    <InputError class="mt-2" :message="form.errors[`colocations.${index}.place`]" />
                    <InputError class="mt-2" :message="form.errors[`colocations.${index}.points`]" />
                </div>
                <button type="button" @click="addRow('colocation')"
                    class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Atributo
                </button>
                <InputError class="mt-2" :message="form.errors.colocations" />

                <hr class="my-4">

                <InputLabel value="Pontuação por Colocação" />
                <div v-for="(checklist_colocation, index) in form.checklist_colocations" :key="index"
                    class="flex items-center gap-2 mt-2">
                    <div
                        class="block w-1/2 p-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm text-left">
                        {{ getPlaceText(index) }}
                    </div>
                    <TextInput :id="`checklist-colocation-points-${index}`" type="number" class="block w-1/2"
                        v-model="form.checklist_colocations[index].points" placeholder="Pontos" />
                    <button type="button" @click="removeRow('checklist_colocation', index)"
                        class="text-red-500 hover:text-red-700 font-bold">
                        <TrashIcon class="h-5 w-5" />
                    </button>
                    <InputError class="mt-2" :message="form.errors[`checklist_colocations.${index}.points`]" />
                </div>
                <button type="button" @click="addRow('checklist_colocation')"
                    class="mt-2 text-blue-500 hover:text-blue-700 font-bold">
                    + Adicionar Pontuação por Colocação
                </button>
                <InputError class="mt-2" :message="form.errors.checklist_colocations" />
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