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
            <Badge variant="outline" class="border-warning/50 text-warning bg-warning/15 ring-1 ring-warning/30">
                {{ alerts.length }} alertas
            </Badge>
        </CardHeader>
        <CardContent>
            <div class="grid w-full max-w-xl items-start gap-4">
                <Alert v-for="alert in alerts" :key="alert.id"
                    class="relative overflow-hidden ring-1 shadow-sm"
                    :class="periodStyles[alert.period].card">
                    <span class="absolute inset-y-0 left-0 w-1.5" :class="periodStyles[alert.period].bar" />
                    <span class="h-9 w-9 rounded-full border flex items-center justify-center ring-2"
                          :class="periodStyles[alert.period].iconWrap">
                        <component :is="periodStyles[alert.period].icon" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <AlertTitle class="text-sm font-medium truncate">{{ alert.title }}</AlertTitle>
                        <AlertDescription class="text-xs opacity-70 mt-1">Vence: {{ alert.expiresAt }}</AlertDescription>
                        <AlertDescription class="text-[11px] opacity-70 mt-1">{{ alert.meta }}</AlertDescription>
                        <AlertDescription class="mt-2">
                            <Badge variant="outline" :class="periodStyles[alert.period].badge">
                                {{ periodStyles[alert.period].label }}
                            </Badge>
                        </AlertDescription>
                    </div>
                </Alert>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertTriangle, BellRing, TriangleAlert } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';


const alerts = [
    {
        id: 1,
        title: 'Soporte SAP ERP vence en 15 días',
        expiresAt: '2024-02-28',
        severity: 'warning',
        meta: 'Contrato CTR-002 · Soporte',
        period: 'RECURRING',
    },
    {
        id: 2,
        title: 'Garantía servidor PE740 vencida',
        expiresAt: '2024-01-10',
        severity: 'critical',
        meta: 'Activo: SRV-PE740 · Hardware',
        period: 'FIXED_TERM',
    },
    {
        id: 3,
        title: 'Antivirus CrowdStrike - Renovar urgente',
        expiresAt: '2024-01-15',
        severity: 'critical',
        meta: 'Contrato CTR-003 · Licencia',
        period: 'ONE_TIME',
    },
    {
        id: 4,
        title: 'Mantenimiento preventivo UPS programado',
        expiresAt: '2024-02-01',
        severity: 'info',
        meta: 'Evento programado · Infraestructura',
        period: 'RECURRING',
    },
    {
        id: 5,
        title: 'AWS Hosting - Revisión anual en 60 días',
        expiresAt: '2025-01-01',
        severity: 'info',
        meta: 'Contrato CTR-004 · Servicio',
        period: 'FIXED_TERM',
    },
];

const periodStyles = {
    RECURRING: {
        label: 'Recurrente',
        card: 'bg-linear-to-br from-emerald-500/20 via-emerald-500/10 to-background text-emerald-700 border-emerald-500/30 pl-6 ring-emerald-500/20 shadow-emerald-500/20',
        bar: 'bg-emerald-500',
        icon: AlertTriangle,
        iconWrap: 'bg-emerald-500/15 border-emerald-500/40 ring-emerald-500/30 text-emerald-600',
        badge: 'border-emerald-500/40 text-emerald-700 bg-emerald-500/10 text-[10px]',
    },
    FIXED_TERM: {
        label: 'Plazo fijo',
        card: 'bg-linear-to-br from-sky-500/20 via-sky-500/10 to-background text-sky-700 border-sky-500/30 pl-6 ring-sky-500/20 shadow-sky-500/20',
        bar: 'bg-sky-500',
        icon: TriangleAlert,
        iconWrap: 'bg-sky-500/15 border-sky-500/40 ring-sky-500/30 text-sky-600',
        badge: 'border-sky-500/40 text-sky-700 bg-sky-500/10 text-[10px]',
    },
    ONE_TIME: {
        label: 'Único',
        card: 'bg-linear-to-br from-violet-500/20 via-violet-500/10 to-background text-violet-700 border-violet-500/30 pl-6 ring-violet-500/20 shadow-violet-500/20',
        bar: 'bg-violet-500',
        icon: AlertTriangle,
        iconWrap: 'bg-violet-500/15 border-violet-500/40 ring-violet-500/30 text-violet-600',
        badge: 'border-violet-500/40 text-violet-700 bg-violet-500/10 text-[10px]',
    },
};
</script>
