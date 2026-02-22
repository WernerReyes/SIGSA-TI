<script setup lang="ts">
import type { ChartConfig } from "@/components/ui/chart"
import { VisArea, VisAxis, VisLine, VisXYContainer } from "@unovis/vue"
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
} from "@/components/ui/chart"
import { type RRHHDashboard } from "@/interfaces/dashboard.interface"

const { monthlyAssignments } = defineProps<{
    title: string;
  
    monthlyAssignments: RRHHDashboard['monthlyAssignments'];
}>()

type Data = typeof monthlyAssignments[number]

const chartConfig = {
    smartphones: {
        label: "Teléfonos",
        color: "var(--chart-1)",
    },
    chargers: {
        label: "Cargadores",
        color: "var(--chart-2)",
    },
} satisfies ChartConfig

const svgDefs = `
  <linearGradient id="fillSmartphones" x1="0" y1="0" x2="0" y2="1">
    <stop
      offset="5%"
            stop-color="var(--color-smartphones)"
      stop-opacity="0.8"
    />
    <stop
      offset="95%"
            stop-color="var(--color-smartphones)"
      stop-opacity="0.1"
    />
  </linearGradient>
  <linearGradient id="fillChargers" x1="0" y1="0" x2="0" y2="1">
    <stop
      offset="5%"
            stop-color="var(--color-chargers)"
      stop-opacity="0.8"
    />
    <stop
      offset="95%"
            stop-color="var(--color-chargers)"
      stop-opacity="0.1"
    />
  </linearGradient>
`
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>

        </CardHeader>
        <CardContent>
            <ChartContainer :config="chartConfig" class="h-72 w-full">
                <VisXYContainer :data="monthlyAssignments" :svg-defs="svgDefs">
                    <VisArea :x="(d: Data) => d.month" :y="[(d: Data) => d.chargers, (d: Data) => d.smartphones]"
                        :color="(d: Data, i: number) => ['url(#fillChargers)', 'url(#fillSmartphones)'][i]" :opacity="0.4" />
                    <VisLine :x="(d: Data) => d.month" :y="[(d: Data) => d.chargers, (d: Data) => d.smartphones]"
                        :color="(d: Data, i: number) => [chartConfig.chargers.color, chartConfig.smartphones.color][i]"
                        :line-width="1" />
                    <VisAxis type="x" :x="(d: Data) => d.month" :tick-line="false" :domain-line="false"
                        :grid-line="false" :num-ticks="monthlyAssignments.length"
                        :tick-format="(value: number) => monthlyAssignments.find((d) => d.month === value)?.label || ''" />
                    <VisAxis type="y" :num-ticks="3" :tick-line="false" :domain-line="false"
                        :tick-format="(d: number, index: number) => ''" />
                    <ChartTooltip />
                    <ChartCrosshair
                        :template="componentToString(chartConfig, ChartTooltipContent, { labelKey: 'label' })"
                        :color="(d: Data, i: number) => [chartConfig.chargers.color, chartConfig.smartphones.color][i % 2]" />
                </VisXYContainer>
            </ChartContainer>
        </CardContent>

    </Card>
</template>

