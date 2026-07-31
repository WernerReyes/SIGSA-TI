<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
    type ChartConfig,
} from '@/components/ui/chart';
import type { TicketDashboardData } from '@/interfaces/ticketDashboard.interface';
import { parseDateOnly } from '@/lib/utils';
import { VisArea, VisAxis, VisLine, VisXYContainer } from '@unovis/vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed } from 'vue';

const props = defineProps<{
    data: TicketDashboardData['daily_trend'];
}>();

const chartData = computed(() =>
    props.data.map((item, index) => ({
        ...item,
        index,
        label: format(parseDateOnly(item.date), 'dd MMM', { locale: es }),
    })),
);

type TrendDatum = (typeof chartData.value)[number];

const chartConfig = {
    created: { label: 'Creados', color: 'var(--chart-1)' },
    resolved: { label: 'Resueltos', color: 'var(--chart-2)' },
} satisfies ChartConfig;

const tickStep = computed(() =>
    Math.max(1, Math.ceil(chartData.value.length / 8)),
);
const tickValues = computed(() =>
    chartData.value
        .filter((_item, index) => index % tickStep.value === 0)
        .map((item) => item.index),
);
</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>Evolución diaria</CardTitle>
            <CardDescription
                >Tickets creados frente a tickets resueltos.</CardDescription
            >
        </CardHeader>
        <CardContent>
            <ChartContainer :config="chartConfig" class="h-80 w-full">
                <VisXYContainer
                    :data="chartData"
                    :margin="{ top: 12, right: 12, bottom: 28, left: 28 }"
                >
                    <VisArea
                        :x="(item: TrendDatum) => item.index"
                        :y="(item: TrendDatum) => item.created"
                        :color="chartConfig.created.color"
                        :opacity="0.12"
                    />
                    <VisLine
                        :x="(item: TrendDatum) => item.index"
                        :y="(item: TrendDatum) => item.created"
                        :color="chartConfig.created.color"
                        :line-width="2.5"
                    />
                    <VisLine
                        :x="(item: TrendDatum) => item.index"
                        :y="(item: TrendDatum) => item.resolved"
                        :color="chartConfig.resolved.color"
                        :line-width="2.5"
                    />
                    <VisAxis
                        type="x"
                        :tick-values="tickValues"
                        :tick-format="
                            (value: number) => chartData[value]?.label ?? ''
                        "
                        :grid-line="false"
                    />
                    <VisAxis type="y" :num-ticks="5" />
                    <ChartTooltip />
                    <ChartCrosshair
                        :template="
                            componentToString(
                                chartConfig,
                                ChartTooltipContent,
                                { labelKey: 'label' },
                            )
                        "
                        :color="
                            (_item: TrendDatum, index: number) =>
                                [
                                    chartConfig.created.color,
                                    chartConfig.resolved.color,
                                ][index % 2]
                        "
                    />
                </VisXYContainer>
            </ChartContainer>
        </CardContent>
    </Card>
</template>
