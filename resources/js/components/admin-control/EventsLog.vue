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
                <Button size="sm" variant="outline" @click="resetFilters">Limpiar filtros</Button>
                <Button size="sm" class="gap-2" @click="openDialog = true">
                    <Plus class="h-4 w-4" />
                    Registrar evento
                </Button>
            </div>
        </CardHeader>
        <CardContent>
            <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                <Input v-model="filters.query" placeholder="Buscar por titulo, sistema o autor" />
                <Select v-model="filters.type">
                    <SelectTrigger>
                        <SelectValue placeholder="Tipo" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Todos los tipos</SelectItem>
                        <SelectItem v-for="type in typeOptions" :key="type" :value="type">
                            {{ type }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="filters.status">
                    <SelectTrigger>
                        <SelectValue placeholder="Estado" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Todos los estados</SelectItem>
                        <SelectItem v-for="status in statusOptions" :key="status" :value="status">
                            {{ status }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="filters.impact">
                    <SelectTrigger>
                        <SelectValue placeholder="Impacto" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Todos los impactos</SelectItem>
                        <SelectItem v-for="impact in impactOptions" :key="impact" :value="impact">
                            {{ impact }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="space-y-4">
                <div v-if="!filteredEvents.length" class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                    No hay eventos que coincidan con los filtros.
                </div>
                <div v-for="event in filteredEvents" :key="event.id"
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
                            <p class="font-medium text-foreground">{{ formatDate(event.date) }}</p>
                            <p>{{ event.time }}</p>
                            <Button size="icon" variant="ghost" class="mt-2" @click="handleDelete(event.id)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
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

    <UpsertEventDialog
        v-model:open="openDialog"
        :type-options="typeOptions"
        :status-options="statusOptions"
        :impact-options="impactOptions"
        :window-options="windowOptions"
        @submit="handleCreate"
    />
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { router } from '@inertiajs/core';
import { AlertTriangle, HardHat, Plus, Repeat, Server, ShieldCheck, Trash2, Wrench } from 'lucide-vue-next';
import UpsertEventDialog from './UpsertEventDialog.vue';

type EventFormValues = {
    title: string;
    description: string;
    type: string;
    system: string;
    author: string;
    date: string;
    time: string;
    status: string;
    impact: string;
    window: string;
};

type EventItem = EventFormValues & {
    id: number;
    icon: typeof Server;
    color: string;
    accent: string;
    accentBar: string;
    statusStyle: string;
};

const openDialog = ref(false);
const useLocalOnly = true;

const typeOptions = ['Cambio', 'Mantenimiento', 'Seguridad', 'Auditoria', 'Incidente', 'Renovacion'];
const statusOptions = ['Programado', 'En seguimiento', 'Completado', 'Cancelado'];
const impactOptions = ['Sin impacto', 'Bajo impacto', 'Impacto moderado', 'Alto impacto'];
const windowOptions = ['Ventana programada', 'Ventana nocturna', 'Automatizado', 'Programado'];

const typeMeta: Record<string, Omit<EventItem, keyof EventFormValues | 'id' | 'statusStyle'>> = {
    Cambio: {
        icon: Server,
        color: 'text-primary',
        accent: 'pl-5 border-primary/30',
        accentBar: 'bg-primary',
    },
    Mantenimiento: {
        icon: Wrench,
        color: 'text-warning',
        accent: 'pl-5 border-warning/30',
        accentBar: 'bg-warning',
    },
    Seguridad: {
        icon: ShieldCheck,
        color: 'text-info',
        accent: 'pl-5 border-info/30',
        accentBar: 'bg-info',
    },
    Auditoria: {
        icon: HardHat,
        color: 'text-success',
        accent: 'pl-5 border-success/30',
        accentBar: 'bg-success',
    },
    Incidente: {
        icon: AlertTriangle,
        color: 'text-destructive',
        accent: 'pl-5 border-destructive/30',
        accentBar: 'bg-destructive',
    },
    Renovacion: {
        icon: Repeat,
        color: 'text-primary',
        accent: 'pl-5 border-primary/30',
        accentBar: 'bg-primary',
    },
};

const statusStyles: Record<string, string> = {
    Programado: 'bg-warning/10 text-warning',
    'En seguimiento': 'bg-info/10 text-info',
    Completado: 'bg-success/10 text-success',
    Cancelado: 'bg-destructive/10 text-destructive',
};

const filters = reactive({
    query: '',
    type: '',
    status: '',
    impact: '',
});

const seedEvents: EventFormValues[] = [
    {
        title: 'Actualizacion de firmware en cluster de servidores',
        description: 'Actualizacion coordinada para mejorar rendimiento y seguridad.',
        type: 'Cambio',
        system: 'Infraestructura Core',
        author: 'Equipo TI',
        date: '2026-02-04',
        time: '08:30',
        status: 'Completado',
        impact: 'Bajo impacto',
        window: 'Ventana programada',
    },
    {
        title: 'Mantenimiento preventivo UPS Data Center',
        description: 'Revision y pruebas de autonomia.',
        type: 'Mantenimiento',
        system: 'Data Center',
        author: 'Infraestructura',
        date: '2026-02-03',
        time: '21:00',
        status: 'Programado',
        impact: 'Impacto moderado',
        window: 'Ventana nocturna',
    },
    {
        title: 'Rotacion de certificados de seguridad',
        description: 'Renovacion de certificados TLS y llaves de acceso.',
        type: 'Seguridad',
        system: 'Servicios Web',
        author: 'Seguridad TI',
        date: '2026-02-01',
        time: '11:15',
        status: 'En seguimiento',
        impact: 'Sin impacto',
        window: 'Automatizado',
    },
    {
        title: 'Revision de respaldos mensuales',
        description: 'Verificacion de integridad y restauracion de prueba.',
        type: 'Auditoria',
        system: 'Backups',
        author: 'Operaciones TI',
        date: '2026-01-30',
        time: '17:45',
        status: 'Completado',
        impact: 'Sin impacto',
        window: 'Programado',
    },
];

const nextId = ref(seedEvents.length + 1);
const events = ref<EventItem[]>(seedEvents.map((event, index) => buildEventItem(event, index + 1)));

const filteredEvents = computed(() => {
    const query = filters.query.trim().toLowerCase();

    return events.value.filter((event) => {
        if (query) {
            const matchesQuery = [event.title, event.system, event.author, event.description]
                .some((value) => value.toLowerCase().includes(query));
            if (!matchesQuery) return false;
        }

        if (filters.type && event.type !== filters.type) return false;
        if (filters.status && event.status !== filters.status) return false;
        if (filters.impact && event.impact !== filters.impact) return false;

        return true;
    });
});

function buildEventItem(values: EventFormValues, id: number): EventItem {
    const meta = typeMeta?.[values.type] || typeMeta.Cambio;

    return {
        ...values,
        id,
        ...meta,
        statusStyle: statusStyles[values.status] || 'bg-muted text-muted-foreground',
    };
}

function resetFilters() {
    filters.query = '';
    filters.type = '';
    filters.status = '';
    filters.impact = '';
}

function handleCreate(values: EventFormValues) {
    if (useLocalOnly) {
        const item = buildEventItem(values, nextId.value);
        nextId.value += 1;
        events.value = [item, ...events.value];
        openDialog.value = false;
        return;
    }

    router.post('/admin-control/events', values, {
        preserveScroll: true,
        onSuccess: () => {
            openDialog.value = false;
        },
    });
}

function handleDelete(id: number) {
    if (useLocalOnly) {
        events.value = events.value.filter((event) => event.id !== id);
        return;
    }

    router.delete(`/admin-control/events/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            events.value = events.value.filter((event) => event.id !== id);
        },
    });
}

function formatDate(value: string) {
    const parts = value.split('-');
    if (parts.length !== 3) return value;
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}
</script>


<!-- CREATE TABLE infrastructure_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    event_type ENUM(
        'mantenimiento',
        'cambio',
        'incidente',
        'renovacion'
    ) NOT NULL,

    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (contract_id) REFERENCES contracts(id)
); -->