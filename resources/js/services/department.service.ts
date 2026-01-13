import type { PageConstKey } from '@/constants/pages.constant';
import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';


export const getAllBasicDepartmentInfo = (page:PageConstKey, onSuccess?: (page: Page<PageProps>) => void) => {
    return router.get(
        `departments/basic-info?component=${page}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['departments', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
}