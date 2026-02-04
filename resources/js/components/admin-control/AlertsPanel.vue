<template>
    <Card class="border-warning/30 bg-linear-to-br from-warning/10 via-background to-background shadow-sm">
        <CardHeader class="flex-row items-center justify-between border-b border-warning/20 bg-warning/5">
            <div class="space-y-1">
                <CardTitle class="text-base flex items-center gap-2">
                    <span class="h-8 w-8 rounded-full bg-warning/15 text-warning flex items-center justify-center">
                        <BellRing class="h-4 w-4" />
                    </span>
                    Alertas activas
                </CardTitle>
                <CardDescription>
                    Revisa vencimientos y tareas críticas próximas.
                </CardDescription>
            </div>
            <Badge variant="outline" class="border-warning/40 text-warning bg-warning/10">
                {{ alerts.length }} alertas
            </Badge>
        </CardHeader>
        <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                <div v-for="alert in alerts" :key="alert.id"
                    class="rounded-lg border p-3 flex items-start gap-3 relative overflow-hidden"
                    :class="alertStyles[alert.severity].card">
                    <span class="absolute inset-y-0 left-0 w-1" :class="alertStyles[alert.severity].bar" />
                    <span class="h-8 w-8 rounded-full bg-background/70 border flex items-center justify-center">
                        <component :is="alertStyles[alert.severity].icon" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ alert.title }}</p>
                        <p class="text-xs opacity-70 mt-1">Vence: {{ alert.expiresAt }}</p>
                        <p class="text-[11px] opacity-70 mt-1">{{ alert.meta }}</p>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertTriangle, BellRing, TriangleAlert } from 'lucide-vue-next';

const alerts = [
    {
        id: 1,
        title: 'Soporte SAP ERP vence en 15 días',
        expiresAt: '2024-02-28',
        severity: 'warning',
        meta: 'Contrato CTR-002 · Soporte',
    },
    {
        id: 2,
        title: 'Garantía servidor PE740 vencida',
        expiresAt: '2024-01-10',
        severity: 'critical',
        meta: 'Activo: SRV-PE740 · Hardware',
    },
    {
        id: 3,
        title: 'Antivirus CrowdStrike - Renovar urgente',
        expiresAt: '2024-01-15',
        severity: 'critical',
        meta: 'Contrato CTR-003 · Licencia',
    },
    {
        id: 4,
        title: 'Mantenimiento preventivo UPS programado',
        expiresAt: '2024-02-01',
        severity: 'info',
        meta: 'Evento programado · Infraestructura',
    },
    {
        id: 5,
        title: 'AWS Hosting - Revisión anual en 60 días',
        expiresAt: '2025-01-01',
        severity: 'info',
        meta: 'Contrato CTR-004 · Servicio',
    },
];

const alertStyles = {
    warning: {
        card: 'bg-warning/10 text-warning border-warning/30 pl-5',
        bar: 'bg-warning',
        icon: AlertTriangle,
    },
    critical: {
        card: 'bg-critical/10 text-critical border-critical/30 pl-5',
        bar: 'bg-critical',
        icon: TriangleAlert,
    },
    info: {
        card: 'bg-info/10 text-info border-info/30 pl-5',
        bar: 'bg-info',
        icon: AlertTriangle,
    },
};
</script>
