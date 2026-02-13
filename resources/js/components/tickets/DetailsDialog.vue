<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-4xl">
            <DialogHeader class="space-y-3 pb-4">
                <div
                    class="flex items-start gap-4 p-4 rounded-xl bg-linear-to-br from-muted/40 via-background to-background border">
                    <div
                        class="size-16 rounded-xl bg-linear-to-br from-cyan-100/40 to-blue-100/40 dark:from-cyan-900/30 dark:to-blue-900/30 flex items-center justify-center ring-2 ring-blue-200/50 dark:ring-blue-800/50 shadow-sm">
                        <TicketIcon class="size-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-mono text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded-md">TK-{{
                                ticket?.id }}</p>
                            <Badge class="text-xs" :class="typeOp(ticket?.type)?.bg">
                                <component :is="typeOp(ticket?.type)?.icon" />
                                {{ typeOp(ticket?.type)?.label }}
                            </Badge>
                        </div>
                        <h2 class="font-bold tracking-tight text-2xl">{{ ticket?.title }}</h2>
                        <p class="text-sm text-muted-foreground mt-1">{{ ticket?.description }}</p>
                    </div>
                </div>
            </DialogHeader>

            <ScrollArea class="dialog-content">
            <div class="grid lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 grid md:grid-cols-2 gap-3">
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <User class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Solicitante</p>

                                    <!-- :disabled="!values.requester_id" -->
                                    <Button
                                    type="button"
                                    variant="outline"
                                    size="icon-sm"
                                    class=""
                                    @click="openRequesterAssets = true"
                                >
                                    <MonitorSmartphone class="size-4" />
                                </Button>

                               
                            </div>

                            <p class="text-sm font-semibold mt-1">{{ ticket?.requester?.full_name ?? 'Sin dato' }}</p>
                            <p v-if="ticket?.requester?.department" class="text-xs text-muted-foreground mt-0.5">{{
                                ticket?.requester?.department?.name }}</p>
                            <p v-else class="text-xs text-muted-foreground mt-0.5">Sin departamento</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <Zap class="size-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Asignado a</p>
                            <p v-if="ticket?.responsible" class="text-sm font-semibold mt-1">{{
                                ticket?.responsible?.full_name }}</p>
                            <Badge v-else variant="secondary" class="mt-1">Sin asignar</Badge>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                            <Tag class="size-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Categoría</p>
                            <Badge v-if="ticket?.category" :class="requestTypeOp(ticket?.category)?.bg" class="mt-1">
                                {{ requestTypeOp(ticket?.category)?.label }}
                            </Badge>
                            <Badge v-else variant="secondary" class="mt-1">Sin categoría</Badge>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg flex items-center justify-center shrink-0"
                            :class="priorityOp(ticket?.priority)?.bg?.includes('red') ? 'bg-red-100 dark:bg-red-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30'">
                            <AlertTriangle
                                :class="priorityOp(ticket?.priority)?.bg?.includes('red') ? 'size-5 text-red-600 dark:text-red-400' : 'size-5 text-yellow-600 dark:text-yellow-400'" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Prioridad</p>
                            <Badge :class="priorityOp(ticket?.priority)?.bg" class="mt-1">
                                {{ priorityOp(ticket?.priority)?.label ?? 'Sin dato' }}
                            </Badge>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <CheckCircle2 class="size-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Estado</p>
                            <Badge :class="statusOp(ticket?.status)?.bg" class="mt-1">
                                {{ statusOp(ticket?.status)?.label ?? 'Sin dato' }}
                            </Badge>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <Calendar class="size-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Creado</p>
                            <p class="text-sm font-semibold mt-1">{{ formatDate(ticket?.created_at) }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">Actualizado: {{
                                formatDate(ticket?.updated_at) }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center shrink-0">
                            <Gauge class="size-5 text-sky-600 dark:text-sky-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Impacto</p>
                            <Badge variant="outline" class="mt-1">{{ impactLabels[ticket?.impact ?? ''] ?? 'Sin dato' }}
                            </Badge>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div
                            class="size-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                            <Activity class="size-5 text-rose-600 dark:text-rose-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Urgencia</p>
                            <Badge variant="outline" class="mt-1">{{ urgencyLabels[ticket?.urgency ?? ''] ?? 'Sin dato'
                                }}</Badge>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- SLA Response -->
                    <div class="rounded-lg border bg-card overflow-hidden">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase text-muted-foreground px-4 py-3 bg-muted/30">
                            <Clock class="size-4" />
                            SLA Respuesta
                        </div>
                        <div class="p-4 space-y-3">
                            <div v-if="ticket?.sla_response_time_minutes" class="space-y-2">
                                <Badge 
                                    v-if="ticket.sla_response_time_minutes.late_minutes" 
                                    class="bg-red-500 text-white w-full justify-center py-2"
                                >
                                    <Clock class="size-4" />
                                    {{ formatMinutes(ticket.sla_response_time_minutes.late_minutes) }} tarde
                                </Badge>
                                <Badge 
                                    v-else-if="ticket.sla_response_time_minutes.before_late_minutes" 
                                    class="bg-green-500 text-white w-full justify-center py-2"
                                >
                                    <CheckCircle2 class="size-4" />
                                    {{ formatMinutes(ticket.sla_response_time_minutes.before_late_minutes) }} antes
                                </Badge>
                                <Badge 
                                    v-else-if="ticket.sla_response_time_minutes.remaining_minutes" 
                                    class="bg-yellow-500 text-white w-full justify-center py-2"
                                >
                                    <Clock class="size-4" />
                                    {{ formatMinutes(ticket.sla_response_time_minutes.remaining_minutes) }} restantes
                                </Badge>
                            </div>
                            <div class="grid gap-2 text-xs">
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-muted-foreground">Debe responder</span>
                                    <span class="font-medium">{{ formatDate(ticket?.sla_response_due_at) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-muted-foreground">Primera respuesta</span>
                                    <span class="font-medium" :class="{ 'text-muted-foreground/60 italic': !ticket?.first_response_at }">
                                        {{ formatDate(ticket?.first_response_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SLA Resolution -->
                    <div class="rounded-lg border bg-card overflow-hidden">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase text-muted-foreground px-4 py-3 bg-muted/30">
                            <CheckCircle2 class="size-4" />
                            SLA Solución
                        </div>
                        <div class="p-4 space-y-3">
                            <div v-if="ticket?.sla_resolution_time_minutes" class="space-y-2">
                                <Badge 
                                    v-if="ticket.sla_resolution_time_minutes.late_minutes" 
                                    class="bg-red-500 text-white w-full justify-center py-2"
                                >
                                    <Clock class="size-4" />
                                    {{ formatMinutes(ticket.sla_resolution_time_minutes.late_minutes) }} tarde
                                </Badge>
                                <Badge 
                                    v-else-if="ticket.sla_resolution_time_minutes.before_late_minutes" 
                                    class="bg-green-500 text-white w-full justify-center py-2"
                                >
                                    <CheckCircle2 class="size-4" />
                                    {{ formatMinutes(ticket.sla_resolution_time_minutes.before_late_minutes) }} antes
                                </Badge>
                                <Badge 
                                    v-else-if="ticket.sla_resolution_time_minutes.remaining_minutes" 
                                    class="bg-yellow-500 text-white w-full justify-center py-2"
                                >
                                    <Clock class="size-4" />
                                    {{ formatMinutes(ticket.sla_resolution_time_minutes.remaining_minutes) }} restantes
                                </Badge>
                            </div>
                            <div class="grid gap-2 text-xs">
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-muted-foreground">Debe resolver</span>
                                    <span class="font-medium">{{ formatDate(ticket?.sla_resolution_due_at) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-muted-foreground">Resuelto</span>
                                    <span class="font-medium" :class="{ 'text-muted-foreground/60 italic': !ticket?.resolved_at }">
                                        {{ formatDate(ticket?.resolved_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <div class="mt-4 rounded-lg border bg-card p-4">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase text-muted-foreground">
                    <Image class="size-4" />
                    Evidencias
                </div>
                <div v-if="imageUrls.length" class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                    <a v-for="(img, idx) in imageUrls" :key="`${img}-${idx}`" :href="img" target="_blank"
                        rel="noreferrer" class="group block overflow-hidden rounded-lg border bg-muted/30">
                        <img :src="img" alt="Evidencia"
                            class="h-28 w-full object-cover transition-transform duration-200 group-hover:scale-[1.02]" />
                    </a>
                </div>
                <div v-else class="mt-3 text-xs text-muted-foreground">No hay imagenes registradas.</div>
            </div>
                </ScrollArea>


        </DialogContent>
    </Dialog>

     <RequesterAssetsSheet v-model:open="openRequesterAssets" 
                                    :requester-id="ticket?.requester_id" />
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { priorityOp, requestTypeOp, statusOp, typeOp, type Ticket } from '@/interfaces/ticket.interface';
import { format } from 'date-fns';
import { Activity, AlertTriangle, Calendar, CheckCircle2, Clock, Gauge, Image, Tag, Ticket as TicketIcon, User, Zap, MonitorSmartphone } from 'lucide-vue-next';
import RequesterAssetsSheet from './RequesterAssetsSheet.vue';
import { Button } from '@/components/ui/button';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';

const open = defineModel<boolean>('open');

const openRequesterAssets = ref(false);

const { ticket } = defineProps<{
    ticket: Ticket | null,
}>();

const imageUrls = computed(() => {
    const urls = ticket?.images_urls ?? ticket?.images ?? [];
    return Array.isArray(urls) ? urls : [];
});

const impactLabels: Record<string, string> = {
    LOW: 'Bajo',
    MEDIUM: 'Medio',
    HIGH: 'Alto',
};

const urgencyLabels: Record<string, string> = {
    LOW: 'Baja',
    MEDIUM: 'Media',
    HIGH: 'Alta',
};

const formatDate = (value?: string | Date | null): string => {
    if (!value) return 'Sin registrar';
    return format(new Date(value), 'dd/MM/yyyy HH:mm');
};

const formatMinutes = (minutes: number): string => {
    if (!Number.isFinite(minutes) || minutes === 0) return '0 min';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hrs && mins) return `${hrs}h ${mins}m`;
    if (hrs) return `${hrs}h`;
    return `${mins}m`;
};

</script>