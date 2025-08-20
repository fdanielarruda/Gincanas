// resources/js/types/inertia.d.ts

import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { Errors, Page, PageProps } from '@inertiajs/vue3';

// Defina a estrutura das suas mensagens flash
interface FlashMessages {
    success?: string;
    error?: string;
    message?: string;
    // Adicione outras mensagens flash que você possa ter
}

// Defina a estrutura do seu usuário autenticado (se você compartilhar)
interface User {
    id: number;
    name: string;
    email: string;
    type: number;
    // Adicione outras propriedades do usuário
}

// Combine todas as suas props personalizadas aqui
// Esta interface estende PageProps e inclui suas props específicas
interface CustomPageProps extends PageProps {
    auth: {
        user: User;
    };
    flash: FlashMessages;
    // Adicione qualquer outra prop que você compartilha globalmente via HandleInertiaRequests
}

// Sobrescreva a declaração de tipo PageProps do Inertia
declare module '@inertiajs/vue3' {
    // eslint-disable-next-line @typescript-eslint/no-empty-interface
    export interface PageProps extends CustomPageProps { }
}

// Opcional: Se você estiver usando o @inertiajs/core diretamente em algum lugar
declare module '@inertiajs/core' {
    // eslint-disable-next-line @typescript-eslint/no-empty-interface
    export interface PageProps extends CustomPageProps { }
}

// Se você estiver usando `Ziggy` para as rotas e quer tipar `route()`
declare global {
    var route: typeof ZiggyRoute;
}