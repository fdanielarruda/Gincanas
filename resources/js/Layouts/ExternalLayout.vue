<script setup lang="ts">
import { ref } from 'vue';

const isDark = ref(false);

function applyTheme(theme: string) {
    if (theme === 'dark') {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
        isDark.value = true;
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
        isDark.value = false;
    }
}

if (localStorage.getItem("theme") === "dark") {
    applyTheme("dark");
} else {
    applyTheme("light");
}
</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen flex">
        <main class="flex-1 overflow-auto p-4">
            <slot />
        </main>
    </div>
</template>