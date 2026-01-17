import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

export const useApp = () => {
    const isLoading = ref(false);

    const start = () => (isLoading.value = true);
    const finish = () => (isLoading.value = false);

    let unsubscribeStart: (() => void) | null = null;
    let unsubscribeFinish: (() => void) | null = null;

    onMounted(() => {
        unsubscribeStart = router.on('start', start);
        unsubscribeFinish = router.on('finish', finish);
    });

    onUnmounted(() => {
        unsubscribeStart?.();
        unsubscribeFinish?.();
    });

    return { isLoading };
};
