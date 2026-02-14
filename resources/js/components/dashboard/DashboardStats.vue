<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card v-for="stat in statsParsed" :key="stat.title" :class="stat.cardClass">
            <CardContent class="flex items-start justify-between gap-4 py-6">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">
                        {{ stat.title }}
                    </p>
                    <div class="flex items-center gap-3">
                        <p class="text-3xl font-semibold text-foreground">
                            {{ stat.value }}
                        </p>
                        <Badge class="border" :class="stat.deltaClass" variant="secondary">
                            {{ stat.delta }}
                        </Badge>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ stat.helper }}
                    </p>
                </div>
                <div class="rounded-lg p-3" :class="stat.iconClass">
                    <component :is="stat.icon" class="h-5 w-5" />
                </div>
            </CardContent>
        </Card>
    </div>
</template>


<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { type DashboardStats } from '@/interfaces/dashboard.interface';
import { getTicketOp, TicketPriority } from '@/interfaces/ticket.interface';
import { Code2, Monitor, Ticket } from 'lucide-vue-next';
import { computed } from 'vue';

const { stats } = defineProps<{
    stats: DashboardStats;
}>();


const statsParsed = computed(() => {
    const { active_projects, assets_at_risk, open_tickets, sla_breaches } = stats;

    return [
        {
            title: 'Tickets abiertos',
            value: open_tickets.total,
            helper: `${open_tickets.unassigned} sin asignar`,
            delta: open_tickets.trend_direction
                ? `${open_tickets.trend_direction === 'up' ? '↑' : '↓'} ${Math.abs(open_tickets.trend_percentage)}%`
                : 'Sin cambios',
            deltaClass: open_tickets.trend_percentage
                ? open_tickets.trend_direction === 'up'
                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                    : 'bg-destructive/10 text-destructive border-destructive/20'
                : 'bg-muted/10 text-muted-600 border-muted/20',
            icon: Ticket,
            cardClass: 'border-primary/15 bg-primary/5',
            iconClass: 'text-primary bg-primary/10',
        },
        {
            title: 'Fuera de SLA',
            value: sla_breaches.out_sla,
            helper: sla_breaches.message,
            delta: getTicketOp('priority', sla_breaches.severity).label,
            deltaClass: slaBreachesClass[sla_breaches.severity].delta,
            icon: getTicketOp('priority', sla_breaches.severity).icon,
            cardClass: slaBreachesClass[sla_breaches.severity].card,
            iconClass: slaBreachesClass[sla_breaches.severity].icon,

        },
        {
            title: 'Activos en riesgo',
            value: assets_at_risk.total,
            helper: assets_at_risk.message,
            delta: `${assets_at_risk.new_assets} nuevos`,
            deltaClass: assets_at_risk.new_assets
                ? 'bg-amber-500/10 text-amber-600 border-amber-500/20'
                : 'bg-muted/10 text-muted-600 border-muted/20',
            icon: Monitor,
            cardClass: assets_at_risk.new_assets
                ? 'border-amber-500/20 bg-amber-500/5'
                : 'border-muted/20 bg-muted/5',
            iconClass: assets_at_risk.new_assets
                ? 'text-amber-600 bg-amber-500/10'
                : 'text-muted bg-muted/10',
        },
        {
            title: 'Proyectos activos',
            value: active_projects.total,
            helper: `${active_projects.in_development} en fase de desarrollo`,
            delta: active_projects.trend_direction
                ? `${active_projects.trend_direction === 'up' ? '↑' : '↓'} ${Math.abs(active_projects.trend_percentage)}%`
                : 'Sin cambios',
            deltaClass: active_projects.trend_percentage
                ? active_projects.trend_direction === 'up'
                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                    : 'bg-destructive/10 text-destructive border-destructive/20'
                : 'bg-muted/10 text-muted-600 border-muted/20',
            icon: Code2,
            cardClass: active_projects.trend_percentage
                ? active_projects.trend_direction === 'up'
                    ? 'border-emerald-500/20 bg-emerald-500/5'
                    : 'border-destructive/20 bg-destructive/5'
                : 'border-muted/20 bg-muted/5',
            iconClass: active_projects.trend_percentage
                ? active_projects.trend_direction === 'up'
                    ? 'text-emerald-600 bg-emerald-500/10'
                    : 'text-destructive bg-destructive/10'
                : 'bg-muted',
        }

    ]
})

const slaBreachesClass: Record<TicketPriority, {
    delta: string;
    card: string;
    icon: string;
}> = {
    URGENT: {
        delta: 'bg-rose-500/10 text-rose-600 border-rose-500/20',
        card: 'border-rose-500/20 bg-rose-500/5',
        icon: 'text-rose-600 bg-rose-500/10',
    },
    HIGH: {
        delta: 'bg-orange-500/10 text-orange-600 border-orange-500/20',
        card: 'border-orange-500/20 bg-orange-500/5',
        icon: 'text-orange-600 bg-orange-500/10',
    },
    MEDIUM: {
        delta: 'bg-yellow-500/10 text-yellow-600 border-yellow-500/20',
        card: 'border-yellow-500/20 bg-yellow-500/5',
        icon: 'text-yellow-600 bg-yellow-500/10',
    },
    LOW: {
        delta: 'bg-green-500/10 text-green-600 border-green-500/20',
        card: 'border-green-500/20 bg-green-500/5',
        icon: 'text-green-600 bg-green-500/10',
    },
};
</script>