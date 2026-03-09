<script setup lang="ts">
import DashboardActiveAlerts from '@/components/dashboard/DashboardActiveAlerts.vue';
import DashboardPriorityChart from '@/components/dashboard/DashboardPriorityChart.vue';
import DashboardRecentTickets from '@/components/dashboard/DashboardRecentTickets.vue';
import DashboardSlaChart from '@/components/dashboard/DashboardSlaChart.vue';
import DashboardStats from '@/components/dashboard/DashboardStats.vue';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { useApp } from '@/composables/useApp';
import { type NotificationContract } from '@/interfaces/contract.interface';
import type { DashboardSLACompliance, DashboardTicketsByPriority, DashboardStats as IDashboardStats } from '@/interfaces/dashboard.interface';
import { type Ticket } from '@/interfaces/ticket.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { getLocalTimeZone, parseDate } from '@internationalized/date';
import { Head, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { ChevronDownIcon, RefreshCw } from 'lucide-vue-next';
import { type DateRange } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import { es } from 'date-fns/locale';

const { activeFilters } = defineProps<{
    stats: IDashboardStats;
    tickets_by_priority: DashboardTicketsByPriority,
    recent_tickets: Ticket[],
    sla_compliance: DashboardSLACompliance,
    recent_contract_notifications: NotificationContract[],
    activeFilters?: {
        start_date?: string;
        end_date?: string;
    };
}>();

const { isFromTI } = useApp();

// Inicializar rango de fechas desde filtros activos o valores por defecto
const initialStart = activeFilters?.start_date
    ? parseDate(activeFilters.start_date)
    : undefined;
const initialEnd = activeFilters?.end_date
    ? parseDate(activeFilters.end_date)
    : undefined;

const dateRange = ref<DateRange | undefined>(
    initialStart ? {
        start: initialStart,
        end: initialEnd || initialStart,
    } : undefined
);

const formattedDateRange = computed(() => {
    if (!dateRange.value?.start) {
        return 'Seleccionar rango';
    }

    // const start = dateRange.value.start.toDate(getLocalTimeZone()).toLocaleDateString();
    const start = format(dateRange.value.start.toDate(getLocalTimeZone()), "EEEE, d 'de' MMMM 'de' yyyy", {
        locale: es
    });

    if (!dateRange.value.end) {
        return start;
    }

    const end = format(dateRange.value.end.toDate(getLocalTimeZone()), "EEEE, d 'de' MMMM 'de' yyyy", {
        locale: es
    });
    // const end = dateRange.value.end.toDate(getLocalTimeZone()).toLocaleDateString();
    return `${start} - ${end}`;
});

const applyFilters = () => {
    const startDate = dateRange.value?.start
        ? format(dateRange.value.start.toDate(getLocalTimeZone()), 'yyyy-MM-dd')
        : undefined;
    const endDate = dateRange.value?.end
        ? format(dateRange.value.end.toDate(getLocalTimeZone()), 'yyyy-MM-dd')
        : undefined;

    router.get('/dashboard', {
        start_date: startDate,
        end_date: endDate,
    }, {
        only: ['stats', 'tickets_by_priority', 'recent_tickets', 'sla_compliance', 'recent_contract_notifications', 'activeFilters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

watch(dateRange, () => {
    applyFilters();
}, { deep: true });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '#',
    },
];
</script>

<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- <template #actions> -->

        <!-- </template> -->

        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
            <div class="flex items-center gap-4 self-end">

                <Popover>
                    <PopoverTrigger as-child>
                        <Button id="date" variant="outline" class="w-fit justify-between font-normal h-9">
                            {{ formattedDateRange }}
                            <ChevronDownIcon class="w-4 h-4" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto overflow-hidden p-0" align="end">
                        <RangeCalendar v-model="dateRange as any" locales="es" class="rounded-md border shadow-sm"
                            disable-days-outside-current-view />
                    </PopoverContent>
                </Popover>
                <Button v-if="dateRange" variant="outline" color="red" class="h-9" @click="dateRange = initialStart ? {
                    start: initialStart,
                    end: initialEnd || initialStart,
                } : undefined">
                    <RefreshCw />
                </Button>
            </div>

            <DashboardStats :stats="stats" />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <DashboardPriorityChart :tickets="tickets_by_priority" />
                <DashboardSlaChart :sla-compliance="sla_compliance" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <DashboardRecentTickets :class="isFromTI ? 'lg:col-span-2' : 'lg:col-span-3'"
                    :tickets="recent_tickets" />
                <DashboardActiveAlerts v-if="isFromTI" :notifications="recent_contract_notifications" />
            </div>
        </div>
    </AppLayout>
</template>
