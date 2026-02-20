<template>
    <Card class="rounded-lg border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Tasa de Utilización</CardTitle>
        </CardHeader>
        <CardContent class="pt-0 flex flex-col items-center justify-center">
            <div class="h-45 w-full">
                <VisSingleContainer :data="gaugeData" class="h-full w-full">
                    <VisDonut :show-background="true" :value="(d: GaugeDatum) => d.value"
                        :angle-range="[-1.5707963267948966, 1.5707963267948966]" :inner-radius="0.68"
                        :outer-radius="0.92" :color="(d: GaugeDatum) => d.color" />
                </VisSingleContainer>
            </div>
            <p class="text-3xl font-bold text-primary -mt-8">{{ utilizationPercent }}%</p>
            <p class="text-xs text-muted-foreground mt-1">Celulares asignados vs total</p>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type Asset, AssetStatus } from '@/interfaces/asset.interface';
import { VisDonut, VisSingleContainer } from '@unovis/vue';
import { computed } from 'vue';

interface GaugeDatum {
    value: number;
    color: string;
}

const smartphoneTypeId = 3;

const mockAssets: Asset[] = [
    { id: 1, type_id: smartphoneTypeId, model: 'iPhone 13', serial_number: 'APL-001', processor: 'A15', ram: '4 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'iPhone RH-01', full_name: 'Apple iPhone 13', description: 'Equipo RH', is_new: true },
    { id: 2, type_id: smartphoneTypeId, model: 'Galaxy A54', serial_number: 'SMS-002', processor: 'Exynos', ram: '8 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Galaxy RH-01', full_name: 'Samsung Galaxy A54', description: 'Equipo RH', is_new: true },
    { id: 3, type_id: smartphoneTypeId, model: 'Moto G84', serial_number: 'MOT-003', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.AVAILABLE, brand: 'Motorola', created_at: new Date(), updated_at: new Date(), name: 'Moto RH-01', full_name: 'Motorola Moto G84', description: 'Equipo RH', is_new: false },
    { id: 4, type_id: smartphoneTypeId, model: 'Redmi Note 13', serial_number: 'XIA-004', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Xiaomi', created_at: new Date(), updated_at: new Date(), name: 'Redmi RH-01', full_name: 'Xiaomi Redmi Note 13', description: 'Equipo RH', is_new: true },
];

const totalSmartphones = computed(() => mockAssets.length);
const assignedSmartphones = computed(() =>
    mockAssets.filter((asset) => asset.status === AssetStatus.ASSIGNED).length,
);

const utilizationRate = computed(() => {
    if (!totalSmartphones.value) {
        return 0;
    }
    return assignedSmartphones.value / totalSmartphones.value;
});

const utilizationPercent = computed(() => Math.round(utilizationRate.value * 100));

const gaugeData = computed<GaugeDatum[]>(() => [
    {
        value: utilizationRate.value,
        color: 'hsl(224 76% 48%)',
    },
]);
</script>