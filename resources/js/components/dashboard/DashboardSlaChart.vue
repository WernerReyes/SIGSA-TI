<template>
    <Card class="border-border/80">
        <CardHeader>
            <div class="flex flex-row items-center justify-between gap-4">
                <div>
                    <CardTitle>Cumplimiento de SLA</CardTitle>
                    <CardDescription>Semana actual comparando cumplimiento e incumplimiento.</CardDescription>
                </div>

                
            </div>
        </CardHeader>
        <CardContent class="pt-0">
            <ChartContainer :config="chartConfig" class="h-80 aspect-auto">
                <template #default>
                    <div class="flex-1">
                        <VisXYContainer :data="dailySlaData" :margin="{ top: 12, bottom: 32, left: 40, right: 16 }"
                            class="h-full w-full">
                            <VisLine :x="(_d: SlaDatum, index: number) => index" :y="(d: SlaDatum) => d.complied"
                                :stroke-width="2.5" :color="chartConfig.compliance.color" />
                            <VisLine :x="(_d: SlaDatum, index: number) => index" :y="(d: SlaDatum) => d.breached"
                                :stroke-width="2" :color="chartConfig.breach.color" />
                            <VisScatter :x="(_d: SlaDatum, index: number) => index" :y="(d: SlaDatum) => d.complied"
                                :size="10" :color="chartConfig.compliance.color" :stroke-color="'#ffffff'"
                                :stroke-width="2" />
                            <VisScatter :x="(_d: SlaDatum, index: number) => index" :y="(d: SlaDatum) => d.breached"
                                :size="10" :color="chartConfig.breach.color" :stroke-color="'#ffffff'"
                                :stroke-width="2" />
                            <VisAxis type="x" :tick-values="tickValues"
                                :tick-format="(value: number) => dailySlaData[value]?.date || ''" />
                            <VisAxis type="y" :domain="[0, 100]" :tick-values="[0, 25, 50, 75, 100]" />
                            <VisTooltip :triggers="tooltipTriggers"
                                class-name="border-none bg-transparent p-0 shadow-none" />
                        </VisXYContainer>
                    </div>
                    <ChartLegendContent class="pt-3" />
                </template>
            </ChartContainer>
        </CardContent>
    </Card>
</template>


<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartLegendContent } from '@/components/ui/chart';
import { type DashboardSLACompliance } from '@/interfaces/dashboard.interface';
import { parseDateOnly } from '@/lib/utils';
import { VisAxis, VisLine, VisScatter, VisScatterSelectors, VisTooltip, VisXYContainer } from '@unovis/vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed } from 'vue';

const { slaCompliance } = defineProps<{
    slaCompliance: DashboardSLACompliance;
}>();


type SlaDatum = DashboardSLACompliance['daily'][number];






const dailySlaData = computed(() => {
    return slaCompliance.daily.map(d => ({
        date: format(parseDateOnly(d.date), 'eee MMM dd yyyy', {
            locale: es
        }),
        complied: d.complied,
        breached: d.breached,
        compliance_rate: d.compliance_rate,
        breach_rate: d.breach_rate,
    }));
});

const chartConfig = {
    compliance: { label: 'Cumplido', color: 'hsl(142 76% 36%)' },
    breach: { label: 'Incumplido', color: 'hsl(0 84% 60%)' },
};

const tickValues = computed(() => slaCompliance.daily.map((_, index) => index));

const buildTooltipContent = (d: SlaDatum): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'min-w-[190px] rounded-xl border border-border/40 bg-background/90 p-3 shadow-2xl backdrop-blur-md';

    wrapper.innerHTML = `
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full ring-2 ring-background" style="background:${chartConfig.compliance.color}"></span>
                    <span class="text-[11px] uppercase tracking-wide text-muted-foreground">${d.complied} Cumplido${d.complied !== 1 ? 's' : ''}</span>
                </div>
                <span class="rounded-full border border-border/40 bg-background/60 px-2 py-0.5 text-[10px] font-medium text-foreground/80">${d.compliance_rate}%</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full ring-2 ring-background" style="background:${chartConfig.breach.color}"></span>
                    <span class="text-[11px] uppercase tracking-wide text-muted-foreground">${d.breached} Incumplido${d.breached !== 1 ? 's' : ''}</span>
                </div>
                <span class="rounded-full border border-border/40 bg-background/60 px-2 py-0.5 text-[10px] font-medium text-foreground/80">${d.breach_rate}%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-xs font-medium text-foreground">${d.date}</span>
                <span class="text-[10px] text-muted-foreground">SLA diario</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="h-1 w-full rounded-full" style="background:${chartConfig.compliance.color}"></div>
                <div class="h-1 w-full rounded-full" style="background:${chartConfig.breach.color}"></div>
            </div>
        </div>
    `;

    return wrapper;
};

const tooltipTriggers = computed(() => ({
    [VisScatterSelectors.point]: (d: SlaDatum) => buildTooltipContent(d),
}));
</script>