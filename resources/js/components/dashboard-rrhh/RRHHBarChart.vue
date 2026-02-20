<template>
  <Card class="border-border/80">
    <CardHeader>
      <CardTitle>Tendencia semanal</CardTitle>
    </CardHeader>
    <CardContent class="pt-0">
      <ChartContainer :config="chartConfig" class="h-80 aspect-auto">
        <template #default>
          <VisXYContainer
            :data="rrhhTrendMock"
            :margin="{ top: 16, bottom: 32, left: 36, right: 16 }"
            class="h-full w-full"
          >
            <VisLine
              :x="(_d: RRHHTrendDatum, i: number) => i"
              :y="(d: RRHHTrendDatum) => d.celulares"
              :color="chartConfig.celular.color"
              :stroke-width="2.5"
            />
            <VisLine
              :x="(_d: RRHHTrendDatum, i: number) => i"
              :y="(d: RRHHTrendDatum) => d.cargadores"
              :color="chartConfig.cargador.color"
              :stroke-width="2.5"
            />
            <VisScatter
              :x="(_d: RRHHTrendDatum, i: number) => i"
              :y="(d: RRHHTrendDatum) => d.celulares"
              :color="chartConfig.celular.color"
              :size="8"
            />
            <VisScatter
              :x="(_d: RRHHTrendDatum, i: number) => i"
              :y="(d: RRHHTrendDatum) => d.cargadores"
              :color="chartConfig.cargador.color"
              :size="8"
            />
            <VisAxis type="x" :tick-values="[0, 1, 2, 3, 4]" :tick-format="(i: number) => rrhhTrendMock[i]?.period ?? ''" />
            <VisAxis type="y" :tick-values="[0, 2, 4, 6, 8, 10]" />
            <VisTooltip />
          </VisXYContainer>
        </template>
      </ChartContainer>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer } from '@/components/ui/chart';
import { VisAxis, VisLine, VisScatter, VisTooltip, VisXYContainer } from '@unovis/vue';
import { rrhhTrendMock, type RRHHTrendDatum } from './mock';

const chartConfig = {
  celular: { label: 'Celular', color: 'hsl(224 76% 48%)' },
  cargador: { label: 'Cargador de Celular', color: 'hsl(38 92% 50%)' },
};
</script>
