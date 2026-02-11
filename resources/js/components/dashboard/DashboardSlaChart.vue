<script setup lang="ts">
import { computed } from 'vue';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { VisAxis, VisLine, VisScatter, VisScatterSelectors, VisTooltip, VisXYContainer } from '@unovis/vue';

interface SlaDatum {
    day: string;
    compliance: number;
    breach: number;
}

const slaData: SlaDatum[] = [
    { day: 'Lun', compliance: 92, breach: 8 },
    { day: 'Mar', compliance: 95, breach: 5 },
    { day: 'Mie', compliance: 88, breach: 12 },
    { day: 'Jue', compliance: 91, breach: 9 },
    { day: 'Vie', compliance: 97, breach: 3 },
    { day: 'Sab', compliance: 93, breach: 7 },
    { day: 'Dom', compliance: 90, breach: 10 },
];

const chartConfig = {
    compliance: { label: 'Cumplido', color: 'hsl(142 76% 36%)' },
    breach: { label: 'Incumplido', color: 'hsl(0 84% 60%)' },
};

const tickValues = slaData.map((_item, index) => index);

const buildTooltipContent = (d: SlaDatum): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex flex-col gap-1';

    const title = document.createElement('div');
    title.className = 'font-medium text-foreground';
    title.textContent = d.day;

    const compliance = document.createElement('div');
    compliance.className = 'text-xs text-muted-foreground';
    compliance.textContent = `Cumplido: ${d.compliance}%`;

    const breach = document.createElement('div');
    breach.className = 'text-xs text-muted-foreground';
    breach.textContent = `Incumplido: ${d.breach}%`;

    wrapper.appendChild(title);
    wrapper.appendChild(compliance);
    wrapper.appendChild(breach);

    return wrapper;
};

const tooltipTriggers = computed(() => ({
    [VisScatterSelectors.point]: (d: SlaDatum) => buildTooltipContent(d),
}));
</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>Cumplimiento de SLA</CardTitle>
            <CardDescription>Semana actual comparando cumplimiento e incumplimiento.</CardDescription>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-[20rem] aspect-auto">
                <template #default>
                    <div class="flex-1">
                        <VisXYContainer
                            :data="slaData"
                            :margin="{ top: 12, bottom: 32, left: 40, right: 16 }"
                            class="h-full w-full"
                        >
                            <VisLine
                                :x="(_d: SlaDatum, index: number) => index"
                                :y="(d: SlaDatum) => d.compliance"
                                :stroke-width="2.5"
                                :color="chartConfig.compliance.color"
                            />
                            <VisLine
                                :x="(_d: SlaDatum, index: number) => index"
                                :y="(d: SlaDatum) => d.breach"
                                :stroke-width="2"
                                :color="chartConfig.breach.color"
                            />
                            <VisScatter
                                :x="(_d: SlaDatum, index: number) => index"
                                :y="(d: SlaDatum) => d.compliance"
                                :size="10"
                                :color="chartConfig.compliance.color"
                                :stroke-color="'#ffffff'"
                                :stroke-width="2"
                            />
                            <VisScatter
                                :x="(_d: SlaDatum, index: number) => index"
                                :y="(d: SlaDatum) => d.breach"
                                :size="10"
                                :color="chartConfig.breach.color"
                                :stroke-color="'#ffffff'"
                                :stroke-width="2"
                            />
                            <VisAxis
                                type="x"
                                :tick-values="tickValues"
                                :tick-format="(value: number) => slaData[value]?.day || ''"
                            />
                            <VisAxis type="y" :domain="[0, 100]" :tick-values="[0, 25, 50, 75, 100]" />
                            <VisTooltip
                                :triggers="tooltipTriggers"
                                class-name="rounded-lg border border-border/50 bg-background px-2.5 py-2 text-xs shadow-xl"
                            />
                        </VisXYContainer>
                    </div>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>
