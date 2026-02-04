<template>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Card v-for="stat in stats" :key="stat.label" class="border-border/60 shadow-sm overflow-hidden"
            :class="stat.bg">
            <CardContent class="p-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl flex items-center justify-center" :class="stat.iconBg">
                            <component :is="stat.icon" class="h-5 w-5" :class="stat.color" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold">{{ stat.value }}</p>
                            <p class="text-xs text-muted-foreground">{{ stat.label }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-1 rounded-full border" :class="stat.badge">
                        {{ stat.badgeText }}
                    </span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { CircleCheckBig, DollarSign, FileText, TriangleAlert } from 'lucide-vue-next';

const stats = [
    {
        label: 'Total contratos',
        value: '12',
        icon: FileText,
        color: 'text-primary',
        iconBg: 'bg-primary/10',
        bg: 'bg-linear-to-br from-primary/10 via-background to-background',
        badgeText: '+2 este mes',
        badge: 'border-primary/30 text-primary',
    },
    {
        label: 'Valor total anual',
        value: '$184k',
        icon: DollarSign,
        color: 'text-success',
        iconBg: 'bg-success/10',
        bg: 'bg-linear-to-br from-success/10 via-background to-background',
        badgeText: '↑ 6%',
        badge: 'border-success/30 text-success',
    },
    {
        label: 'Activos',
        value: '8',
        icon: CircleCheckBig,
        color: 'text-success',
        iconBg: 'bg-success/10',
        bg: 'bg-linear-to-br from-emerald-500/10 via-background to-background',
        badgeText: 'Estables',
        badge: 'border-emerald-400/30 text-emerald-500',
    },
    {
        label: 'Requieren atención',
        value: '4',
        icon: TriangleAlert,
        color: 'text-warning',
        iconBg: 'bg-warning/10',
        bg: 'bg-linear-to-br from-warning/10 via-background to-background',
        badgeText: 'Urgente',
        badge: 'border-warning/30 text-warning',
    },
];
</script>
