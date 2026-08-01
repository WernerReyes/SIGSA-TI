<script setup lang="ts">
import TicketDashboardSummary from '@/components/ticket-dashboard/TicketDashboardSummary.vue';
import TicketDashboardTables from '@/components/ticket-dashboard/TicketDashboardTables.vue';
import TicketDistributionCard from '@/components/ticket-dashboard/TicketDistributionCard.vue';
import TicketTrendChart from '@/components/ticket-dashboard/TicketTrendChart.vue';
import SelectFilters from '@/components/SelectFilters.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useApp } from '@/composables/useApp';
import type {
    TicketDashboardData,
    TicketDashboardFilterOptions,
} from '@/interfaces/ticketDashboard.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ListChecks,
    ListFilter,
    RefreshCw,
    TableProperties,
    Tags,
    TicketPlus,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    dashboard: TicketDashboardData;
    filterOptions: TicketDashboardFilterOptions;
}>();

const { isLoading } = useApp();

const filters = ref({
    start_date: props.dashboard.filters.start_date ?? '',
    end_date: props.dashboard.filters.end_date ?? '',
    responsible_ids: props.dashboard.filters.responsible_ids ?? [],
    requester_ids: props.dashboard.filters.requester_ids ?? [],
    statuses: props.dashboard.filters.statuses ?? [],
    types: props.dashboard.filters.types ?? [],
    categories: props.dashboard.filters.categories ?? [],
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tickets', href: '/tickets' },
    { title: 'Dashboard', href: '/tickets/dashboard' },
];

const applyFilters = () => {
    router.get(
        '/tickets/dashboard',
        {
            start_date: filters.value.start_date,
            end_date: filters.value.end_date,
            responsible_ids: filters.value.responsible_ids.length
                ? filters.value.responsible_ids
                : undefined,
            requester_ids: filters.value.requester_ids.length
                ? filters.value.requester_ids
                : undefined,
            statuses: filters.value.statuses.length
                ? filters.value.statuses
                : undefined,
            types: filters.value.types.length ? filters.value.types : undefined,
            categories: filters.value.categories.length
                ? filters.value.categories
                : undefined,
        },
        {
            only: ['dashboard'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const clearDimensionFilters = () => {
    filters.value.responsible_ids = [];
    filters.value.requester_ids = [];
    filters.value.statuses = [];
    filters.value.types = [];
    filters.value.categories = [];
    applyFilters();
};
</script>

<template>
    <Head title="Dashboard de tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <div
                class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
            >
                <div>
                    <p
                        class="text-primary text-xs font-medium uppercase tracking-[0.25em]"
                    >
                        Analítica de soporte
                    </p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight">
                        Dashboard de tickets
                    </h1>
                    <p class="text-muted-foreground mt-1 max-w-2xl text-sm">
                        Indicadores de carga, SLA, evolución y desempeño del
                        equipo para el periodo seleccionado.
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/tickets">
                        <TableProperties />
                        Ver matriz de tickets
                    </Link>
                </Button>
            </div>

            <form
                class="border-border/80 bg-card grid gap-4 rounded-xl border p-4 shadow-sm md:grid-cols-2 xl:grid-cols-4"
                @submit.prevent="applyFilters"
            >
                <div class="space-y-2">
                    <Label for="ticket-dashboard-start">Desde</Label>
                    <Input
                        id="ticket-dashboard-start"
                        v-model="filters.start_date"
                        type="date"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="ticket-dashboard-end">Hasta</Label>
                    <Input
                        id="ticket-dashboard-end"
                        v-model="filters.end_date"
                        type="date"
                    />
                </div>
                <div class="min-w-0 space-y-2">
                    <Label>Solicitantes</Label>
                    <SelectFilters
                        label="Solicitantes"
                        :items="filterOptions.requesters"
                        item-value="staff_id"
                        item-label="full_name"
                        :icon="Users"
                        :multiple="true"
                        selected-as-label
                        :max-label-length="1"
                        full-width
                        :default-value="filters.requester_ids"
                        @select="(values) => (filters.requester_ids = values)"
                    />
                </div>
                <div class="min-w-0 space-y-2">
                    <Label>Responsables</Label>
                    <SelectFilters
                        label="Responsables"
                        :items="filterOptions.responsibles"
                        item-value="staff_id"
                        item-label="full_name"
                        :icon="Users"
                        :multiple="true"
                        selected-as-label
                        :max-label-length="1"
                        full-width
                        :default-value="filters.responsible_ids"
                        @select="(values) => (filters.responsible_ids = values)"
                    />
                </div>
                <div class="min-w-0 space-y-2">
                    <Label>Estados</Label>
                    <SelectFilters
                        label="Estados"
                        :items="filterOptions.statuses"
                        item-value="value"
                        item-label="label"
                        :icon="ListChecks"
                        :multiple="true"
                        selected-as-label
                        :max-label-length="2"
                        full-width
                        :default-value="filters.statuses"
                        @select="(values) => (filters.statuses = values)"
                    />
                </div>
                <div class="min-w-0 space-y-2">
                    <Label>Tipos</Label>
                    <SelectFilters
                        label="Tipos"
                        :items="filterOptions.types"
                        item-value="value"
                        item-label="label"
                        :icon="TicketPlus"
                        :multiple="true"
                        selected-as-label
                        :max-label-length="2"
                        full-width
                        :default-value="filters.types"
                        @select="(values) => (filters.types = values)"
                    />
                </div>
                <div class="min-w-0 space-y-2">
                    <Label>Categorías</Label>
                    <SelectFilters
                        label="Categorías"
                        :items="filterOptions.categories"
                        item-value="value"
                        item-label="label"
                        :icon="Tags"
                        :multiple="true"
                        selected-as-label
                        :max-label-length="2"
                        full-width
                        :default-value="filters.categories"
                        @select="(values) => (filters.categories = values)"
                    />
                </div>
                <div class="flex items-end gap-2">
                    <Button type="submit" class="flex-1" :disabled="isLoading">
                        <ListFilter />
                        Aplicar
                    </Button>
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        title="Limpiar filtros de selección"
                        :disabled="isLoading"
                        @click="clearDimensionFilters"
                    >
                        <RefreshCw />
                    </Button>
                </div>
            </form>

            <TicketDashboardSummary :summary="dashboard.summary" />

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <TicketTrendChart
                    :data="dashboard.daily_trend"
                    class="xl:col-span-2"
                />
                <TicketDistributionCard
                    title="Tickets por estado"
                    description="Distribución del flujo de atención."
                    :items="dashboard.by_status"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                <TicketDistributionCard
                    title="Tickets por prioridad"
                    description="Concentración según impacto y urgencia."
                    :items="dashboard.by_priority"
                />
                <TicketDistributionCard
                    title="Tickets por tipo"
                    description="Incidentes y solicitudes de servicio."
                    :items="dashboard.by_type"
                />
                <TicketDistributionCard
                    title="Tickets por categoría"
                    description="Clasificación funcional de las solicitudes."
                    :items="dashboard.by_category"
                />
            </div>

            <TicketDashboardTables
                :technicians="dashboard.technicians"
                :tickets="dashboard.tickets"
            />
        </div>
    </AppLayout>
</template>
