import type { PageConstKey } from '@/constants/pages.constant';
import { useUserStore } from '@/store/useUserStore';
import { router } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';

export const useUser = () => {
    const { basicUsersInfo, loadingBasicUsersInfo } =
        storeToRefs(useUserStore());

    const getBasicUsersInfo = (page: PageConstKey) => {
        if (basicUsersInfo.value.length > 0) return;
        loadingBasicUsersInfo.value = true;
        
        router.get(
            `/users/basic-info?component=${page}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ['basicUsersInfo', 'flash'],
                onFinish: () => {
                    loadingBasicUsersInfo.value = false;
                    
                },
            },
        );
    };

    return {
        //* Props
        basicUsersInfo,
        loadingBasicUsersInfo,

        //* Functions
        getBasicUsersInfo,
    };
};
