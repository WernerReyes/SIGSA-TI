import type { PageConstKey } from '@/constants/pages.constant';
import { useDepartmentStore } from '@/store/useDeparmentStore';
import { router } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';

export const useDepartment = () => {
    const { departmentsList, loadingDepartmentsList } =
        storeToRefs(useDepartmentStore());

    const getAllBasicDepartmentInfo = (page: PageConstKey) => {
        if (departmentsList.value.length > 0) {
            return;
        }
        loadingDepartmentsList.value = true;

        return router.get(
            `departments/basic-info?component=${page}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ['departments', 'flash'],
                onFinish: () => {
                    loadingDepartmentsList.value = false;
                },
            },
        );
    };

    return {
        //* Refs
        departmentsList,
        loadingDepartmentsList,

        //* Methods
        getAllBasicDepartmentInfo,
    };
};
