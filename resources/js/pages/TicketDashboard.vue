<script setup lang="ts">
import TicketDashboardSummary from '@/components/ticket-dashboard/TicketDashboardSummary.vue';
import TicketDashboardTables from '@/components/ticket-dashboard/TicketDashboardTables.vue';
import TicketDistributionCard from '@/components/ticket-dashboard/TicketDistributionCard.vue';
import TicketTrendChart from '@/components/ticket-dashboard/TicketTrendChart.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    NativeSelect,
    NativeSelectOption,
} from '@/components/ui/native-select';
import { useApp } from '@/composables/useApp';
import type {
    TicketDashboardData,
    TicketDashboardFilterOptions,
} from '@/interfaces/ticketDashboard.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ListFilter, RefreshCw, TableProperties } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    dashboard: TicketDashboardData;
    filterOptions: TicketDashboardFilterOptions;
}>();

const { isLoading } = useApp();

const filters = ref({
    start_date: props.dashboard.filters.start_date ?? '',
    end_date: props.dashboard.filters.end_date ?? '',
    responsible_id: props.dashboard.filters.responsible_id?.toString() ?? '',
    type: props.dashboard.filters.type ?? '',
    category: props.dashboard.filters.category ?? '',
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
            responsible_id: filters.value.responsible_id || undefined,
            type: filters.value.type || undefined,
            category: filters.value.category || undefined,
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
    filters.value.responsible_id = '';
    filters.value.type = '';
    filters.value.category = '';
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
                class="border-border/80 bg-card grid gap-4 rounded-xl border p-4 shadow-sm md:grid-cols-2 xl:grid-cols-6"
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
                <div class="space-y-2">
                    <Label for="ticket-dashboard-responsible"
                        >Responsable</Label
                    >
                    <NativeSelect
                        id="ticket-dashboard-responsible"
                        v-model="filters.responsible_id"
                        class="w-full"
                    >
                        <NativeSelectOption value="">Todos</NativeSelectOption>
                        <NativeSelectOption
                            v-for="responsible in filterOptions.responsibles"
                            :key="responsible.staff_id"
                            :value="responsible.staff_id.toString()"
                        >
                            {{ responsible.full_name }}
                        </NativeSelectOption>
                    </NativeSelect>
                </div>
                <div class="space-y-2">
                    <Label for="ticket-dashboard-type">Tipo</Label>
                    <NativeSelect
                        id="ticket-dashboard-type"
                        v-model="filters.type"
                        class="w-full"
                    >
                        <NativeSelectOption value="">Todos</NativeSelectOption>
                        <NativeSelectOption
                            v-for="type in filterOptions.types"
                            :key="type.value"
                            :value="type.value"
                        >
                            {{ type.label }}
                        </NativeSelectOption>
                    </NativeSelect>
                </div>
                <div class="space-y-2">
                    <Label for="ticket-dashboard-category">Categoría</Label>
                    <NativeSelect
                        id="ticket-dashboard-category"
                        v-model="filters.category"
                        class="w-full"
                    >
                        <NativeSelectOption value="">Todas</NativeSelectOption>
                        <NativeSelectOption
                            v-for="category in filterOptions.categories"
                            :key="category.value"
                            :value="category.value"
                        >
                            {{ category.label }}
                        </NativeSelectOption>
                    </NativeSelect>
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
                        title="Limpiar responsable, tipo y categoría"
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
                :tickets="dashboard.recent_tickets"
            />
        </div>
    </AppLayout>
</template>
