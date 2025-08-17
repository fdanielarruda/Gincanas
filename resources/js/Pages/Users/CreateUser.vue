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
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <UserForm @form-submitted="submit">
                            <template #buttonText>
                                Salvar Usuário
                            </template>
                        </UserForm>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>