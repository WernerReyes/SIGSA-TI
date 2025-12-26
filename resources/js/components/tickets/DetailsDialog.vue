<template>

    <Dialog v-model:open="open">
        <DialogContent class=" max-h-screen md:min-w-11/12  overflow-y-auto">
            <DialogHeader>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-mono text-sm text-primary">TK-{{ ticket?.id }}</p>
                        <h2 id="radix-:r1i:" class="font-semibold tracking-tight text-xl mt-1">{{ ticket?.title }}</h2>
                    </div>
                    <div class="flex gap-2">

                        <Badge :class="priorityOp(ticket?.priority)?.bg">
                            {{ priorityOp(ticket?.priority)?.label }}
                        </Badge>
                        <Badge :class="statusOp(ticket?.status)?.bg">
                            {{ statusOp(ticket?.status)?.label }}
                        </Badge>
                    </div>
                </div>
            </DialogHeader>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 border-t mt-4">
                <div class="lg:col-span-2 border-r">
                    <div dir="ltr" class="relative overflow-hidden h-125"
                        style="position: relative; --radix-scroll-area-corner-width: 0px; --radix-scroll-area-corner-height: 0px;">
                        <div data-radix-scroll-area-viewport="" class="h-full w-full rounded-[inherit]"
                            style="overflow: hidden scroll;">
                            <div style="min-width: 100%; display: table;">
                                <div class="p-6 space-y-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-muted-foreground mb-2">Descripción</h4>
                                        <p class="text-sm">{{ ticket?.description }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-muted-foreground mb-4">Comentarios</h4>
                                        <div class="space-y-4">
                                            <div class="bg-muted/30 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2"><span
                                                        class="font-medium text-sm">María López</span><span
                                                        class="text-xs text-muted-foreground">2024-01-15 11:30</span>
                                                </div>
                                                <p class="text-sm">Estoy revisando el problema. Parece ser un issue de
                                                    configuración.</p>
                                            </div>
                                            <div class="bg-muted/30 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2"><span
                                                        class="font-medium text-sm">Pedro Sánchez</span><span
                                                        class="text-xs text-muted-foreground">2024-01-15 12:00</span>
                                                </div>
                                                <p class="text-sm">Gracias por la actualización. ¿Necesitas acceso
                                                    adicional?</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex gap-2"><textarea
                                                class="flex min-h-20 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 flex-1"
                                                placeholder="Escribir un comentario..." rows="2"
                                                spellcheck="false"></textarea><button
                                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 w-10 h-auto"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-send w-4 h-4">
                                                    <path
                                                        d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z">
                                                    </path>
                                                    <path d="m21.854 2.147-10.94 10.939"></path>
                                                </svg></button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6 grid sm:grid-cols-3 lg:grid-cols-1">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">


                            <User class="size-4 text-muted-foreground" />
                            <div>

                                <p class="text-xs text-muted-foreground">Solicitante</p>
                                <p class="text-sm font-medium">{{ ticket?.requester.full_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">

                            <User class="size-4 text-muted-foreground" />
                            <div>
                                <p class="text-xs text-muted-foreground">Asignado a</p>
                                <p v-if="ticket?.technician" class="text-sm font-medium">{{
                                    ticket?.technician?.full_name }}</p>
                                <p v-else class="text-sm font-medium">No asignado</p>
                            </div>

                        </div>
                        <div class="flex items-center gap-3">


                            <Clock class="size-4 text-muted-foreground" />
                            <div>
                                <p class="text-xs text-muted-foreground">Creado</p>
                                <p class="text-sm font-medium">{{ format(ticket?.opened_at ?? '', 'yyyy-MM-dd HH:mm')
                                    }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">

                            <Monitor class="size-4 text-muted-foreground" />
                            <div v-if="ticket?.request_type">

                                <p class="text-xs text-muted-foreground">Categoría</p>
                                <!-- <p class="text-sm font-medium">Infraestructura</p> -->
                                <Badge :class="requestTypeOp(ticket?.request_type)?.bg">
                                    {{ requestTypeOp(ticket?.request_type)?.label }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-muted-foreground mb-3">Historial</h4>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button :disabled="ticket?.histories?.length === 0" variant="outline" size="sm"
                                            @click="openFullHistory = true">
                                            <History class="size-4" />
                                        </Button>

                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Ver todo el historial</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                        <div class="space-y-3">
                            <div v-if="ticket?.histories?.length === 0" class="text-xs text-muted-foreground">
                                No hay historial disponible.
                            </div>
                            <div v-else v-for="history in slicedHistories" :key="history.id" class="flex gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium">{{ history.action }}</p>
                                    <p class="text-muted-foreground">{{ history.performer?.full_name }} • {{
                                        format(new Date(history.performed_at), 'yyyy-MM-dd HH:mm') }}</p>
                                </div>
                            </div>


                            <!-- <div class="flex gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium">Ticket creado</p>
                                    <p class="text-muted-foreground">Sistema • 2024-01-15 10:30</p>
                                </div>
                            </div>
                            <div class="flex gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium">Asignado a María López</p>
                                    <p class="text-muted-foreground">Admin • 2024-01-15 10:35</p>
                                </div>
                            </div>
                            <div class="flex gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium">Estado cambiado a En progreso</p>
                                    <p class="text-muted-foreground">María López • 2024-01-15 11:00</p>
                                </div>
                            </div>
                            <div class="flex gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="font-medium">Comentario agregado</p>
                                    <p class="text-muted-foreground">María López • 2024-01-15 11:30</p>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <!-- <Button :disabled="isSubmitting
                        || Object.keys(errors).length > 0
                        " type="submit" form="dialogForm">
                        <Spinner v-if="isSubmitting" />
                        Crear Ticket
                    </Button> -->
            </DialogFooter>
        </DialogContent>
    </Dialog>


    <FullTicketHistoryDialog v-model:open="openFullHistory" :histories="ticket?.histories || []" />

</template>

<script setup lang="ts">
import { computed, ref } from 'vue';



import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { priorityOp, requestTypeOp, statusOp, type Ticket } from '@/interfaces/ticket.interface';
import { type TicketHistory } from '@/interfaces/ticketHistory.interface';
import { format } from 'date-fns';
import { Clock, History, Monitor, User } from 'lucide-vue-next';
import FullTicketHistoryDialog from './FullTicketHistoryDialog.vue';

const open = defineModel<boolean>('open');
const openFullHistory = ref(false);

const { ticket } = defineProps<{
    ticket: Ticket | null,

}>();

const slicedHistories = computed<TicketHistory[]>(() => {
    if (!ticket?.histories) return [];
    return ticket.histories.slice(0, 5);
});



</script>