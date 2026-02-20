<template>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <Card v-for="item in statsMock" :key="item.label" class="bg-card rounded-xl border shadow-card p-4 animate-fade-in">
            <CardContent class="p-0">
                <div class="flex items-center gap-3">
                    <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', item.iconContainerClass]">
                        <component :is="item.icon" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ item.value }}</p>
                        <p class="text-xs text-muted-foreground">{{ item.label }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import {
    BatteryCharging,
    Package,
    ShieldOff,
    Smartphone,
    Users,
    Wrench,
} from 'lucide-vue-next';
import type { Component } from 'vue';

interface RRHHStatItem {
    label: string;
    value: number;
    icon: Component;
    iconContainerClass: string;
}

const statsMock: RRHHStatItem[] = [
    {
        label: 'Celulares',
        value: 8,
        icon: Smartphone,
        iconContainerClass: 'bg-primary/10 text-primary',
    },
    {
        label: 'Cargadores',
        value: 10,
        icon: BatteryCharging,
        iconContainerClass: 'bg-chart-5/10 text-chart-5',
    },
    {
        label: 'Total Activos',
        value: 18,
        icon: Package,
        iconContainerClass: 'bg-info/10 text-info',
    },
    {
        label: 'Asignados',
        value: 6,
        icon: Users,
        iconContainerClass: 'bg-success/10 text-success',
    },
    {
        label: 'Garantía Vencida',
        value: 5,
        icon: ShieldOff,
        iconContainerClass: 'bg-critical/10 text-critical',
    },
    {
        label: 'En Mantenimiento',
        value: 2,
        icon: Wrench,
        iconContainerClass: 'bg-warning/10 text-warning',
    },
];
</script>
