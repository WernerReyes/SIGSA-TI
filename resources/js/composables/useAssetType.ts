import { PageConstKey } from '@/constants/pages.constant';
import { useAssetTypeStore } from '@/store/useAssetTypeStore';
import { router } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
const PREFIX = '/asset-types';

export const useAssetType = () => {
    const { assetTypesList, loadingAssetTypesList } =
        storeToRefs(useAssetTypeStore());
    const getAssetTypesList = (page: PageConstKey) => {
        if (assetTypesList.value.length > 0) return;
        loadingAssetTypesList.value = true;
        router.get(
            `${PREFIX}?component=${page}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                only: ['types', 'flash'],
                onFinish: () => {
                    loadingAssetTypesList.value = false;
                },
            },
        );
    };

    return {
        //* Props
        assetTypesList,
        loadingAssetTypesList,

        //* Functions
        getAssetTypesList,
    };
};
