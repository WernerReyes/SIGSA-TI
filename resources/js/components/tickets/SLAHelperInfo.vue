<template>

    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" size="icon" class="shadow-sm hover:shadow-md" aria-label="Ayuda SLA">
                <Info class="size-4" />
            </Button>
        </PopoverTrigger>
        <PopoverContent align="end" class="w-105 p-0 overflow-hidden">
            <div class="p-4 bg-linear-to-br from-muted/70 to-background">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <Info class="size-4" />
                    </div>
                    <div>
                        <div class="font-semibold">Ayuda SLA</div>
                        <p class="text-xs text-muted-foreground">
                            Los tiempos se cuentan solo dentro del horario laboral.
                        </p>
                    </div>
                </div>
            </div>
            <div class="p-4 grid gap-4 text-sm">
                <div class="grid gap-2">
                    <div class="flex items-center gap-2 text-[11px] font-semibold uppercase text-muted-foreground">
                        <CalendarDays class="size-3.5" />
                        Horario laboral
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-md border bg-background/60 p-2">
                            <div class="font-medium">Lun - Vie</div>
                            <div class="text-muted-foreground">8:30 - 18:00</div>
                        </div>
                        <div class="rounded-md border bg-background/60 p-2">
                            <div class="font-medium">Sabados</div>
                            <div class="text-muted-foreground">8:30 - 12:00</div>
                        </div>
                        <div class="rounded-md border bg-background/60 p-2 flex items-center gap-2 col-span-2">
                            <Utensils class="size-3.5 text-muted-foreground" />
                            <div class="text-muted-foreground">Almuerzo: 13:00 - 14:00 (no se cuenta)</div>
                        </div>
                        <div class="rounded-md border bg-background/60 p-2 flex items-center gap-2 col-span-2">
                            <Ban class="size-3.5 text-muted-foreground" />
                            <div class="text-muted-foreground">Feriados: no se cuentan</div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center gap-2 text-[11px] font-semibold uppercase text-muted-foreground">
                        <Clock class="size-3.5" />
                        Tiempos por prioridad
                    </div>
                    <div
                        class="grid grid-cols-3 gap-2 rounded-md border bg-muted/30 px-3 py-2 text-[11px] font-semibold">
                        <div>Prioridad</div>
                        <div class="text-center">Respuesta</div>
                        <div class="text-center">Solucion</div>
                    </div>
                    <WhenVisible data="slaPolicies" class="grid gap-2">
                        <template #fallback>
                            <Skeleton v-for=" _ in 4" :key="_" class="h-8 w-full rounded-md" />
                        </template>

                        <div v-for="policy in slaPolicies" :key="policy.priority"
                            class="grid grid-cols-3 items-center gap-2 rounded-md border bg-background/60 px-3 py-2 text-xs">

                            <Badge :class="getTicketOp('priority', policy.priority)?.bg">
                                <component :is="getTicketOp('priority', policy.priority)?.icon" />
                                {{ policy.label }}
                            </Badge>

                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center rounded-full  px-2 py-0.5 text-[10px] font-semibold">
                                    {{ formatMinutes(policy.response_time_minutes) }}
                                </span>
                            </div>
                            <div class="flex justify-center">
                                <span
                                    class="inline-flex items-center rounded-full  px-2 py-0.5 text-[10px] font-semibold">
                                    {{ formatMinutes(policy.resolution_time_minutes) }}
                                </span>
                            </div>
                        </div>
                    </WhenVisible>
                    
                </div>
            </div>
        </PopoverContent>
    </Popover>


</template>

<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useApp } from '@/composables/useApp';
import { getTicketOp } from '@/interfaces/ticket.interface';
import { WhenVisible } from '@inertiajs/vue3';
import { Ban, CalendarDays, Clock, Info, Utensils } from 'lucide-vue-next';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';

const { slaPolicies } = useApp();


const formatMinutes = (minutes: number): string => {
    if (!Number.isFinite(minutes)) return '-';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hrs && mins) return `${hrs}h ${mins}m`;
    if (hrs) return `${hrs}h`;
    return `${mins}m`;
};


</script>