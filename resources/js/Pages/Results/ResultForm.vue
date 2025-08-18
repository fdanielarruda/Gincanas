<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3';

interface Gymkhana {
    id: number;
    title: string;
}

interface ResultFormFields {
    gymkhana_id: number | null;
}

interface ResultFormProps {
    form: InertiaForm<ResultFormFields>;
    gymkhanas: Gymkhana[];
}

defineProps<ResultFormProps>();

const emit = defineEmits<{
    (e: 'form-submitted'): void;
}>();
</script>

<template>
    <form @submit.prevent="() => emit('form-submitted')">
        <div class="mb-4">
            <label for="gymkhana_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Gincana
            </label>
            <select
                id="gymkhana_id"
                v-model="form.gymkhana_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
            >
                <option :value="null" disabled>Selecione uma gincana</option>
                <option v-for="gymkhana in gymkhanas" :key="gymkhana.id" :value="gymkhana.id">
                    {{ gymkhana.title }}
                </option>
            </select>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <slot name="buttonText" />
            </button>
        </div>
    </form>
</template>