<template>
    <Card class="border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Celulares por Marca</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-65 aspect-auto">
                <template #default>
                    <div class="relative h-full w-full">
                        <VisSingleContainer :data="donutData" class="h-full w-full">
                            <VisDonut :value="(d: BrandDonutDatum) => d.value" :pad-angle="0.02" :corner-radius="10"
                                :inner-radius="0.62" :outer-radius="0.94" :color="(d: BrandDonutDatum) => d.color" />
                            <VisTooltip />
                        </VisSingleContainer>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xs uppercase tracking-widest text-muted-foreground">Total</span>
                            <span class="text-2xl font-semibold text-foreground">{{ totalSmartphones }}</span>
                        </div>
                    </div>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script lang="ts" setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import type { Asset } from '@/interfaces/asset.interface';
import { AssetStatus } from '@/interfaces/asset.interface';
import { VisDonut, VisSingleContainer, VisTooltip } from '@unovis/vue';
import { computed } from 'vue';

interface BrandDonutDatum {
    key: string;
    label: string;
    value: number;
    color: string;
}

const smartphoneTypeId = 3;

const mockAssets: Asset[] = [
    {
        id: 1,
        type_id: smartphoneTypeId,
        color: 'Negro',
        model: 'iPhone 13',
        serial_number: 'APL-001',
        processor: 'A15',
        ram: '4 GB',
        storage: '128 GB',
        purchase_date: '2025-01-10',
        warranty_expiration: '2027-01-10',
        status: AssetStatus.ASSIGNED,
        brand: 'Apple',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'iPhone 13 RH-01',
        full_name: 'Apple iPhone 13',
        description: 'Celular corporativo',
        is_new: true,
    },
    {
        id: 2,
        type_id: smartphoneTypeId,
        color: 'Azul',
        model: 'Galaxy A54',
        serial_number: 'SMS-002',
        processor: 'Exynos',
        ram: '8 GB',
        storage: '128 GB',
        purchase_date: '2025-02-01',
        warranty_expiration: '2027-02-01',
        status: AssetStatus.AVAILABLE,
        brand: 'Samsung',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'Galaxy A54 RH-01',
        full_name: 'Samsung Galaxy A54',
        description: 'Celular corporativo',
        is_new: true,
    },
    {
        id: 3,
        type_id: smartphoneTypeId,
        color: 'Gris',
        model: 'Redmi Note 13',
        serial_number: 'XIA-003',
        processor: 'Snapdragon',
        ram: '8 GB',
        storage: '256 GB',
        purchase_date: '2025-03-20',
        warranty_expiration: '2027-03-20',
        status: AssetStatus.ASSIGNED,
        brand: 'Xiaomi',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'Redmi RH-01',
        full_name: 'Xiaomi Redmi Note 13',
        description: 'Celular corporativo',
        is_new: false,
    },
    {
        id: 4,
        type_id: smartphoneTypeId,
        color: 'Negro',
        model: 'Moto G84',
        serial_number: 'MOT-004',
        processor: 'Snapdragon',
        ram: '8 GB',
        storage: '256 GB',
        purchase_date: '2025-04-15',
        warranty_expiration: '2027-04-15',
        status: AssetStatus.IN_REPAIR,
        brand: 'Motorola',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'Moto RH-01',
        full_name: 'Motorola Moto G84',
        description: 'Celular corporativo',
        is_new: false,
    },
    {
        id: 5,
        type_id: smartphoneTypeId,
        color: 'Blanco',
        model: 'iPhone 12',
        serial_number: 'APL-005',
        processor: 'A14',
        ram: '4 GB',
        storage: '128 GB',
        purchase_date: '2024-11-10',
        warranty_expiration: '2026-11-10',
        status: AssetStatus.ASSIGNED,
        brand: 'Apple',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'iPhone 12 RH-01',
        full_name: 'Apple iPhone 12',
        description: 'Celular corporativo',
        is_new: false,
    },
    {
        id: 6,
        type_id: smartphoneTypeId,
        color: 'Verde',
        model: 'Galaxy S23 FE',
        serial_number: 'SMS-006',
        processor: 'Exynos',
        ram: '8 GB',
        storage: '256 GB',
        purchase_date: '2025-05-08',
        warranty_expiration: '2027-05-08',
        status: AssetStatus.AVAILABLE,
        brand: 'Samsung',
        created_at: new Date(),
        updated_at: new Date(),
        name: 'Galaxy RH-02',
        full_name: 'Samsung Galaxy S23 FE',
        description: 'Celular corporativo',
        is_new: true,
    },
];

const brandColors: Record<string, string> = {
    Apple: 'hsl(224 76% 48%)',
    Samsung: 'hsl(142 76% 36%)',
    Xiaomi: 'hsl(38 92% 50%)',
    Motorola: 'hsl(262 83% 58%)',
};

const donutData = computed<BrandDonutDatum[]>(() => {
    const smartphones = mockAssets.filter((asset) => asset.type_id === smartphoneTypeId);
    const grouped = smartphones.reduce<Record<string, number>>((acc, asset) => {
        const brand = asset.brand || 'Sin marca';
        acc[brand] = (acc[brand] || 0) + 1;
        return acc;
    }, {});

    return Object.entries(grouped).map(([brand, value]) => ({
        key: brand.toLowerCase().replaceAll(' ', '_'),
        label: brand,
        value,
        color: brandColors[brand] ?? 'hsl(215 20% 65%)',
    }));
});

const totalSmartphones = computed(() => donutData.value.reduce((sum, item) => sum + item.value, 0));

const chartConfig = computed(() =>
    donutData.value.reduce((acc, item) => {
        acc[item.key] = { label: item.label, color: item.color };
        return acc;
    }, {} as Record<string, { label: string; color: string }>),
);
</script>