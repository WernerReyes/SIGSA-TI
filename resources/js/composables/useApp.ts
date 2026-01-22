import { DepartmentAllowed } from '@/interfaces/department.interace';
import type { User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

export const useApp = () => {
    const isLoading = ref(false);
    const page = usePage();
    const userAuth = computed(() => (page.props as unknown as { auth: { user: User } })?.auth?.user);
    const users = computed(() => (page.props as unknown as { users: User[] })?.users || []);
    const TIUsers = computed(() => (page.props as unknown as { TIUsers: User[] })?.TIUsers || []);

    const isFromTI = computed(() => {
        return userAuth.value?.dept_id === DepartmentAllowed.SYSTEM_TI;
    });

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

    

    const isSameUser = (userId: number | undefined) => {
        return userAuth.value?.staff_id === userId;
    };

    return { isLoading, userAuth, isFromTI, isSameUser, users, TIUsers };
};
