<template>
    <!-- 🕓 3️⃣ UI – Historial del Ticket (Timeline) -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold flex items-center gap-2">
            <History class="size-5 text-purple-600 dark:text-purple-400" />
            Historial del Ticket
        </h3>

        <div class="relative space-y-4">
            <!-- Timeline vertical -->
            <div 
                v-for="(event, index) in timelineEvents" 
                :key="index"
                class="relative flex gap-4 pb-6 last:pb-0"
            >
                <!-- Línea vertical -->
                <div v-if="index < timelineEvents.length - 1" class="absolute left-5 top-12 w-0.5 h-full bg-border"></div>

                <!-- Icono del evento -->
                <div :class="getEventIconClass(event.type)">
                    <component :is="getEventIcon(event.type)" :class="getEventIconColor(event.type)" />
                </div>

                <!-- Contenido del evento -->
                <div class="flex-1 min-w-0">
                    <div class="p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                        <!-- Header del evento -->
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <Badge :class="getEventBadge(event.type)" class="text-xs font-semibold">
                                        {{ event.type }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">{{ event.timestamp }}</span>
                                </div>
                                <h4 class="text-sm font-semibold">{{ event.title }}</h4>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <p class="text-sm text-muted-foreground mb-2">{{ event.description }}</p>

                        <!-- Metadata -->
                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                            <div class="flex items-center gap-1">
                                <User class="size-3" />
                                <span>{{ event.user }}</span>
                            </div>
                            <div v-if="event.role" class="flex items-center gap-1">
                                <Tag class="size-3" />
                                <span>{{ event.role }}</span>
                            </div>
                        </div>

                        <!-- Detalles adicionales para eventos de activos -->
                        <div v-if="event.asset_details" class="mt-3 pt-3 border-t">
                            <div class="flex items-center gap-3 text-xs">
                                <div class="flex items-center gap-1">
                                    <Laptop class="size-3 text-muted-foreground" />
                                    <span class="font-medium">{{ event.asset_details.name }}</span>
                                </div>
                                <span class="text-muted-foreground">•</span>
                                <span class="font-mono text-muted-foreground">{{ event.asset_details.serial }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { 
    History, User, Tag, Laptop,
    FileText, AlertCircle, CheckCircle, UserPlus, 
    Settings, Package, RotateCw 
} from 'lucide-vue-next';

// Mock data - Eventos del timeline
const timelineEvents = [
    {
        type: 'ESTADO',
        title: 'Ticket resuelto',
        description: 'El ticket ha sido marcado como resuelto',
        timestamp: '2024-01-25 15:30',
        user: 'Carlos Martínez',
        role: 'Técnico TI'
    },
    {
        type: 'ACTIVO',
        title: 'Equipo asignado',
        description: 'Se asignó el equipo Dell Latitude 5520 al usuario',
        timestamp: '2024-01-25 14:45',
        user: 'Carlos Martínez',
        role: 'Técnico TI',
        asset_details: {
            name: 'Dell Latitude 5520',
            serial: 'DEL-2024-001234'
        }
    },
    {
        type: 'ACTIVO',
        title: 'Equipo devuelto',
        description: 'Se devolvió el equipo anterior HP EliteBook 840 por falla en pantalla',
        timestamp: '2024-01-25 14:30',
        user: 'Carlos Martínez',
        role: 'Técnico TI',
        asset_details: {
            name: 'HP EliteBook 840',
            serial: 'HP-2023-005678'
        }
    },
    {
        type: 'COMENTARIO',
        title: 'Comentario agregado',
        description: 'Se verificó que la pantalla del equipo actual presenta daños físicos. Se procederá con el cambio de equipo.',
        timestamp: '2024-01-25 11:20',
        user: 'Carlos Martínez',
        role: 'Técnico TI'
    },
    {
        type: 'ESTADO',
        title: 'Ticket en progreso',
        description: 'El estado del ticket cambió de ABIERTO a EN PROGRESO',
        timestamp: '2024-01-25 10:15',
        user: 'Carlos Martínez',
        role: 'Técnico TI'
    },
    {
        type: 'ASIGNACIÓN',
        title: 'Técnico asignado',
        description: 'El ticket fue asignado a Carlos Martínez',
        timestamp: '2024-01-25 09:30',
        user: 'Sistema',
        role: 'Automático'
    },
    {
        type: 'CREACIÓN',
        title: 'Ticket creado',
        description: 'Ticket abierto: "Pantalla de laptop presenta falla"',
        timestamp: '2024-01-25 09:00',
        user: 'Juan Pérez García',
        role: 'Solicitante'
    }
];

// Methods
const getEventIcon = (type: string) => {
    const icons: Record<string, any> = {
        'CREACIÓN': FileText,
        'ESTADO': AlertCircle,
        'ASIGNACIÓN': UserPlus,
        'COMENTARIO': Settings,
        'ACTIVO': Package,
        'RESUELTO': CheckCircle,
    };
    return icons[type] || FileText;
};

const getEventIconClass = (type: string) => {
    const classes: Record<string, string> = {
        'CREACIÓN': 'size-10 rounded-full bg-gray-100 dark:bg-gray-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
        'ESTADO': 'size-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
        'ASIGNACIÓN': 'size-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
        'COMENTARIO': 'size-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
        'ACTIVO': 'size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
        'RESUELTO': 'size-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0 ring-4 ring-background',
    };
    return classes[type] || classes['CREACIÓN'];
};

const getEventIconColor = (type: string) => {
    const colors: Record<string, string> = {
        'CREACIÓN': 'size-5 text-gray-600 dark:text-gray-400',
        'ESTADO': 'size-5 text-blue-600 dark:text-blue-400',
        'ASIGNACIÓN': 'size-5 text-purple-600 dark:text-purple-400',
        'COMENTARIO': 'size-5 text-amber-600 dark:text-amber-400',
        'ACTIVO': 'size-5 text-green-600 dark:text-green-400',
        'RESUELTO': 'size-5 text-emerald-600 dark:text-emerald-400',
    };
    return colors[type] || colors['CREACIÓN'];
};

const getEventBadge = (type: string) => {
    const badges: Record<string, string> = {
        'CREACIÓN': 'bg-gray-500/20 text-gray-700 dark:text-gray-400 border-gray-300 dark:border-gray-800',
        'ESTADO': 'bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800',
        'ASIGNACIÓN': 'bg-purple-500/20 text-purple-700 dark:text-purple-400 border-purple-300 dark:border-purple-800',
        'COMENTARIO': 'bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-300 dark:border-amber-800',
        'ACTIVO': 'bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800',
        'RESUELTO': 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border-emerald-300 dark:border-emerald-800',
    };
    return badges[type] || badges['CREACIÓN'];
};
</script>
