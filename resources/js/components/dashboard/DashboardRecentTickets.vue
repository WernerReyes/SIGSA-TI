<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { type Ticket, getTicketOp } from '@/interfaces/ticket.interface';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';


const { tickets  } = defineProps<{
    tickets: Ticket[]
}>();


</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <div>
                <CardTitle>Tickets recientes</CardTitle>
                <CardDescription>Ultimas solicitudes reportadas por el equipo.</CardDescription>
            </div>
            <CardAction>
                <Button as="a" href="/tickets" variant="ghost" size="sm">
                    Ver todos
                </Button>
            </CardAction>
        </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Titulo</TableHead>
                        <TableHead>Prioridad</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead>Asignado</TableHead>
                        <TableHead>Creado</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if ="!tickets.length">
                        <TableCell colspan="6" class="text-center text-sm text-muted-foreground">
                            No hay tickets recientes.
                        </TableCell>
                    </TableRow>
                    <TableRow v-else v-for="ticket in tickets" :key="ticket.id">
                        <TableCell class="font-mono text-primary">
                           TK-{{ ticket.id.toString().padStart(3, '0') }}
                        </TableCell>
                        <TableCell class="max-w-60 truncate font-medium">
                            {{ ticket.title }}
                        </TableCell>
                        <TableCell>
                            <Badge class="border" :class="getTicketOp('priority', ticket.priority).bg">
                                <component :is="getTicketOp('priority', ticket.priority)?.icon" />
                                {{ getTicketOp('priority', ticket.priority).label }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge class="border" :class="getTicketOp('status', ticket.status).bg" variant="secondary">
                                <component :is="getTicketOp('status', ticket.status)?.icon" />
                                {{ getTicketOp('status', ticket.status).label }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ ticket.responsible ? ticket.responsible.full_name : 'Sin asignar' }}
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ formatDistanceToNow(ticket.created_at, { addSuffix: true, locale: es }) }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
