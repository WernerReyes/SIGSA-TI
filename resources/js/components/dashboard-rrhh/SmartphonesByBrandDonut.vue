<template>
    <!-- {{ assetsByBrand }} -->
    <Card class="border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Celulares por Marca</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="aspect-auto">
                <template #default>
                    <div class="relative h-full w-full">
                        <VisSingleContainer :data="donutData" class="h-full w-full">
                            <VisDonut :value="(d: BrandDonutDatum) => d.value" :pad-angle="0.02" :corner-radius="10"
                                :inner-radius="0.62" :outer-radius="0.94" :color="(d: BrandDonutDatum) => d.color" />
                            <VisTooltip :triggers="tooltipTriggers" />
                        </VisSingleContainer>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xs uppercase tracking-widest text-muted-foreground">Total</span>
                            <span class="text-2xl font-semibold text-foreground">{{ totalSmartphones }}</span>
                        </div>
                    </div>
                    <ChartLegendContent class="pt-3 flex flex-wrap" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script lang="ts" setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import { buildTooltipContent, getRandomColor } from '@/lib/utils';
import { VisDonut, VisDonutSelectors, VisSingleContainer, VisTooltip } from '@unovis/vue';
import { computed } from 'vue';


const { assetsByBrand } = defineProps<{
    assetsByBrand: RRHHDashboard['assetsByBrand'];
}>();

interface BrandDonutDatum {
    key: string;
    label: string;
    value: number;
    color: string;
}

const tooltipTriggers = computed(() => ({
    [VisDonutSelectors.segment]: (d: {
    data: { label: string; color: string };
    value: number;
    }) => buildTooltipContent({
        total: totalSmartphones.value,
        data: { label: d.data.label, color: d.data.color },
        value: d.value,
    }, 'celulares'),
}));


const totalSmartphones = computed(() => {
    return assetsByBrand.reduce((sum, item) => sum + item.total, 0);
});

const chartConfig = computed(() =>
    donutData.value.reduce((acc, item) => {
        acc[item.key] = { label: item.label, color: item.color };
        return acc;
    }, {} as Record<string, { label: string; color: string }>),
);


const donutData = computed<BrandDonutDatum[]>(() => {
    return assetsByBrand.map((item) => ({
        key: item.brand.toLowerCase().replaceAll(' ', '_'),
        label: item.brand,
        value: item.total,
        color: getRandomColor(item.brand),
    }));
});






</script>