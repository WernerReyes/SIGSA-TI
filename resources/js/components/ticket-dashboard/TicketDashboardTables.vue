<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { TicketDashboardData } from '@/interfaces/ticketDashboard.interface';
import { getTicketOp } from '@/interfaces/ticket.interface';
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';

defineProps<{
    technicians: TicketDashboardData['technicians'];
    tickets: TicketDashboardData['recent_tickets'];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        <Card class="border-border/80 xl:col-span-2">
            <CardHeader>
                <CardTitle>Desempeño por responsable</CardTitle>
                <CardDescription
                    >Máximo de ocho responsables en el periodo.</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div v-if="technicians.length" class="space-y-5">
                    <div
                        v-for="technician in technicians"
                        :key="technician.staff_id"
                        class="space-y-2"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium">
                                    {{ technician.name }}
                                </p>
                                <p class="text-muted-foreground text-xs">
                                    {{ technician.resolved }} resueltos ·
                                    {{ technician.active }} activos ·
                                    {{ technician.sla_breached }} fuera de SLA
                                </p>
                            </div>
                            <span class="text-sm font-semibold tabular-nums">
                                {{ technician.resolution_rate }}%
                            </span>
                        </div>
                        <Progress :model-value="technician.resolution_rate" />
                    </div>
                </div>
                <p
                    v-else
                    class="text-muted-foreground py-12 text-center text-sm"
                >
                    No hay responsables con tickets en este periodo.
                </p>
            </CardContent>
        </Card>

        <Card class="border-border/80 xl:col-span-3">
            <CardHeader>
                <CardTitle>Tickets recientes</CardTitle>
                <CardDescription
                    >Últimos tickets creados dentro de los
                    filtros.</CardDescription
                >
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Ticket</TableHead>
                            <TableHead>Prioridad</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Categoría</TableHead>
                            <TableHead>Responsable</TableHead>
                            <TableHead>Creado</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="ticket in tickets" :key="ticket.id">
                            <TableCell>
                                <p class="text-primary font-mono text-xs">
                                    TK-{{
                                        ticket.id.toString().padStart(4, '0')
                                    }}
                                </p>
                                <p class="max-w-60 truncate font-medium">
                                    {{ ticket.title }}
                                </p>
                            </TableCell>
                            <TableCell>
                                <Badge variant="secondary" class="border">
                                    {{
                                        getTicketOp('priority', ticket.priority)
                                            .label
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    {{
                                        getTicketOp('status', ticket.status)
                                            .label
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    v-if="ticket.category"
                                    variant="secondary"
                                    class="border"
                                >
                                    {{
                                        getTicketOp('category', ticket.category)
                                            .label
                                    }}
                                </Badge>
                                <span v-else class="text-muted-foreground">
                                    Sin categoría
                                </span>
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{
                                    ticket.responsible?.full_name ??
                                    'Sin asignar'
                                }}
                            </TableCell>
                            <TableCell
                                class="text-muted-foreground whitespace-nowrap"
                            >
                                {{
                                    formatDistanceToNow(
                                        new Date(ticket.created_at),
                                        { addSuffix: true, locale: es },
                                    )
                                }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="!tickets.length">
                            <TableCell
                                colspan="6"
                                class="text-muted-foreground py-12 text-center"
                            >
                                No hay tickets para los filtros seleccionados.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
