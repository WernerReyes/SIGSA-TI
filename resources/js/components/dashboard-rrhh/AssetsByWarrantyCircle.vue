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
                            <ChartTooltip 
                            :triggers="tooltipTriggers"
                            />
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
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import { buildTooltipContent } from '@/lib/utils';
import { VisDonut, VisDonutSelectors, VisSingleContainer } from '@unovis/vue';
import { computed } from 'vue';


const { smartphonesWarrantyStatus } = defineProps<{
    smartphonesWarrantyStatus: RRHHDashboard['smartphonesWarrantyStatus'];
}>();

interface WarrantyDatum {
    key: string;
    label: string;
    value: number;
    color: string;
}
const warrantyData = computed<WarrantyDatum[]>(() => {
    return [
        { key: 'vigente', label: 'Vigente', value: smartphonesWarrantyStatus?.in_warranty ?? 0, color: 'hsl(142 76% 36%)' },
        { key: 'por_vencer', label: 'Por vencer', value: smartphonesWarrantyStatus?.expiring_soon ?? 0, color: 'hsl(38 92% 50%)' },
        { key: 'vencida', label: 'Vencida', value: smartphonesWarrantyStatus?.expired ?? 0, color: 'hsl(0 84% 60%)' },
        { key: 'sin_garantia', label: 'Sin garantía', value: smartphonesWarrantyStatus?.unknown ?? 0, color: 'hsl(215 16% 47%)' },
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

const totalSmartphones = computed(() => warrantyData.value.reduce((sum, item) => sum + item.value, 0));

const tooltipTriggers = computed(() => ({
    [VisDonutSelectors.segment]: (d: {
        data: { label: string; color: string };
        value: number;
    }) => {
        return buildTooltipContent({
            data: { label: d.data.label, color: d.data.color },
            value: d.value,
            total: totalSmartphones.value
        }, 'celulares');
    },
}));
</script>