<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import UserForm from './UserForm.vue';

interface UserFormFields {
    name: string;
    email: string;
    is_active: boolean;
    password?: string;
    password_confirmation?: string;
}

const submit = (form: InertiaForm<UserFormFields>) => {
    form.post(route('users.store'));
};
</script>

<template>
    <Head title="Adicionar Usuário" />

    <AuthenticatedLayout>
        <UserForm @form-submitted="submit">
            <template #buttonText>
                Salvar Usuário
            </template>
        </UserForm>
    </AuthenticatedLayout>
</template>