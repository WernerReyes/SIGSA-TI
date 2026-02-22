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
            <p class="text-3xl font-bold text-primary -mt-8">{{ assignedProfiRate }}%</p>
            <p class="text-xs text-muted-foreground mt-1">Celulares asignados vs total</p>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { VisDonut, VisSingleContainer } from '@unovis/vue';
import { computed } from 'vue';


const { assignedProfiRate }  = defineProps<{
    assignedProfiRate: number;
}>();

interface GaugeDatum {
    value: number;
    color: string;
}


const gaugeData = computed<GaugeDatum[]>(() => [
    {
        value: assignedProfiRate,
        color: 'hsl(224 76% 48%)',
    },
]);


</script>