<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import type { TicketDashboardData } from '@/interfaces/ticketDashboard.interface';
import {
    CheckCheck,
    CircleAlert,
    Clock3,
    Inbox,
    Tickets,
    UserRoundX,
} from 'lucide-vue-next';
import { computed, type Component } from 'vue';

const props = defineProps<{
    summary: TicketDashboardData['summary'];
}>();

interface SummaryCard {
    label: string;
    value: string | number;
    helper: string;
    icon: Component;
    accent: string;
    iconClass: string;
}

const formatDuration = (minutes: number) => {
    if (minutes < 60) return `${minutes} min`;

    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    return remainingMinutes
        ? `${hours} h ${remainingMinutes} min`
        : `${hours} h`;
};

const cards = computed<SummaryCard[]>(() => [
    {
        label: 'Tickets del periodo',
        value: props.summary.total,
        helper: `${props.summary.active} todavía activos`,
        icon: Tickets,
        accent: 'bg-primary',
        iconClass: 'bg-primary/10 text-primary',
    },
    {
        label: 'Resueltos',
        value: props.summary.resolved,
        helper: `${props.summary.closed} cerrados`,
        icon: CheckCheck,
        accent: 'bg-emerald-500',
        iconClass: 'bg-emerald-500/10 text-emerald-600',
    },
    {
        label: 'Sin asignar',
        value: props.summary.unassigned,
        helper: 'Requieren responsable',
        icon: UserRoundX,
        accent: 'bg-amber-500',
        iconClass: 'bg-amber-500/10 text-amber-600',
    },
    {
        label: 'Fuera de SLA',
        value: props.summary.sla_breached,
        helper: 'Vencidos o incumplidos',
        icon: CircleAlert,
        accent: 'bg-rose-500',
        iconClass: 'bg-rose-500/10 text-rose-600',
    },
    {
        label: 'Cumplimiento SLA',
        value: `${props.summary.sla_compliance_rate}%`,
        helper: 'Sobre tickets resueltos',
        icon: Inbox,
        accent: 'bg-sky-500',
        iconClass: 'bg-sky-500/10 text-sky-600',
    },
    {
        label: 'Tiempo medio',
        value: formatDuration(props.summary.average_resolution_minutes),
        helper: 'Promedio de resolución',
        icon: Clock3,
        accent: 'bg-violet-500',
        iconClass: 'bg-violet-500/10 text-violet-600',
    },
]);
</script>

<template>
    <div
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6"
    >
        <Card
            v-for="card in cards"
            :key="card.label"
            class="border-border/80 group relative overflow-hidden transition-transform hover:-translate-y-0.5"
        >
            <div :class="['absolute inset-x-0 top-0 h-1', card.accent]" />
            <CardContent class="p-5">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <p class="text-muted-foreground text-sm font-medium">
                        {{ card.label }}
                    </p>
                    <div
                        :class="[
                            'flex size-10 items-center justify-center rounded-lg',
                            card.iconClass,
                        ]"
                    >
                        <component :is="card.icon" class="size-5" />
                    </div>
                </div>
                <p class="text-3xl font-bold tracking-tight">
                    {{ card.value }}
                </p>
                <p class="text-muted-foreground mt-2 text-xs">
                    {{ card.helper }}
                </p>
            </CardContent>
        </Card>
    </div>
</template>
