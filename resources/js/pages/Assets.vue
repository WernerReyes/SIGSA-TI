<template>

    <Head title="Activos TI" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <Dialog includeButton v-model:current-asset="currentAsset" v-model:open-editor="openEditor" />

            <!-- Tabs para cambiar entre Activos y Alertas -->
            <div class="flex gap-2 border-b">
                <button @click="activeTab = 'assets'" :class="[
                    'px-4 py-2 font-medium transition border-b-2',
                    activeTab === 'assets'
                        ? 'border-blue-600 text-blue-600'
                        : 'border-transparent text-muted-foreground hover:text-foreground'
                ]">
                    Activos TI
                </button>
                <button @click="activeTab = 'alerts'" :class="[
                    'px-4 py-2 font-medium transition border-b-2 flex items-center gap-2',
                    activeTab === 'alerts'
                        ? 'border-blue-600 text-blue-600'
                        : 'border-transparent text-muted-foreground hover:text-foreground'
                ]">
                    <AlertCircle class="size-4" />
                    Alertas de Accesorios
                    <Badge v-if="unreadAlerts > 0" class="ml-1 bg-rose-500">{{ unreadAlerts }}</Badge>
                </button>
            </div>

            <!-- Tab: Activos -->
            <div v-show="activeTab === 'assets'" class="space-y-4">
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <Card v-for="stat in statsView" :key="stat.label"
                        class="rounded-xl border shadow-card transition hover:shadow-lg">
                        <CardContent>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="size-8 rounded-lg flex items-center justify-center" :class="stat.bg">
                                        <component :is="stat.icon" class="size-5" :class="stat.color" />
                                    </div>
                                    <CardTitle class="text-sm font-medium text-muted-foreground">
                                        {{ stat.label }}
                                    </CardTitle>
                                </div>
                                <DropdownMenu v-if="stat.label === statusOp.label">
                                    <DropdownMenuTrigger as-child>
                                        <button class="p-1 rounded-md hover:bg-muted transition">
                                            <EllipsisVertical class="size-4 text-muted-foreground" />
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="w-40" align="end">
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

            <!-- Tab: Alertas -->
            <div v-show="activeTab === 'alerts'" class="space-y-4">
                <div class="flex justify-between items-center">

                    <div class="flex gap-2">
                        <button @click="filterStatus = 'all'" :class="[
                            'px-3 py-1 rounded-lg text-sm transition',
                            filterStatus === 'all'
                                ? 'bg-blue-600 text-white'
                                : 'bg-muted hover:bg-muted/80'
                        ]">
                            Todas
                        </button>
                        <button @click="filterStatus = 'active'" :class="[
                            'px-3 py-1 rounded-lg text-sm transition',
                            filterStatus === 'active'
                                ? 'bg-blue-600 text-white'
                                : 'bg-muted hover:bg-muted/80'
                        ]">
                            Activas
                        </button>
                        <button @click="filterStatus = 'resolved'" :class="[
                            'px-3 py-1 rounded-lg text-sm transition',
                            filterStatus === 'resolved'
                                ? 'bg-blue-600 text-white'
                                : 'bg-muted hover:bg-muted/80'
                        ]">
                            Resueltas
                        </button>
                    </div>
                </div>

                <AlertsTable :alerts="filteredAlerts" @view-details="handleViewDetails"
                    @mark-resolved="handleMarkResolved" @archive="handleArchive" />

                <AlertDialog v-model:open="openAlertDetails" :alert="selectedAlert"
                    @mark-resolved="handleMarkResolvedFromDialog" />
            </div>

        </div>

    </AppLayout>
</template>

<script setup lang="ts">
import Dialog from '@/components/assets/Dialog.vue';
import DialogDetails from '@/components/assets/DialogDetails.vue';
import Table from '@/components/assets/Table.vue';
import AlertsTable from '@/components/assets/AlertTable.vue';
import AlertDialog from '@/components/assets/AlertDialog.vue';
import type { AssetStats, AssetStatusOption } from '@/interfaces/asset.interface';
import { AssetStatus, assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import type { Alert } from '@/interfaces/alert.interface';
import { AlertStatus } from '@/interfaces/alert.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Paginated } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Boxes, Check, EllipsisVertical, ShieldCheck, ShieldX, AlertCircle } from 'lucide-vue-next';

const { assetsPaginated, stats } = defineProps<{ assetsPaginated: Paginated<Asset>, stats: AssetStats }>();

const currentAsset = ref<Asset | null>(null);
const openDetails = ref(false);
const openEditor = ref(false);
const openAlertDetails = ref(false);
const statusOp = ref<AssetStatusOption>(assetStatusOptions[AssetStatus.ASSIGNED]);
const activeTab = ref<'assets' | 'alerts'>('assets');
const filterStatus = ref<'all' | 'active' | 'resolved'>('all');
const selectedAlert = ref<Alert | null>(null);

// Datos de ejemplo para alertas
const alerts = ref<Alert[]>([
    {
        id: 1,
        entity_type: 'Accesorio',
        entity_id: 1,
        type: 'Stock Bajo',
        message: 'Se notificó a ventas que el Mouse Logitech MX Master 3 se ha agotado',
        status: AlertStatus.ACTIVE,
        last_notified_at: '2026-01-18T10:30:00',
        metadata: { product: 'Mouse Logitech MX Master 3', stock: 0, min_stock: 5 },
        created_at: '2026-01-18T10:30:00',
        updated_at: '2026-01-18T10:30:00',
    },
    {
        id: 2,
        entity_type: 'Accesorio',
        entity_id: 2,
        type: 'Stock Bajo',
        message: 'Se notificó a ventas que el Teclado Mecánico Corsair K95 se ha agotado',
        status: AlertStatus.ACTIVE,
        last_notified_at: '2026-01-17T15:45:00',
        metadata: { product: 'Teclado Mecánico Corsair K95', stock: 0, min_stock: 3 },
        created_at: '2026-01-17T15:45:00',
        updated_at: '2026-01-17T15:45:00',
    },
    {
        id: 3,
        entity_type: 'Accesorio',
        entity_id: 3,
        type: 'Stock Bajo',
        message: 'Se notificó a ventas que los Cable USB-C se han agotado',
        status: AlertStatus.RESOLVED,
        last_notified_at: '2026-01-15T09:20:00',
        metadata: { product: 'Cable USB-C', stock: 0, min_stock: 10 },
        created_at: '2026-01-15T09:20:00',
        updated_at: '2026-01-16T14:30:00',
    },
    {
        id: 4,
        entity_type: 'Accesorio',
        entity_id: 4,
        type: 'Stock Bajo',
        message: 'Se notificó a ventas que el Monitor Dell UltraWide se ha agotado',
        status: AlertStatus.ACTIVE,
        last_notified_at: '2026-01-18T08:15:00',
        metadata: { product: 'Monitor Dell UltraWide 34"', stock: 0, min_stock: 2 },
        created_at: '2026-01-18T08:15:00',
        updated_at: '2026-01-18T08:15:00',
    },
]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos TI',
        href: "#"
    },
];

const unreadAlerts = computed(() =>
    alerts.value.filter(a => a.status === AlertStatus.ACTIVE).length
);

const filteredAlerts = computed(() => {
    if (filterStatus.value === 'all') return alerts.value;
    return alerts.value.filter(a => a.status === filterStatus.value);
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

const handleViewDetails = (alert: Alert) => {
    selectedAlert.value = alert;
    openAlertDetails.value = true;
};

const handleMarkResolved = (alert: Alert) => {
    const index = alerts.value.findIndex(a => a.id === alert.id);
    if (index !== -1) {
        alerts.value[index].status = AlertStatus.RESOLVED;
        alerts.value[index].updated_at = new Date().toISOString();
    }
};

const handleMarkResolvedFromDialog = () => {
    if (selectedAlert.value) {
        handleMarkResolved(selectedAlert.value);
        openAlertDetails.value = false;
    }
};

const handleArchive = (alert: Alert) => {
    const index = alerts.value.findIndex(a => a.id === alert.id);
    if (index !== -1) {
        alerts.value[index].status = AlertStatus.ARCHIVED;
        alerts.value[index].updated_at = new Date().toISOString();
    }
};
</script>