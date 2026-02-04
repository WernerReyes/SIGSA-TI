<template>
    <Card class="border-border/60 shadow-sm bg-linear-to-br from-muted/40 via-background to-background">
        <CardHeader class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <CardTitle class="text-base flex items-center gap-2">
                    <span class="h-9 w-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <HardHat class="h-4 w-4" />
                    </span>
                    Bitácora de eventos
                </CardTitle>
                <CardDescription>
                    Registro de cambios de infraestructura y mantenimientos programados.
                </CardDescription>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button size="sm" variant="outline">Filtrar</Button>
                <Button size="sm" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Registrar evento
                </Button>
            </div>
        </CardHeader>
        <CardContent>
            <div class="space-y-4">
                <div v-for="event in events" :key="event.id"
                    class="rounded-lg border bg-card p-4 transition hover:bg-muted/40 relative overflow-hidden"
                    :class="event.accent">
                    <span class="absolute inset-y-0 left-0 w-1" :class="event.accentBar" />
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 rounded-full bg-background/70 border flex items-center justify-center">
                                <component :is="event.icon" class="h-4 w-4" :class="event.color" />
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold">{{ event.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ event.description }}</p>
                                <div class="flex flex-wrap items-center gap-2 text-[11px] text-muted-foreground">
                                    <Badge variant="outline" class="text-[11px]">
                                        {{ event.type }}
                                    </Badge>
                                    <span>{{ event.system }}</span>
                                    <span>•</span>
                                    <span>{{ event.author }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground sm:text-right">
                            <p class="font-medium text-foreground">{{ event.date }}</p>
                            <p>{{ event.time }}</p>
                        </div>
                    </div>
                    <Separator class="my-3" />
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge :class="event.statusStyle" class="text-[11px]">
                            {{ event.status }}
                        </Badge>
                        <Badge variant="secondary" class="text-[11px]">
                            {{ event.impact }}
                        </Badge>
                        <Badge variant="outline" class="text-[11px]">
                            {{ event.window }}
                        </Badge>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { HardHat, Plus, Server, ShieldCheck, Wrench } from 'lucide-vue-next';

const events = [
    {
        id: 1,
        title: 'Actualización de firmware en clúster de servidores',
        description: 'Actualización coordinada para mejorar rendimiento y seguridad.',
        type: 'Cambio',
        system: 'Infraestructura Core',
        author: 'Equipo TI',
        date: '04/02/2026',
        time: '08:30',
        status: 'Completado',
        statusStyle: 'bg-success/10 text-success',
        impact: 'Bajo impacto',
        window: 'Ventana programada',
        icon: Server,
        color: 'text-primary',
        accent: 'pl-5 border-primary/30',
        accentBar: 'bg-primary',
    },
    {
        id: 2,
        title: 'Mantenimiento preventivo UPS Data Center',
        description: 'Revisión y pruebas de autonomía.',
        type: 'Mantenimiento',
        system: 'Data Center',
        author: 'Infraestructura',
        date: '03/02/2026',
        time: '21:00',
        status: 'Programado',
        statusStyle: 'bg-warning/10 text-warning',
        impact: 'Impacto moderado',
        window: 'Ventana nocturna',
        icon: Wrench,
        color: 'text-warning',
        accent: 'pl-5 border-warning/30',
        accentBar: 'bg-warning',
    },
    {
        id: 3,
        title: 'Rotación de certificados de seguridad',
        description: 'Renovación de certificados TLS y llaves de acceso.',
        type: 'Seguridad',
        system: 'Servicios Web',
        author: 'Seguridad TI',
        date: '01/02/2026',
        time: '11:15',
        status: 'En seguimiento',
        statusStyle: 'bg-info/10 text-info',
        impact: 'Sin interrupción',
        window: 'Automatizado',
        icon: ShieldCheck,
        color: 'text-info',
        accent: 'pl-5 border-info/30',
        accentBar: 'bg-info',
    },
    {
        id: 4,
        title: 'Revisión de respaldos mensuales',
        description: 'Verificación de integridad y restauración de prueba.',
        type: 'Auditoría',
        system: 'Backups',
        author: 'Operaciones TI',
        date: '30/01/2026',
        time: '17:45',
        status: 'Completado',
        statusStyle: 'bg-success/10 text-success',
        impact: 'Sin impacto',
        window: 'Programado',
        icon: HardHat,
        color: 'text-success',
        accent: 'pl-5 border-success/30',
        accentBar: 'bg-success',
    },
];
</script>
