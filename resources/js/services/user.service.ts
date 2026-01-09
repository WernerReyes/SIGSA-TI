import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

export const getAllUsers = (prefix:string = '', onSuccess?: (page: Page<PageProps>) => void) => {
    return router.get(
        `${prefix}/users`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['users', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};
