<template>
    <Card class="rounded-lg border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Estado de Garantías</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-65 aspect-auto">
                <template #default>
                    <div class="relative h-full w-full">
                        <VisSingleContainer :data="warrantyData" class="h-full w-full">
                            <VisDonut :value="(d: WarrantyDatum) => d.value" :pad-angle="0.02" :corner-radius="8"
                                :arcWidth="140" :color="(d: WarrantyDatum) => d.color" />
                            <ChartTooltip />
                        </VisSingleContainer>
                    </div>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script lang="ts" setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type ChartConfig, ChartContainer, ChartLegendContent, ChartTooltip } from '@/components/ui/chart';
import { type Asset, AssetStatus } from '@/interfaces/asset.interface';
import { VisDonut, VisSingleContainer } from '@unovis/vue';
import { computed } from 'vue';

interface WarrantyDatum {
    key: string;
    label: string;
    value: number;
    color: string;
}

const smartphoneTypeId = 3;
const chargerTypeId = 12;
const soonThresholdDays = 60;

const mockAssets: Asset[] = [
    { id: 1, type_id: smartphoneTypeId, model: 'iPhone 13', serial_number: 'APL-001', processor: 'A15', ram: '4 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-02-01', status: AssetStatus.ASSIGNED, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'iPhone RH-01', full_name: 'Apple iPhone 13', description: 'Equipo RH', is_new: true },
    { id: 2, type_id: smartphoneTypeId, model: 'Galaxy A54', serial_number: 'SMS-002', processor: 'Exynos', ram: '8 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2026-03-15', status: AssetStatus.AVAILABLE, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Galaxy RH-01', full_name: 'Samsung Galaxy A54', description: 'Equipo RH', is_new: true },
    { id: 3, type_id: smartphoneTypeId, model: 'Moto G84', serial_number: 'MOT-003', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2025-11-15', status: AssetStatus.IN_REPAIR, brand: 'Motorola', created_at: new Date(), updated_at: new Date(), name: 'Moto RH-01', full_name: 'Motorola Moto G84', description: 'Equipo RH', is_new: false },
    { id: 4, type_id: chargerTypeId, model: '20W', serial_number: 'CHG-004', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2026-04-01', status: AssetStatus.AVAILABLE, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-01', full_name: 'Cargador Apple 20W', description: 'Accesorio RH', is_new: true },
    { id: 5, type_id: chargerTypeId, model: '25W', serial_number: 'CHG-005', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '', status: AssetStatus.ASSIGNED, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-02', full_name: 'Cargador Samsung 25W', description: 'Accesorio RH', is_new: true },
    { id: 6, type_id: chargerTypeId, model: 'USB-C', serial_number: 'CHG-006', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '', status: AssetStatus.DECOMMISSIONED, brand: 'Anker', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-03', full_name: 'Cargador Anker USB-C', description: 'Accesorio RH', is_new: false },
];

const warrantyData = computed<WarrantyDatum[]>(() => {
    const now = new Date();
    const assets = mockAssets.filter((asset) => [smartphoneTypeId, chargerTypeId].includes(asset.type_id));

    const counters = {
        vigente: 0,
        por_vencer: 0,
        vencida: 0,
        sin_garantia: 0,
    };

    assets.forEach((asset) => {
        if (!asset.warranty_expiration) {
            counters.sin_garantia += 1;
            return;
        }

        const expirationDate = new Date(asset.warranty_expiration);
        if (Number.isNaN(expirationDate.getTime())) {
            counters.sin_garantia += 1;
            return;
        }

        const diffDays = (expirationDate.getTime() - now.getTime()) / (1000 * 60 * 60 * 24);
        if (diffDays < 0) {
            counters.vencida += 1;
        } else if (diffDays <= soonThresholdDays) {
            counters.por_vencer += 1;
        } else {
            counters.vigente += 1;
        }
    });

    return [
        { key: 'vigente', label: 'Vigente', value: counters.vigente, color: 'hsl(142 76% 36%)' },
        { key: 'por_vencer', label: 'Por vencer', value: counters.por_vencer, color: 'hsl(38 92% 50%)' },
        { key: 'vencida', label: 'Vencida', value: counters.vencida, color: 'hsl(0 84% 60%)' },
        { key: 'sin_garantia', label: 'Sin garantía', value: counters.sin_garantia, color: 'hsl(215 16% 47%)' },
    ];
});

const chartConfig = computed<ChartConfig>(() =>
    warrantyData.value.reduce((acc, item) => {
        acc[item.key] = {
            label: `${item.label}: ${item.value}`,
            color: item.color,
        };
        return acc;
    }, {} as ChartConfig),
);
</script>