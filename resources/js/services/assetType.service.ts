import type { PageConstKey } from '@/constants/pages.constant';
import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

const PREFIX = '/asset-types';

export const getAssetTypes = (
    page: PageConstKey,
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `${PREFIX}?component=${page}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['types', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};
