<template>

    <Head title="Activos TI" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <Dialog includeButton v-model:current-asset="currentAsset" v-model:open-editor="openEditor" />


            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">

                <Card v-for="stat in statsView" :key="stat.label"
                    class="rounded-xl border shadow-card transition hover:shadow-lg">
                    <CardContent>
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <!-- Icon -->
                                <div class="size-8 rounded-lg flex items-center justify-center" :class="stat.bg">
                                    <component :is="stat.icon" class="size-5" :class="stat.color" />
                                </div>

                                <CardTitle class="text-sm font-medium text-muted-foreground">
                                    {{ stat.label }}
                                </CardTitle>
                            </div>

                            <!-- Dropdown -->
                            <DropdownMenu v-if="stat.label === statusOp.label">
                                <DropdownMenuTrigger as-child>
                                    <button class="p-1 rounded-md hover:bg-muted transition">
                                        <EllipsisVertical class="size-4 text-muted-foreground" />
                                    </button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent class="w-40" align="end">
                                    <DropdownMenuLabel>Estados</DropdownMenuLabel>
                                    <DropdownMenuItem v-for="option in Object.values(assetStatusOptions)"
                                        :key="option.value" @click="statusOp = option" class="flex items-center gap-2">
                                        <Badge
                                         
                                        :class="option.bg">
                                            <component :is="option.icon" class="size-4" />
                                            {{ option.label }}
                                            <Check v-if="statusOp.value === option.value" class="ml-auto" />
                                        </Badge>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!-- Value -->
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-bold tracking-tight" :class="stat.color">
                                {{ stat.value }}
                            </span>
                        </div>
                    </CardContent>
                </Card>

            </div>

            <Table :assets="assetsPaginated" />

            <DialogDetails v-model:open="openDetails" v-model:asset="currentAsset" />
        </div>

    </AppLayout>
</template>

<script setup lang="ts">
import Dialog from '@/components/assets/Dialog.vue';
import DialogDetails from '@/components/assets/DialogDetails.vue';
import Table from '@/components/assets/Table.vue';
import type { AssetsPaginated, AssetStats, AssetStatusOption } from '@/interfaces/asset.interface';
import { AssetStatus, assetStatusOptions, type Asset, type AssetType } from '@/interfaces/asset.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Boxes, Check, EllipsisVertical, ShieldCheck, ShieldX } from 'lucide-vue-next';

const { stats } = defineProps<{ types: AssetType[], assets: Asset[], assetsPaginated: AssetsPaginated, stats: AssetStats }>();

const currentAsset = ref<Asset | null>(null);

const openDetails = ref(false);
const openEditor = ref(false);
const statusOp = ref<AssetStatusOption>(assetStatusOptions[AssetStatus.ASSIGNED]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos TI',
        href: "#"
    },

];

const statsView = computed(() => [
    {
        label: 'Total Activos',
        value: stats.total,
        icon: Boxes,
        color: 'text-slate-900 dark:text-slate-100',
        bg: 'bg-slate-100 dark:bg-slate-800',
    },
    {
        label: 'Garantías Vigentes',
        value: stats.not_expired_warranty,
        icon: ShieldCheck,
        color: 'text-emerald-600 dark:text-emerald-400',
        bg: 'bg-emerald-100 dark:bg-emerald-900/30',
    },
    {
        label: statusOp.value.label,
        value: stats.statuses[statusOp.value.value] || 0,
        icon: statusOp.value.icon,
        color: 'text-amber-600 dark:text-amber-400',
        bg: 'bg-amber-100 dark:bg-amber-900/30',
    },
    {
        label: 'Garantías Vencidas',
        value: stats.expired_warranty,
        icon: ShieldX,
        color: 'text-rose-600 dark:text-rose-400',
        bg: 'bg-rose-100 dark:bg-rose-900/30',
    },
])
</script>