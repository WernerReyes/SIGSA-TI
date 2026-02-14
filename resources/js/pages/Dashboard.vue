<script setup lang="ts">
import DashboardActiveAlerts from '@/components/dashboard/DashboardActiveAlerts.vue';
import DashboardPriorityChart from '@/components/dashboard/DashboardPriorityChart.vue';
import DashboardRecentTickets from '@/components/dashboard/DashboardRecentTickets.vue';
import DashboardSlaChart from '@/components/dashboard/DashboardSlaChart.vue';
import DashboardStats from '@/components/dashboard/DashboardStats.vue';
import { DashboardTicketsByPriority, DashboardStats as IDashboardStats } from '@/interfaces/dashboard.interface';
import { Ticket } from '@/interfaces/ticket.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    stats: IDashboardStats;
    tickets_by_priority: DashboardTicketsByPriority,
    recent_tickets: Ticket[]

}>();

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
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
    
            <DashboardStats :stats="stats" />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <DashboardPriorityChart 
                :tickets="tickets_by_priority" />
                <DashboardSlaChart />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <DashboardRecentTickets class="lg:col-span-2" :tickets="recent_tickets" />
                <DashboardActiveAlerts />
            </div>
        </div>
    </AppLayout>
</template>
