import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

export const getAllDepartments = (prefix:string = '', onSuccess?: (page: Page<PageProps>) => void) => {
    return router.get(
        `${prefix}/departments`,
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
};
