<script setup lang="ts">
import { onMounted, ref } from 'vue';

const props = defineProps<{
    modelValue: string | number | null;
    id?: string;
    disabled?: boolean;
    required?: boolean;
}>();

defineEmits(['update:modelValue']);

const input = ref<HTMLSelectElement | null>(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <select
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue" :id="id" :disabled="disabled" :required="required"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)" ref="input">
        <slot />
    </select>
</template>