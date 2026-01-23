<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-4xl max-h-screen overflow-y-auto">
            <DialogHeader class="space-y-3 pb-4">
                <div class="flex items-start gap-4 p-4 rounded-xl bg-linear-to-br from-muted/40 via-background to-background border">
                    <div class="size-16 rounded-xl bg-linear-to-br from-cyan-100/40 to-blue-100/40 dark:from-cyan-900/30 dark:to-blue-900/30 flex items-center justify-center ring-2 ring-blue-200/50 dark:ring-blue-800/50 shadow-sm">
                        <TicketIcon class="size-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-mono text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded-md">TK-{{ ticket?.id }}</p>
                            <Badge  class="text-xs" :class="typeOp(ticket?.type)?.bg">
                                <component :is="typeOp(ticket?.type)?.icon" />
                                {{ typeOp(ticket?.type)?.label }}
                            </Badge>
                        </div>
                        <h2 class="font-bold tracking-tight text-2xl">{{ ticket?.title }}</h2>
                        <p class="text-sm text-muted-foreground mt-1">{{ ticket?.description }}</p>
                    </div>
                </div>
            </DialogHeader>

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Columna Izquierda -->
                <div class="space-y-3">
                    <!-- Solicitante -->
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <User class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Solicitante</p>
                            <p class="text-sm font-semibold mt-1">{{ ticket?.requester?.full_name }}</p>
                            <p v-if="ticket?.requester?.department" class="text-xs text-muted-foreground mt-0.5">{{ ticket?.requester?.department?.name }}</p>
                        </div>
                    </div>

                    <!-- Asignado a -->
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <Zap class="size-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Asignado a</p>
                            <p v-if="ticket?.responsible" class="text-sm font-semibold mt-1">{{ ticket?.responsible?.full_name }}</p>
                            <Badge v-else variant="secondary" class="mt-1">Sin asignar</Badge>
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div v-if="ticket?.request_type" class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                            <Tag class="size-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Categoría</p>
                            <Badge :class="requestTypeOp(ticket?.request_type)?.bg" class="mt-1">
                                {{ requestTypeOp(ticket?.request_type)?.label }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div class="space-y-3">
                    <!-- Prioridad -->
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg flex items-center justify-center shrink-0"
                            :class="priorityOp(ticket?.priority)?.bg?.includes('red') ? 'bg-red-100 dark:bg-red-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30'">
                            <AlertTriangle :class="priorityOp(ticket?.priority)?.bg?.includes('red') ? 'size-5 text-red-600 dark:text-red-400' : 'size-5 text-yellow-600 dark:text-yellow-400'" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Prioridad</p>
                            <Badge :class="priorityOp(ticket?.priority)?.bg" class="mt-1">
                                {{ priorityOp(ticket?.priority)?.label }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <CheckCircle2 class="size-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Estado</p>
                            <Badge :class="statusOp(ticket?.status)?.bg" class="mt-1">
                                {{ statusOp(ticket?.status)?.label }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Fecha de Creación -->
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <div class="size-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <Calendar class="size-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Creado</p>
                            <p class="text-sm font-semibold mt-1">{{ format(new Date(ticket?.created_at ?? ''), 'dd/MM/yyyy HH:mm') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { priorityOp, requestTypeOp, statusOp, typeOp, type Ticket } from '@/interfaces/ticket.interface';
import { format } from 'date-fns';
import { AlertTriangle, Calendar, CheckCircle2, Tag, Ticket as TicketIcon, User, Zap } from 'lucide-vue-next';

const open = defineModel<boolean>('open');

const { ticket } = defineProps<{
    ticket: Ticket | null,
}>();

</script>