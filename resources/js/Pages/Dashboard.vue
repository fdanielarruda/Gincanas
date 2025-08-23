<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <p>Bem-vindo!</p>
    </AuthenticatedLayout>
</template>