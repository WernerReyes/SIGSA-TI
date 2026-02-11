<script setup lang="ts">
import { computed } from 'vue';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { VisDonut, VisDonutSelectors, VisSingleContainer, VisTooltip } from '@unovis/vue';

interface PriorityDatum {
    key: 'critical' | 'high' | 'medium' | 'low';
    label: string;
    value: number;
    color: string;
}

const chartConfig = {
    critical: { label: 'Critico', color: 'hsl(0 84% 60%)' },
    high: { label: 'Alto', color: 'hsl(25 95% 53%)' },
    medium: { label: 'Medio', color: 'hsl(38 92% 50%)' },
    low: { label: 'Bajo', color: 'hsl(142 76% 36%)' },
};

const priorityData: PriorityDatum[] = [
    { key: 'critical', label: 'Critico', value: 18, color: chartConfig.critical.color },
    { key: 'high', label: 'Alto', value: 22, color: chartConfig.high.color },
    { key: 'medium', label: 'Medio', value: 30, color: chartConfig.medium.color },
    { key: 'low', label: 'Bajo', value: 40, color: chartConfig.low.color },
];

const totalTickets = computed(() => priorityData.reduce((sum, item) => sum + item.value, 0));

const buildTooltipContent = (d: PriorityDatum): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex flex-col gap-1';

    const label = document.createElement('div');
    label.className = 'font-medium text-foreground';
    label.textContent = d.label;

    const value = document.createElement('div');
    value.className = 'text-xs text-muted-foreground';
    value.textContent = `${d.value} tickets`;

    wrapper.appendChild(label);
    wrapper.appendChild(value);

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
            <ChartContainer :config="chartConfig" class="relative h-[20rem] aspect-auto">
                <template #default>
                    <div class="relative flex-1">
                        <VisSingleContainer :data="priorityData" class="h-full w-full">
                            <VisDonut
                                :value="(d: PriorityDatum) => d.value"
                                :pad-angle="0.02"
                                :corner-radius="10"
                                :inner-radius="0.62"
                                :outer-radius="0.96"
                                :color="(d: PriorityDatum) => d.color"
                            />
                            <VisTooltip
                                :triggers="tooltipTriggers"
                                class-name="rounded-lg border border-border/50 bg-background px-2.5 py-2 text-xs shadow-xl"
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
