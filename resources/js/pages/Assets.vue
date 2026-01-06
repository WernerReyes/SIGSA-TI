<script setup lang="ts">
import Dialog from '@/components/assets/Dialog.vue';
import DialogDetails from '@/components/assets/DialogDetails.vue';
import Table from '@/components/assets/Table.vue';
import { type Asset, type AssetType } from '@/interfaces/asset.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { AssetsPaginated } from '../interfaces/asset.interface';

defineProps<{ types: AssetType[], assets: Asset[], assetsPaginated: AssetsPaginated }>();

const currentAsset = ref<Asset | null>(null);

const openDetails = ref(false);
const openEditor = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos TI',
        href: "#"
    },

];
</script>

<template>
    
    <Head title="Activos TI" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <!-- <pre>
    {{ assetsPaginated }}
        </pre> -->
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <Dialog includeButton v-model:current-asset="currentAsset" v-model:open-editor="openEditor" />

            <pre>

            </pre>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Total Activos</p>
                    <p class="text-2xl font-bold mt-1">6</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Activos</p>
                    <p class="text-2xl font-bold text-success mt-1">4</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">En Mantenimiento</p>
                    <p class="text-2xl font-bold text-warning mt-1">1</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Garantías Vencidas</p>
                    <p class="text-2xl font-bold text-critical mt-1">1</p>
                </div>
            </div>
            <!-- <div class="bg-card rounded-xl border shadow-card p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg><input
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10"
                            placeholder="Buscar por nombre, ID o serial..." value=""></div>
                    <div class="flex gap-2"><button type="button" role="combobox" aria-controls="radix-:r8v:"
                            aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed"
                            class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-[160px]"><span
                                style="pointer-events: none;">Todos los estados</span><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50"
                                aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg></button><button type="button" role="combobox" aria-controls="radix-:r90:"
                            aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed"
                            class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-[160px]"><span
                                style="pointer-events: none;">Todos los tipos</span><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50"
                                aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg></button><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-filter w-4 h-4">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg></button></div>
                </div>
            </div> -->


            <Table :assets="assetsPaginated" />

            <DialogDetails v-model:open="openDetails" v-model:asset="currentAsset" />
        </div>

    </AppLayout>
</template>