<template>
    <Card class="rounded-lg border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Celulares y Cargadores por Departamento</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-72 w-full">
                <template #default>
                    <VisXYContainer :data="departmentData" :margin="{ top: 6, bottom: 24, left: 96, right: 10 }" class="h-full w-full">
                        <VisGroupedBar
                            orientation="horizontal"
                            :x="(d: DepartmentDatum) => d.order"
                            :y="[(d: DepartmentDatum) => d.celulares, (d: DepartmentDatum) => d.cargadores]"
                            :color="[chartConfig.celulares.color, chartConfig.cargadores.color]"
                            :rounded-corners="4"
                            bar-padding="0.1"
                            group-padding="0.15"
                        />

                        <VisAxis
                            type="x"
                            :tick-line="false"
                            :domain-line="false"
                            :grid-line="true"
                            :tick-values="[100, 200, 300, 400, 500]"
                        />
                        <VisAxis
                            type="y"
                            :tick-line="false"
                            :domain-line="false"
                            :grid-line="true"
                            :tick-values="departmentData.map((d) => d.order)"
                            :tick-format="(value: number) => departmentData.find((d) => d.order === value)?.department || ''"
                        />

                        <ChartTooltip />
                    </VisXYContainer>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type ChartConfig, ChartContainer, ChartLegendContent, ChartTooltip } from '@/components/ui/chart';
import { type Asset, AssetStatus } from '@/interfaces/asset.interface';
import { VisAxis, VisGroupedBar, VisXYContainer } from '@unovis/vue';
import { computed } from 'vue';

interface DepartmentDatum {
    order: number;
    department: string;
    celulares: number;
    cargadores: number;
}

const smartphoneTypeId = 3;
const chargerTypeId = 12;

const mockAssets: Asset[] = [
    { id: 1, type_id: smartphoneTypeId, model: 'iPhone 13', serial_number: 'APL-001', processor: 'A15', ram: '4 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'iPhone RH-01', full_name: 'Apple iPhone 13', description: 'Gerencia', is_new: true },
    { id: 2, type_id: smartphoneTypeId, model: 'Galaxy A54', serial_number: 'SMS-002', processor: 'Exynos', ram: '8 GB', storage: '128 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Galaxy RH-01', full_name: 'Samsung Galaxy A54', description: 'Ventas', is_new: true },
    { id: 3, type_id: smartphoneTypeId, model: 'Moto G84', serial_number: 'MOT-003', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.AVAILABLE, brand: 'Motorola', created_at: new Date(), updated_at: new Date(), name: 'Moto RH-01', full_name: 'Motorola Moto G84', description: 'Marketing', is_new: false },
    { id: 4, type_id: chargerTypeId, model: '20W', serial_number: 'CHG-004', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Apple', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-01', full_name: 'Cargador Apple 20W', description: 'Gerencia', is_new: true },
    { id: 5, type_id: chargerTypeId, model: '25W', serial_number: 'CHG-005', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.AVAILABLE, brand: 'Samsung', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-02', full_name: 'Cargador Samsung 25W', description: 'Stock', is_new: true },
    { id: 6, type_id: chargerTypeId, model: 'USB-C', serial_number: 'CHG-006', processor: '-', ram: '-', storage: '-', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.DECOMMISSIONED, brand: 'Anker', created_at: new Date(), updated_at: new Date(), name: 'Cargador RH-03', full_name: 'Cargador Anker USB-C', description: 'Soporte', is_new: false },
    { id: 7, type_id: smartphoneTypeId, model: 'Redmi Note 13', serial_number: 'XIA-007', processor: 'Snapdragon', ram: '8 GB', storage: '256 GB', purchase_date: '2025-01-01', warranty_expiration: '2027-01-01', status: AssetStatus.ASSIGNED, brand: 'Xiaomi', created_at: new Date(), updated_at: new Date(), name: 'Redmi RH-01', full_name: 'Xiaomi Redmi Note 13', description: 'Stock', is_new: true },
];

const departments = ['Gerencia', 'Ventas', 'Marketing', 'Finanzas', 'Operaciones', 'Soporte', 'Stock'];

const departmentData = computed<DepartmentDatum[]>(() => {
    return departments.map((department, index) => ({
        order: index,
        department,
        celulares: mockAssets.filter((asset) => asset.type_id === smartphoneTypeId && asset.description === department).length,
        cargadores: mockAssets.filter((asset) => asset.type_id === chargerTypeId && asset.description === department).length,
    }));
});

const chartConfig = {
    celulares: {
        label: 'Celulares',
        color: 'hsl(224 76% 48%)',
    },
    cargadores: {
        label: 'Cargadores',
        color: 'hsl(262 83% 58%)',
    },
} satisfies ChartConfig;
</script>