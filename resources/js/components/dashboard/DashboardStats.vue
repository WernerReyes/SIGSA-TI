<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Code2, Monitor, Ticket, TriangleAlert } from 'lucide-vue-next';

const stats = [
    {
        title: 'Tickets abiertos',
        value: '47',
        helper: '12 sin asignar',
        delta: '↓ 8%',
        deltaClass: 'bg-destructive/10 text-destructive border-destructive/20',
        icon: Ticket,
        cardClass: 'border-primary/15 bg-primary/5',
        iconClass: 'text-primary bg-primary/10',
    },
    {
        title: 'Fuera de SLA',
        value: '5',
        helper: 'Requieren atencion inmediata',
        delta: 'Critico',
        deltaClass: 'bg-destructive/10 text-destructive border-destructive/20',
        icon: TriangleAlert,
        cardClass: 'border-destructive/20 bg-destructive/5',
        iconClass: 'text-destructive bg-destructive/10',
    },
    {
        title: 'Activos en riesgo',
        value: '12',
        helper: 'Garantias proximas a vencer',
        delta: '6 nuevos',
        deltaClass: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
        icon: Monitor,
        cardClass: 'border-amber-500/20 bg-amber-500/5',
        iconClass: 'text-amber-600 bg-amber-500/10',
    },
    {
        title: 'Proyectos activos',
        value: '8',
        helper: '3 en fase de desarrollo',
        delta: '↑ 15%',
        deltaClass: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
        icon: Code2,
        cardClass: 'border-emerald-500/20 bg-emerald-500/5',
        iconClass: 'text-emerald-600 bg-emerald-500/10',
    },
];
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card v-for="stat in stats" :key="stat.title" :class="stat.cardClass">
            <CardContent class="flex items-start justify-between gap-4 py-6">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-muted-foreground">
                        {{ stat.title }}
                    </p>
                    <div class="flex items-center gap-3">
                        <p class="text-3xl font-semibold text-foreground">
                            {{ stat.value }}
                        </p>
                        <Badge class="border" :class="stat.deltaClass" variant="secondary">
                            {{ stat.delta }}
                        </Badge>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ stat.helper }}
                    </p>
                </div>
                <div class="rounded-lg p-3" :class="stat.iconClass">
                    <component :is="stat.icon" class="h-5 w-5" />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
