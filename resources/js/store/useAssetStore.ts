import { defineStore } from 'pinia';

export const useAssetStore = defineStore('asset', {
    state: () => ({
        accessoriesLoaded: false,
        refetchAccessories: false,
    }),

    // actions: {
    //     setA
    // },
});
