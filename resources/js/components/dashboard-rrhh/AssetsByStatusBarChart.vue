<template>
    <Card class="rounded-lg border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Estado de Activos</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-65 aspect-auto">
                <template #default>
                    <VisXYContainer :data="statusChartData" :margin="{ top: 16, bottom: 32, left: 32, right: 12 }"
                        class="h-full w-full">
                        <VisGroupedBar :x="(d: StatusDatum) => d.order"
                            :y="[(d: StatusDatum) => d.celulares, (d: StatusDatum) => d.cargadores]"
                            :color="[chartConfig.celulares.color, chartConfig.cargadores.color]" :rounded-corners="4"
                            bar-padding="0.1" group-padding="0" />

                        <VisAxis type="x" :x="(d: StatusDatum) => d.order" :tick-line="false" :domain-line="false"
                            :grid-line="false" :tick-values="statusChartData.map((d) => d.order)"
                            :tick-format="(value: number) => statusChartData.find((d) => d.order === value)?.statusLabel || ''" />
                        <VisAxis type="y" :tick-format="(_d: number) => ''" :tick-line="false" :domain-line="false"
                            :grid-line="true" />

                        <ChartTooltip />
                        <ChartCrosshair
                            :template="componentToString(chartConfig, ChartTooltipContent, { labelFormatter: (value: number | Date) => statusChartData.find((d) => d.order === Number(value))?.statusLabel || '' })"
                            :color="[chartConfig.celulares.color, chartConfig.cargadores.color]" />
                    </VisXYContainer>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script lang="ts" setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type ChartConfig, ChartContainer, ChartCrosshair, ChartLegendContent, ChartTooltip, ChartTooltipContent, componentToString } from '@/components/ui/chart';
import { type Asset, AssetStatus } from '@/interfaces/asset.interface';
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import { computed } from 'vue';

const { assetsByStatus }  = defineProps<{
    assetsByStatus: RRHHDashboard['assetsByStatus'];
}>();

interface StatusDatum {
    order: number;
    status: AssetStatus;
    statusLabel: string;
    celulares: number;
    cargadores: number;
}


const statusOrder: AssetStatus[] = [
    AssetStatus.AVAILABLE,
    AssetStatus.ASSIGNED,
    AssetStatus.IN_REPAIR,
    AssetStatus.DECOMMISSIONED,
];

const statusLabelMap: Record<AssetStatus, string> = {
    [AssetStatus.AVAILABLE]: 'Disponible',
    [AssetStatus.ASSIGNED]: 'Asignado',
    [AssetStatus.IN_REPAIR]: 'Mantenimiento',
    [AssetStatus.DECOMMISSIONED]: 'Retirado',
};

const statusChartData = computed<StatusDatum[]>(() => {
    return statusOrder.map((status, index) => ({
        order: index,
        status,
        statusLabel: statusLabelMap[status],
        celulares: assetsByStatus[status]?.smartphones ?? 0,
        cargadores: assetsByStatus[status]?.chargers ?? 0,
    }));
});

const chartConfig = {
    celulares: { label: 'Celulares', color: 'hsl(224 76% 48%)' },
    cargadores: { label: 'Cargadores', color: 'hsl(262 83% 58%)' },
} satisfies ChartConfig;
</script>