<script setup lang="ts">
import DashboardActiveAlerts from '@/components/dashboard/DashboardActiveAlerts.vue';
import DashboardPriorityChart from '@/components/dashboard/DashboardPriorityChart.vue';
import DashboardRecentTickets from '@/components/dashboard/DashboardRecentTickets.vue';
import DashboardSlaChart from '@/components/dashboard/DashboardSlaChart.vue';
import DashboardStats from '@/components/dashboard/DashboardStats.vue';
import { useApp } from '@/composables/useApp';
import { type NotificationContract } from '@/interfaces/contract.interface';
import type { DashboardSLACompliance, DashboardTicketsByPriority, DashboardStats as IDashboardStats } from '@/interfaces/dashboard.interface';
import { type Ticket } from '@/interfaces/ticket.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    stats: IDashboardStats;
    tickets_by_priority: DashboardTicketsByPriority,
    recent_tickets: Ticket[],
    sla_compliance: DashboardSLACompliance,
    recent_contract_notifications: NotificationContract[],

}>();

const { isFromTI } = useApp();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '#',
    },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs" >
            <template #actions>
                <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
            </template>

        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
           
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
