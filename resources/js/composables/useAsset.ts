import { useAssetStore } from '@/store/useAssetStore';
import { router } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { effect, watch } from 'vue';
const PREFIX = '/assets';


type GetAssetsListFiltered = {
    preserveUrl?: boolean;
}

export const useAsset = () => {
    const { assetsListFiltered, filters, assetListMountedOnce } = storeToRefs(useAssetStore());

    const getAssetsListFiltered = ({
        preserveUrl = false,
    } : GetAssetsListFiltered = {}) => {
        if (!assetsListFiltered.value) {
            return [];
        }
        router.get(
            assetsListFiltered.value.path,
            {
                ...filters.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                preserveUrl: true,
                
                only: ['assetsPaginated', 'assetFilters', 'flash'],
            },
        );
    };

    // watch(
    //     filters,
    //     (current, old) => {
    //         console.log('Watching filters...', current, old);
    //         if (JSON.stringify(current) === JSON.stringify(old)) {
    //             return;
    //         }
    //         console.log('Filters changed:', filters.value);
    //         // getAssetsListFiltered();
    //     },
    //     { deep: true },
    // );

    // effect(() => {
    //     console.log('Effect triggered for filters:', filters.value);
    //     // getAssetsListFiltered();
    // }, );

    return {
        //* Refs
        assetsListFiltered,
        assetsFilters: filters,
        assetListMountedOnce,

        //* Methods
        getAssetsListFiltered,
    };
};
