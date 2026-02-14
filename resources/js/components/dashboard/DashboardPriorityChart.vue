<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { type DashboardTicketsByPriority } from '@/interfaces/dashboard.interface';
import { TicketPriority, ticketPriorityOptions } from '@/interfaces/ticket.interface';
import { VisDonut, VisDonutSelectors, VisSingleContainer, VisTooltip } from '@unovis/vue';
import { computed } from 'vue';

const { tickets } = defineProps<{
    tickets: DashboardTicketsByPriority;
}>();

interface PriorityDatum {
    key: 'critical' | 'high' | 'medium' | 'low';
    label: string;
    value: number;
    color: string;
}

const colors = {
    [TicketPriority.URGENT]: 'hsl(0 84% 60%)',
    [TicketPriority.HIGH]: 'hsl(25 95% 53%)',
    [TicketPriority.MEDIUM]: 'hsl(38 92% 50%)',
    [TicketPriority.LOW]: 'hsl(142 76% 36%)',
};


const chartConfig2 = {
    ...Object.values(ticketPriorityOptions).reduce((acc, option) => {
        acc[option.value] = { label: option.label, color: colors[option.value] };
        return acc;
    }, {} as Record<string, { label: string; color: string }>)
};

const priorityData = computed(() => {
    return Object.values(ticketPriorityOptions).map(priority => {
        const key = priority.value
        return {
            key,
            label: priority.label,
            value: tickets?.[key] || 0,
            color: colors[key]
        };
    })
});

const totalTickets = computed(() => priorityData.value.reduce((sum, item) => sum + item.value, 0));

const buildTooltipContent = (d: PriorityDatum): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'min-w-[170px] rounded-xl border border-border/40 bg-background/90 p-3 shadow-2xl backdrop-blur-md';

    const total = totalTickets.value || 0;
    const percentValue = total > 0 ? Math.round((d.value / total) * 100) : 0;

    wrapper.innerHTML = `
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full ring-2 ring-background" style="background:${d.data.color}"></span>
                    <span class="text-[11px] uppercase tracking-wide text-muted-foreground">${d.data.label}</span>
                </div>
                <span class="rounded-full border border-border/40 bg-background/60 px-2 py-0.5 text-[10px] font-medium text-foreground/80">${percentValue}%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-semibold text-foreground tabular-nums">${d.value}</span>
                <span class="text-[11px] text-muted-foreground">tickets</span>
            </div>
            <div class="h-1 w-full rounded-full" style="background:${d.data.color}"></div>
        </div>
    `;

    return wrapper;
};

const tooltipTriggers = computed(() => ({
    [VisDonutSelectors.segment]: (d: PriorityDatum) => buildTooltipContent(d),
}));
</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>Tickets por prioridad</CardTitle>
            <CardDescription>Distribucion de urgencias en las ultimas 24 horas.</CardDescription>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig2" class="relative h-80 aspect-auto">
                <template #default>
                    <div class="relative flex-1">
                        <VisSingleContainer :data="priorityData" class="h-full w-full">
                            <VisDonut :value="(d: PriorityDatum) => d.value" :pad-angle="0.02" :corner-radius="10"
                                :inner-radius="0.62" :outer-radius="0.96" :color="(d: PriorityDatum) => d.color" />
                           
                          
                                <VisTooltip :triggers="tooltipTriggers"
                                class-name="rounded-lg border border-border/50 bg-background px-2.5 py-2 text-xs shadow-xl" />
                        </VisSingleContainer>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xs uppercase tracking-widest text-muted-foreground">Total</span>
                            <span class="text-2xl font-semibold text-foreground">
                                {{ totalTickets }}
                            </span>
                        </div>
                    </div>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>
