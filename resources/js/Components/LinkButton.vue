<script setup>
import { computed } from 'vue';

const props = defineProps({
    link: {
        type: String,
        required: true,
        default: '#'
    },
    color: {
        type: String,
        default: 'indigo'
    },
    title: {
        type: String,
        default: ''
    }
});

const baseButtonClasses = computed(() => `
    inline-flex items-center border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest
    focus:outline-none disabled:opacity-25 transition ease-in-out duration-150
    bg-indigo-600 hover:bg-indigo-700 border-indigo-600 text-white px-1 py-1
`);
</script>

<template>
    <a :href="link" target="_blank" :class="baseButtonClasses" :title="title">
        <slot />
    </a>
</template>