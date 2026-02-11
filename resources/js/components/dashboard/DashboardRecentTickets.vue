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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

const tickets = [
    {
        id: 'TK-1234',
        title: 'Error critico en servidor de produccion',
        priority: 'Critico',
        status: 'En progreso',
        assignee: 'Maria Lopez',
        created: 'Hace 15 min',
    },
    {
        id: 'TK-1233',
        title: 'Solicitud de acceso a VPN',
        priority: 'Medio',
        status: 'Abierto',
        assignee: 'Sin asignar',
        created: 'Hace 1 hora',
    },
    {
        id: 'TK-1232',
        title: 'Falla en impresora del piso 3',
        priority: 'Bajo',
        status: 'Pendiente',
        assignee: 'Juan Perez',
        created: 'Hace 2 horas',
    },
    {
        id: 'TK-1231',
        title: 'Actualizacion de licencia Office',
        priority: 'Alto',
        status: 'Abierto',
        assignee: 'Carlos Ruiz',
        created: 'Hace 3 horas',
    },
    {
        id: 'TK-1230',
        title: 'Configuracion de nuevo equipo',
        priority: 'Medio',
        status: 'Resuelto',
        assignee: 'Ana Martinez',
        created: 'Hace 4 horas',
    },
];

const priorityClasses: Record<string, string> = {
    Critico: 'bg-destructive/10 text-destructive border-destructive/20',
    Alto: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
    Medio: 'bg-yellow-500/10 text-yellow-600 border-yellow-500/20',
    Bajo: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
};

const statusClasses: Record<string, string> = {
    'En progreso': 'bg-primary/10 text-primary border-primary/20',
    Abierto: 'bg-sky-500/10 text-sky-600 border-sky-500/20',
    Pendiente: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
    Resuelto: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
};
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
                            {{ ticket.id }}
                        </TableCell>
                        <TableCell class="max-w-[240px] truncate font-medium">
                            {{ ticket.title }}
                        </TableCell>
                        <TableCell>
                            <Badge class="border" :class="priorityClasses[ticket.priority]" variant="secondary">
                                {{ ticket.priority }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge class="border" :class="statusClasses[ticket.status]" variant="secondary">
                                {{ ticket.status }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ ticket.assignee }}
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            {{ ticket.created }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
