<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { type DashboardTicketsByPriority } from '@/interfaces/dashboard.interface';
import { TicketPriority, ticketPriorityOptions } from '@/interfaces/ticket.interface';
import { buildTooltipContent } from '@/lib/utils';
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


const tooltipTriggers = computed(() => ({
    [VisDonutSelectors.segment]: (d: {
    data: { label: string; color: string };
    value: number;
    }) => {
        return buildTooltipContent({
            data: { label: d.data.label, color: d.data.color },
            value: d.value,
            total: totalTickets.value
        }, 'tickets');
    },
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
                                 />
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
