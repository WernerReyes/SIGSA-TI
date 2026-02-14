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

// const tickets = [
//     {
//         id: 'TK-1234',
//         title: 'Error critico en servidor de produccion',
//         priority: 'Critico',
//         status: 'En progreso',
//         assignee: 'Maria Lopez',
//         created: 'Hace 15 min',
//     },
//     {
//         id: 'TK-1233',
//         title: 'Solicitud de acceso a VPN',
//         priority: 'Medio',
//         status: 'Abierto',
//         assignee: 'Sin asignar',
//         created: 'Hace 1 hora',
//     },
//     {
//         id: 'TK-1232',
//         title: 'Falla en impresora del piso 3',
//         priority: 'Bajo',
//         status: 'Pendiente',
//         assignee: 'Juan Perez',
//         created: 'Hace 2 horas',
//     },
//     {
//         id: 'TK-1231',
//         title: 'Actualizacion de licencia Office',
//         priority: 'Alto',
//         status: 'Abierto',
//         assignee: 'Carlos Ruiz',
//         created: 'Hace 3 horas',
//     },
//     {
//         id: 'TK-1230',
//         title: 'Configuracion de nuevo equipo',
//         priority: 'Medio',
//         status: 'Resuelto',
//         assignee: 'Ana Martinez',
//         created: 'Hace 4 horas',
//     },
// ];

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
                    <TableRow v-for="ticket in tickets" :key="ticket.id">
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
