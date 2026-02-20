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
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import { computed } from 'vue';

interface StatusDatum {
    order: number;
    status: AssetStatus;
    statusLabel: string;
    celulares: number;
    cargadores: number;
}

const smartphoneTypeId = 3;
const chargerTypeId = 12;

const mockAssets: Asset[] = [
    { id: 1, type_id: smartphoneTypeId, model: 'iPhone 13', serial_number: 'APL-001', processor: 'A15', ram: '4 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'iPhone RH-01', full_name: 'Apple iPhone 13', description: 'Equipo RH', is_new: true },
    { id: 2, type_id: smartphoneTypeId, model: 'Galaxy A54', serial_number: 'SMS-002', processor: 'Exynos', ram: '8 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.AVAILABLE, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Galaxy RH-01', full_name: 'Samsung Galaxy A54', description: 'Equipo RH', is_new: true },
    { id: 3, type_id: smartphoneTypeId, model: 'Moto G84', serial_number: 'MOT-003', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.IN_REPAIR, brand: 'Motorola', created_at: new Date(), updated_at: new Date(), name: 'Moto RH-01', full_name: 'Motorola Moto G84', description: 'Equipo RH', is_new: false },
    { id: 4, type_id: chargerTypeId, model: '20W', serial_number: 'CHG-004', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.AVAILABLE, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-01', full_name: 'Cargador Apple 20W', description: 'Accesorio RH', is_new: true },
    { id: 5, type_id: chargerTypeId, model: '25W', serial_number: 'CHG-005', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-02', full_name: 'Cargador Samsung 25W', description: 'Accesorio RH', is_new: true },
    { id: 6, type_id: chargerTypeId, model: 'USB-C', serial_number: 'CHG-006', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.DECOMMISSIONED, brand: 'Anker', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-03', full_name: 'Cargador Anker USB-C', description: 'Accesorio RH', is_new: false },
];

const statusOrder: AssetStatus[] = [
    AssetStatus.AVAILABLE,
    AssetStatus.ASSIGNED,
    AssetStatus.IN_REPAIR,
    AssetStatus.DECOMMISSIONED,
];

const statusLabelMap: Record<AssetStatus, string> = {
    [AssetStatus.AVAILABLE]: 'Activo',
    [AssetStatus.ASSIGNED]: 'Stock',
    [AssetStatus.IN_REPAIR]: 'Mant.',
    [AssetStatus.DECOMMISSIONED]: 'Retirado',
};

const statusChartData = computed<StatusDatum[]>(() => {
    return statusOrder.map((status, index) => ({
        order: index,
        status,
        statusLabel: statusLabelMap[status],
        celulares: mockAssets.filter((asset) => asset.type_id === smartphoneTypeId && asset.status === status).length,
        cargadores: mockAssets.filter((asset) => asset.type_id === chargerTypeId && asset.status === status).length,
    }));
});

const chartConfig = {
    celulares: { label: 'Celulares', color: 'hsl(224 76% 48%)' },
    cargadores: { label: 'Cargadores', color: 'hsl(262 83% 58%)' },
} satisfies ChartConfig;
</script>