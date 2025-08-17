import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useDeleter<T extends { id: number }>(routeName: string, routeParams: (item: T) => any[]) {
    const confirmingDeletion = ref(false);
    const itemToDelete = ref<T | null>(null);

    const openConfirmModal = (item: T) => {
        itemToDelete.value = item;
        confirmingDeletion.value = true;
    };

    const closeConfirmModal = () => {
        confirmingDeletion.value = false;
        itemToDelete.value = null;
    };

    const performDeletion = () => {
        if (!itemToDelete.value) {
            return;
        }

        const params = routeParams(itemToDelete.value);

        router.delete(route(routeName, params), {
            preserveScroll: true,
            onSuccess: () => closeConfirmModal(),
        });
    };

    return {
        confirmingDeletion,
        itemToDelete,
        openConfirmModal,
        closeConfirmModal,
        performDeletion,
    };
}