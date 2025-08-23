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
        <main class="flex-1 overflow-auto p-2">
            <div class="py-6">
                <div class="mx-auto">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-2 lg:p-4 py-4 text-gray-900 dark:text-gray-100">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>