import { Asset } from '@/interfaces/asset.interface';
import { type AssetType } from '@/interfaces/assetType.interface';
import { type Department, DepartmentAllowed } from '@/interfaces/department.interace';
import { SlaPolicy } from '@/interfaces/slaPolicy.interface';
import { UserCharge, type User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
import { useEchoModel } from '@laravel/echo-vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';

export const useApp = () => {
    const isLoading = ref(false);
    const page = usePage();
    const userAuth = computed(() => (page.props as unknown as { auth: { user: User } })?.auth?.user);
    const users = computed(() => (page.props as unknown as { users: User[] })?.users || []);
    const availableAssets = computed(() => (page.props as unknown as { availableAssets: Asset[] })?.availableAssets || []);
    const TIUsers = computed(() => (page.props as unknown as { TIUsers: User[] })?.TIUsers || []);
    const departments = computed(() => (page.props as unknown as { departments: Department[] })?.departments || []);
    const assetTypes = computed(() => (page.props as unknown as { types: AssetType[] })?.types || []);
    const assetAccessories = computed(() => (page.props as unknown as { accessories: Asset[] })?.accessories || []);

    const slaPolicies = computed(() => (page.props as unknown as { slaPolicies: SlaPolicy[] })?.slaPolicies || []);

    const echo = useEchoModel(
    'App.Models.User',
    userAuth.value.staff_id,
)


    const isFromTI = computed(() => {
        return userAuth.value?.dept_id === DepartmentAllowed.SYSTEM_TI;
    });

    const isFromRRHH = computed(() => {
        return userAuth.value?.dept_id === DepartmentAllowed.RRHH;
    });

    const isTIAssistantManager = computed(() => {
        return isFromTI.value && userAuth.value?.id_cargo === UserCharge.IT_ASSISTANT_MANAGER;
    });

    const isTIManager = computed(() => {
        return isFromTI.value && userAuth.value?.id_cargo === UserCharge.IT_MANAGER;
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

    return { isLoading, userAuth, isFromTI, isFromRRHH, isSameUser, users, TIUsers, assetAccessories, slaPolicies,
        availableAssets,
        departments, assetTypes, isTIManager, isTIAssistantManager, echo };
};
