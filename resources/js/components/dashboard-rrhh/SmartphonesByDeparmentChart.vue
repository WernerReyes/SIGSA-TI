<template>
    <Card class="rounded-lg border bg-card text-card-foreground shadow-sm shadow-card">
        <CardHeader class="pb-2">
            <CardTitle class="tracking-tight text-base font-semibold">Celulares y Cargadores por Departamento</CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-125 w-full">
                <template #default>
                    <VisXYContainer :data="departmentData" :margin="{ top: 6, bottom: 24,  right: 10 }" class="h-full w-full">
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
                            type="y"
                            :tick-values="departmentData.map((d) => d.order)"
                            :tick-format="(value: number) => departmentData.find((d) => d.order === value)?.department || ''"
                            :tick-line="false"
                            :domain-line="false"
                            :grid-line="false"
                        />
                        <VisAxis
                            type="x"
                            :tick-line="false"
                            :domain-line="false"
                            :grid-line="true"
                        />

                        <VisTooltip :triggers="tooltipTriggers" />
                    </VisXYContainer>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type ChartConfig, ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import { VisAxis, VisGroupedBar, VisGroupedBarSelectors, VisTooltip, VisXYContainer } from '@unovis/vue';
import { computed } from 'vue';


const { assetsByDepartment } = defineProps<{
    assetsByDepartment: RRHHDashboard['assetsByDepartment'];
}>();

interface DepartmentDatum {
    order: number;
    department: string;
    celulares: number;
    cargadores: number;
}


const departmentData = computed<DepartmentDatum[]>(() => {
    return assetsByDepartment.map((department, index) => ({
        order: index,
        department: department.department,
        celulares: department.smartphones,
        cargadores: department.chargers,
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

const tooltipTriggers = computed(() => ({
    [VisGroupedBarSelectors.bar]: (rawDatum: unknown) => {
        const datum = ((rawDatum as { data?: DepartmentDatum }).data ?? rawDatum) as DepartmentDatum;
        return `
            <div class="border-border/50 bg-background grid min-w-[10rem] items-start gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs shadow-xl">
                <div class="font-medium">${datum.department}</div>
                <div class="grid gap-1.5">
                    <div class="flex w-full items-center gap-2">
                        <div class="h-2.5 w-2.5 shrink-0 rounded-[2px]" style="background:${chartConfig.celulares.color}"></div>
                        <div class="flex flex-1 items-center justify-between leading-none">
                            <span class="text-muted-foreground">${chartConfig.celulares.label}</span>
                            <span class="text-foreground font-mono font-medium tabular-nums">${(datum.celulares ?? 0).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="flex w-full items-center gap-2">
                        <div class="h-2.5 w-2.5 shrink-0 rounded-[2px]" style="background:${chartConfig.cargadores.color}"></div>
                        <div class="flex flex-1 items-center justify-between leading-none">
                            <span class="text-muted-foreground">${chartConfig.cargadores.label}</span>
                            <span class="text-foreground font-mono font-medium tabular-nums">${(datum.cargadores ?? 0).toLocaleString()}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },
}));


// const totalSmartphones = computed(() => warrantyData.value.reduce((sum, item) => sum + item.value, 0));


</script>