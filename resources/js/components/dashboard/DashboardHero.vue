<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { ArrowUpRight, CalendarDays, Sparkles } from 'lucide-vue-next';
</script>

<template>
    <Card class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 via-card to-muted/40">
        <div class="pointer-events-none absolute -right-24 -top-20 h-56 w-56 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-primary/70 via-primary/20 to-transparent"></div>
        <CardContent class="relative flex flex-col gap-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <Badge class="bg-primary/15 text-primary hover:bg-primary/20" variant="secondary">
                        <Sparkles class="h-3 w-3" />
                        Operacion estable
                    </Badge>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <CalendarDays class="h-3.5 w-3.5" />
                        Actualizado hoy 09:45
                    </div>
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-foreground sm:text-3xl">
                        Panel de operaciones TI
                    </h1>
                    <p class="max-w-xl text-sm text-muted-foreground sm:text-base">
                        Seguimiento en tiempo real de tickets, SLA y alertas clave. Enfoca a tu equipo en lo critico.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                    <div class="flex items-center gap-2 rounded-full border border-border/60 bg-background/70 px-3 py-1">
                        <span class="font-medium text-foreground">98%</span>
                        SLA semanal
                    </div>
                    <div class="flex items-center gap-2 rounded-full border border-border/60 bg-background/70 px-3 py-1">
                        <span class="font-medium text-foreground">14</span>
                        activos en monitoreo
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <Button as="a" href="/tickets/create" class="gap-2">
                    Nuevo ticket
                    <ArrowUpRight class="h-4 w-4" />
                </Button>
                <Button as="a" href="/tickets" variant="outline">
                    Ver detalle
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
