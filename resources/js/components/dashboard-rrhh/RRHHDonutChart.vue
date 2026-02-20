<template>
  <Card class="border-border/80">
    <CardHeader>
      <CardTitle>Distribución por tipo</CardTitle>
    </CardHeader>
    <CardContent class="pt-0">
      <ChartContainer :config="chartConfig" class="h-80 aspect-auto">
        <template #default>
          <VisSingleContainer :data="rrhhTypeDistributionMock" class="h-full w-full">
            <VisDonut
              :value="(d: RRHHChartDatum) => d.value"
              :pad-angle="0.02"
              :corner-radius="10"
              :inner-radius="0.62"
              :outer-radius="0.96"
              :color="(d: RRHHChartDatum) => d.color"
            />
            <VisTooltip />
          </VisSingleContainer>
        </template>
      </ChartContainer>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer } from '@/components/ui/chart';
import { VisDonut, VisSingleContainer, VisTooltip } from '@unovis/vue';
import { rrhhTypeDistributionMock, type RRHHChartDatum } from './mock';

const chartConfig = {
  celular: { label: 'Celular', color: 'hsl(224 76% 48%)' },
  cargador: { label: 'Cargador de Celular', color: 'hsl(38 92% 50%)' },
};
</script>
