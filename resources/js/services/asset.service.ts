import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

export const getAssetDetails = (
    id: number,
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `/assets/${id}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['asset','flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};

export const getAssetHistories = (
    id: number,
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `/assets/${id}/histories`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['asset', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};

