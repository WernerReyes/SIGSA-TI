<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Clock, ShieldAlert, TriangleAlert } from 'lucide-vue-next';

const alerts = [
    {
        title: 'Contrato Microsoft por vencer',
        description: 'El contrato #CT-2024-001 vence en 15 dias',
        time: 'Hace 2 horas',
        tone: 'warning',
        icon: TriangleAlert,
    },
    {
        title: '3 equipos con garantia proxima a expirar',
        description: 'Laptops Dell XPS del area de Finanzas',
        time: 'Hace 5 horas',
        tone: 'warning',
        icon: TriangleAlert,
    },
    {
        title: 'Servidor DB-01 con alto uso de CPU',
        description: 'Uso sostenido por encima del 90%',
        time: 'Hace 30 min',
        tone: 'critical',
        icon: ShieldAlert,
    },
    {
        title: 'Backup completado exitosamente',
        description: 'Respaldo nocturno finalizado sin errores',
        time: 'Hace 8 horas',
        tone: 'info',
        icon: Clock,
    },
];

const toneStyles: Record<string, { card: string; badge: string }> = {
    warning: {
        card: 'border-l-4 border-amber-500/60 bg-amber-500/5',
        badge: 'border-amber-500/20 bg-amber-500/10 text-amber-700',
    },
    critical: {
        card: 'border-l-4 border-destructive/70 bg-destructive/5',
        badge: 'border-destructive/20 bg-destructive/10 text-destructive',
    },
    info: {
        card: 'border-l-4 border-sky-500/60 bg-sky-500/5',
        badge: 'border-sky-500/20 bg-sky-500/10 text-sky-700',
    },
};
</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>Alertas activas</CardTitle>
            <CardDescription>Eventos que requieren seguimiento inmediato.</CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div
                v-for="alert in alerts"
                :key="alert.title"
                class="rounded-lg border border-border/60 p-4 transition-colors hover:bg-muted/40"
                :class="toneStyles[alert.tone].card"
            >
                <div class="flex items-start gap-3">
                    <div class="rounded-md bg-background/80 p-2">
                        <component :is="alert.icon" class="h-4 w-4" />
                    </div>
                    <div class="flex-1 space-y-2">
                        <p class="text-sm font-medium text-foreground">
                            {{ alert.title }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ alert.description }}
                        </p>
                        <div class="flex items-center gap-2">
                            <Badge class="border" :class="toneStyles[alert.tone].badge" variant="secondary">
                                {{ alert.time }}
                            </Badge>
                            <span class="text-[11px] text-muted-foreground">Prioridad {{ alert.tone }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
