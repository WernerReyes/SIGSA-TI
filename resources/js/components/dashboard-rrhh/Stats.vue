<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <Card
            v-for="item in statsFormatted"
            :key="item.label"
            class="group relative overflow-hidden rounded-xl border bg-card shadow-card transition-all duration-300 hover:-translate-y-0.5 hover:border-border/80 animate-fade-in"
        >
            <div :class="['pointer-events-none absolute inset-x-0 top-0 h-1', item.accentBarClass]" />

            <CardContent class="p-4">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <p class="text-xs font-medium leading-none text-muted-foreground">{{ item.label }}</p>

                    <div
                        :class="['h-10 w-10 shrink-0 rounded-lg flex items-center justify-center transition-transform duration-300 group-hover:scale-105', item.iconContainerClass]"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                    </div>
                </div>

                <div class="flex items-end justify-between gap-2">
                    <p class="text-3xl font-bold leading-none tracking-tight text-foreground">{{ item.value }}</p>
                    <span :class="['inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium', item.badgeClass]">
                        Activos
                    </span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { type RRHHDashboard } from '@/interfaces/dashboard.interface';
import {
    BatteryCharging,
    Package,
    ShieldOff,
    Smartphone,
    Users,
    Wrench,
} from 'lucide-vue-next';
import { computed, type Component } from 'vue';


const { stats } = defineProps<{
    stats: RRHHDashboard['stats'];
}>();

interface RRHHStatItem {
    label: string;
    value: number;
    icon: Component;
    iconContainerClass: string;
    accentBarClass: string;
    badgeClass: string;
}

const statsFormatted = computed((): RRHHStatItem[] => {
    return [
        {
            label: 'Celulares',
            value: stats.smartphones,
            icon: Smartphone,
            iconContainerClass: 'bg-primary/10 text-primary',
            accentBarClass: 'bg-primary/70',
            badgeClass: 'bg-primary/10 text-primary',
        },
        {
            label: 'Cargadores',
            value: stats.chargers,
            icon: BatteryCharging,
            iconContainerClass: 'bg-chart-5/10 text-chart-5',
            accentBarClass: 'bg-chart-5/70',
            badgeClass: 'bg-chart-5/10 text-chart-5',
        },
        {
            label: 'Total Activos',
            value: stats.total,
            icon: Package,
            iconContainerClass: 'bg-info/10 text-info',
            accentBarClass: 'bg-info/70',
            badgeClass: 'bg-info/10 text-info',
        },
        {
            label: 'Asignados',
            value: stats.assigned,
            icon: Users,
            iconContainerClass: 'bg-success/10 text-success',
            accentBarClass: 'bg-success/70',
            badgeClass: 'bg-success/10 text-success',
        },
        {
            label: 'Garantía Vencida',
            value: stats.expired_warranty,
            icon: ShieldOff,
            iconContainerClass: 'bg-critical/10 text-critical',
            accentBarClass: 'bg-critical/70',
            badgeClass: 'bg-critical/10 text-critical',
        },
        {
            label: 'En Mantenimiento',
            value: stats.under_maintenance,
            icon: Wrench,
            iconContainerClass: 'bg-warning/10 text-warning',
            accentBarClass: 'bg-warning/70',
            badgeClass: 'bg-warning/10 text-warning',
        },
    ];
});

// const statsSerialized: RRHHStatItem[] = [
//     {
//         label: 'Celulares',
//         value: props.stats.smartphones,
//         icon: Smartphone,
//         iconContainerClass: 'bg-primary/10 text-primary',
//         accentBarClass: 'bg-primary/70',
//         badgeClass: 'bg-primary/10 text-primary',
//     },
//     {
//         label: 'Cargadores',
//         value: 10,
//         icon: BatteryCharging,
//         iconContainerClass: 'bg-chart-5/10 text-chart-5',
//         accentBarClass: 'bg-chart-5/70',
//         badgeClass: 'bg-chart-5/10 text-chart-5',
//     },
//     {
//         label: 'Total Activos',
//         value: 18,
//         icon: Package,
//         iconContainerClass: 'bg-info/10 text-info',
//         accentBarClass: 'bg-info/70',
//         badgeClass: 'bg-info/10 text-info',
//     },
//     {
//         label: 'Asignados',
//         value: 6,
//         icon: Users,
//         iconContainerClass: 'bg-success/10 text-success',
//         accentBarClass: 'bg-success/70',
//         badgeClass: 'bg-success/10 text-success',
//     },
//     {
//         label: 'Garantía Vencida',
//         value: 5,
//         icon: ShieldOff,
//         iconContainerClass: 'bg-critical/10 text-critical',
//         accentBarClass: 'bg-critical/70',
//         badgeClass: 'bg-critical/10 text-critical',
//     },
//     {
//         label: 'En Mantenimiento',
//         value: 2,
//         icon: Wrench,
//         iconContainerClass: 'bg-warning/10 text-warning',
//         accentBarClass: 'bg-warning/70',
//         badgeClass: 'bg-warning/10 text-warning',
//     },
// ];
</script>
