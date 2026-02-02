<template>
    <div class="w-full h-64 bg-muted/10 rounded-lg p-4">
        <VisXYContainer :data="data" :margin="{ top: 10, bottom: 30, left: 40, right: 10 }" class="w-full h-full">
            <VisLine
                ref="lineRef"
                :x="(d: DevelopmentProgress) => new Date(d.created_at).getTime()"
                :y="(d: DevelopmentProgress) => d.percentage"
                color="#3b82f6"
                :stroke-width="2"
            />
            <VisScatter
                ref="scatterRef"
                :x="(d: DevelopmentProgress) => new Date(d.created_at).getTime()"
                :y="(d: DevelopmentProgress) => d.percentage"
                color="#3b82f6"
                :size="8"
                :stroke-color="'#ffffff'"
                :stroke-width="2"
            />
            <VisTooltip
                :components="tooltipComponents"
                :triggers="tooltipTriggers"
                class-name="rounded-lg border border-border/50 bg-background px-2.5 py-2 text-xs shadow-xl"
            />
            <VisAxis type="x" :tick-format="(ms: number) => format(new Date(ms), 'dd MMM', { locale: es })" />
            <VisAxis type="y" :domain="[0, 100]" :tick-values="[0, 25, 50, 75, 100]" />
        </VisXYContainer>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { VisXYContainer, VisLine, VisAxis, VisScatter, VisTooltip, VisScatterSelectors } from '@unovis/vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import type { DevelopmentProgress } from '@/interfaces/developmentProgress.interface';

interface Props {
    data: DevelopmentProgress[];
}

defineProps<Props>();

const lineRef = ref();
const scatterRef = ref();

const tooltipComponents = computed(() =>
    [lineRef.value?.component, scatterRef.value?.component].filter(Boolean),
);

const buildTooltipContent = (d: DevelopmentProgress): HTMLElement => {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex max-w-56 flex-col gap-1';

    const title = document.createElement('div');
    title.className = 'font-medium text-foreground';
    title.textContent = format(new Date(d.created_at), 'dd MMM yyyy HH:mm', { locale: es });

    const percentage = document.createElement('strong');
    percentage.className = 'text-muted-foreground';
    percentage.textContent = `Avance: ${d.percentage}%`;

    const notes = document.createElement('div');
    notes.className = 'text-muted-foreground';
    notes.textContent = d.notes ? `Notas: ${d.notes}` : 'Notas: sin notas';

    wrapper.appendChild(title);
    wrapper.appendChild(percentage);
    wrapper.appendChild(notes);

    return wrapper;
};

const tooltipTriggers = computed(() => ({
    [VisScatterSelectors.point]: (d: DevelopmentProgress) => buildTooltipContent(d),
}));
</script>
