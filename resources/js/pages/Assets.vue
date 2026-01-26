<template>

    <Head title="Activos TI" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <!-- Botón flotante de alerta -->
        <Teleport to="body" v-if="generalAlert">
            <!-- <div class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-2">
                <button
                    class="relative flex h-12 w-12 items-center justify-center rounded-full bg-slate-800 text-white shadow-lg transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    @click="showAlertPanel = !showAlertPanel"
                    :aria-expanded="showAlertPanel"
                    aria-label="Ver alerta de accesorios"
                >
                    <Bell class="size-5" />
                    <span
                        class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-white text-slate-700 text-[10px] font-semibold px-1.5 py-0.5 shadow"
                    >!</span>
                </button>

                <div
                    v-if="showAlertPanel"
                    class="w-80 rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-4 shadow-2xl"
                >
                    <div class="flex items-start gap-3">
                        <div class="size-10 rounded-xl bg-slate-100 dark:bg-slate-800 grid place-content-center">
                            <AlertCircle class="size-5 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Accesorios sin stock</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 line-clamp-2">
                                {{ generalAlert?.message || 'Correo enviado a Ventas indicando faltante de accesorios.' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Se notificó a Ventas para reabastecer.</p>
                            <div class="mt-2 flex items-center gap-2 text-[11px] text-muted-foreground">
                                <span>Última notificación: 
                                        {{ format(new Date(generalAlert?.last_notified_at), 'dd/MM/yyyy HH:mm') }}

                                </span>
                            </div>
                            <div class="mt-2">
                                <Badge :class="isAlertActive ? 'bg-slate-800 text-white' : 'bg-emerald-600 text-white'">
                                    {{ isAlertActive ? 'Activa' : 'Resuelta' }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-2">
                        <button class="px-3 py-1.5 text-xs rounded-md border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" @click="handleResendAlert" :disabled="!isAlertActive">Reenviar a Ventas</button>
                        <button class="px-3 py-1.5 text-xs rounded-md bg-slate-800 text-white hover:bg-slate-900" @click="handleResolveAlert" :disabled="!isAlertActive">Marcar como resuelta</button>
                    </div>
                </div>
            </div> -->
            <AlertAccessoryOutStock  />
        </Teleport>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <Dialog includeButton v-model:current-asset="currentAsset" v-model:open-editor="openEditor" />

            <!-- Contenido principal -->
            <div class="space-y-4">
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <Card v-for="stat in statsView" :key="stat.label"
                        class="group relative overflow-hidden rounded-xl border bg-lineal-to-br from-background via-background to-muted/40 shadow-card transition hover:-translate-y-0.5 hover:shadow-2xl">

                        <div class="pointer-events-none absolute inset-0 opacity-50" :class="stat.bg"></div>
                        <div class="pointer-events-none absolute inset-0 bg-lineal-to-br from-white/60 via-transparent to-white/10 dark:from-white/5"></div>

                        <CardContent class="relative space-y-4 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-xl flex items-center justify-center ring-2 ring-white/40 shadow-sm"
                                        :class="stat.bg">
                                        <component :is="stat.icon" class="size-5" :class="stat.color" />
                                    </div>
                                    <div class="flex flex-col">
                                        <CardTitle class="text-sm font-semibold text-foreground">
                                            {{ stat.label }}
                                        </CardTitle>
                                        <span class="text-xs text-muted-foreground">Indicador actualizado</span>
                                    </div>
                                </div>
                                <DropdownMenu v-if="stat.label === statusOp.label">
                                    <DropdownMenuTrigger as-child>
                                        <button class="p-1.5 rounded-lg hover:bg-muted transition" title="Cambiar estado">
                                            <EllipsisVertical class="size-4 text-muted-foreground" />
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="w-44" align="end">
                                        <DropdownMenuLabel>Estados</DropdownMenuLabel>
                                        <DropdownMenuItem v-for="option in Object.values(assetStatusOptions)"
                                            :key="option.value" @click="statusOp = option"
                                            class="flex items-center gap-2">
                                            <Badge :class="option.bg">
                                                <component :is="option.icon" class="size-4" />
                                                {{ option.label }}
                                                <Check v-if="statusOp.value === option.value" class="ml-auto" />
                                            </Badge>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                            <div class="flex items-end justify-between">
                                <span class="text-4xl font-black leading-none tracking-tight" :class="stat.color">
                                    {{ stat.value }}
                                </span>
                                <span class="text-xs text-muted-foreground group-hover:text-foreground transition">Estadística</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Table :assets="assetsPaginated" />

                <DialogDetails v-model:open="openDetails" v-model:asset="currentAsset" />
            </div>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import Dialog from '@/components/assets/Dialog.vue';
import DialogDetails from '@/components/assets/DialogDetails.vue';
import Table from '@/components/assets/Table.vue';
import type { Alert } from '@/interfaces/alert.interface';
import type { AssetStats, AssetStatusOption } from '@/interfaces/asset.interface';
import { AssetStatus, assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Paginated } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import AlertAccessoryOutStock from '@/components/assets/AlertAccessoryOutStock.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Boxes, Check, EllipsisVertical, ShieldCheck, ShieldX } from 'lucide-vue-next';

const { assetsPaginated, stats } = defineProps<{ assetsPaginated: Paginated<Asset>, stats: AssetStats }>();

const currentAsset = ref<Asset | null>(null);
const openDetails = ref(false);
const openEditor = ref(false);
const statusOp = ref<AssetStatusOption>(assetStatusOptions[AssetStatus.ASSIGNED]);

// Alerta general a partir de props

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos TI',
        href: "#"
    },
];

const generalAlert = computed(() => {
    const p = usePage().props as any;
    return (p.accessoriesOutOfStockAlert || null) as Alert | null;
});


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
]);


</script>