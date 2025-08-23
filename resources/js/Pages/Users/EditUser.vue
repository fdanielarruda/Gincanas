<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, InertiaForm } from '@inertiajs/vue3';
import UserForm from './UserForm.vue';

interface User {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
}

interface UserFormFields {
    name: string;
    email: string;
    is_active: boolean;
    password?: string;
    password_confirmation?: string;
}

const props = defineProps<{
    user: User;
}>();

const submit = (form: InertiaForm<UserFormFields>) => {
    form.put(route('users.update', props.user.id));
};
</script>

<template>
    <Head :title="`Editar Usuário: ${props.user.name}`" />

    <AuthenticatedLayout>
        <UserForm :initial-data="props.user" @form-submitted="submit">
            <template #buttonText>
                Atualizar Usuário
            </template>
        </UserForm>
    </AuthenticatedLayout>
</template>