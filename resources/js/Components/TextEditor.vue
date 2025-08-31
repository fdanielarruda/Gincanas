<script setup>
import { onMounted, ref, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = defineProps({
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
let quillInstance = null;

onMounted(() => {
    quillInstance = new Quill(editor.value, {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline']
            ]
        },
        placeholder: 'Escreva a descrição aqui...'
    });

    if (props.modelValue) {
        quillInstance.root.innerHTML = props.modelValue;
    }

    quillInstance.on('text-change', () => {
        emit('update:modelValue', quillInstance.root.innerHTML);
    });
});

watch(() => props.modelValue, (newValue) => {
    if (quillInstance && quillInstance.root.innerHTML !== newValue) {
        quillInstance.root.innerHTML = newValue;
    }
});
</script>

<template>
    <div>
        <div ref="editor"
            class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-300 rounded-md shadow-sm border border-gray-300 dark:border-gray-700 h-40">
        </div>
    </div>
</template>

<style scoped>
:deep(.ql-container.ql-snow) {
    height: calc(100% - 42px);
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

:deep(.ql-toolbar.ql-snow) {
    @apply bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-t-md;
}

:deep(.ql-toolbar.ql-snow .ql-stroke) {
    @apply stroke-gray-600 dark:stroke-gray-300;
}

:deep(.ql-toolbar.ql-snow .ql-picker-label) {
    @apply text-gray-800 dark:text-gray-300;
}
</style>