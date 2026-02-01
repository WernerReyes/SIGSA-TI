<template>
    <!-- 📋 2️⃣ UI – Detalle del Ticket con Equipos Asociados -->
    <div class="space-y-4">
        <!-- Header de la sección -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <Package class="size-5 text-blue-600 dark:text-blue-400" />
                Equipos Asociados
            </h3>
            <Button 
                v-if="canAssignEquipment" 
                @click="openAssignModal"
                size="sm"
                class="bg-blue-600 hover:bg-blue-700"
            >
                <Plus class="size-4 mr-2" />
                {{ hasEquipmentAssigned ? 'Cambiar equipo' : 'Asignar equipo' }}
            </Button>
        </div>

        <!-- Lista de equipos asociados -->
        <div v-if="associatedEquipments.length > 0" class="space-y-3">
            <div 
                v-for="(item, index) in associatedEquipments" 
                :key="index"
                class="flex items-start gap-4 p-4 rounded-lg border bg-card hover:bg-muted/50 transition"
            >
                <!-- Icono de acción -->
                <div :class="getActionIconClass(item.action)">
                    <component :is="getActionIcon(item.action)" class="size-5" />
                </div>

                <!-- Información del equipo -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <Badge :class="getActionBadge(item.action)" class="text-xs font-semibold">
                            {{ getActionLabel(item.action) }}
                        </Badge>
                        <span class="text-xs text-muted-foreground">{{ item.timestamp }}</span>
                    </div>
                    
                    <p class="text-sm font-semibold">{{ item.equipment_name }}</p>
                    <p class="text-xs text-muted-foreground font-mono mt-0.5">S/N: {{ item.serial_number }}</p>
                    
                    <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                        <div class="flex items-center gap-1">
                            <User class="size-3" />
                            <span>{{ item.technician }}</span>
                        </div>
                        <div v-if="item.notes" class="flex items-center gap-1">
                            <FileText class="size-3" />
                            <span>{{ item.notes }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botón de detalle rápido -->
                <Button 
                    variant="ghost" 
                    size="sm"
                    @click="openQuickView(item)"
                    class="shrink-0"
                >
                    <Eye class="size-4" />
                </Button>
            </div>
        </div>

        <!-- Estado vacío -->
        <div v-else class="flex flex-col items-center justify-center p-8 rounded-lg border border-dashed bg-muted/30">
            <div class="size-16 rounded-full bg-muted flex items-center justify-center mb-3">
                <Package class="size-8 text-muted-foreground" />
            </div>
            <p class="text-sm font-medium text-muted-foreground">Este ticket no tiene equipos asociados</p>
            <p class="text-xs text-muted-foreground mt-1">
                {{ canAssignEquipment ? 'Haz clic en "Asignar equipo" para vincular un activo' : 'Solo tickets de tipo EQUIPMENT pueden tener equipos asociados' }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    Package, Plus, User, FileText, Eye,
    CheckCircle, RotateCw, ArrowLeftRight 
} from 'lucide-vue-next';

const props = defineProps<{
    ticketType?: string;
    ticketStatus?: string;
}>();

const emit = defineEmits<{
    assignEquipment: [];
    viewEquipment: [equipmentId: number];
}>();

// Mock data - Equipos asociados al ticket
const associatedEquipments = [
    {
        id: 1,
        action: 'ASSIGN',
        equipment_name: 'Dell Latitude 5520',
        serial_number: 'DEL-2024-001234',
        technician: 'Carlos Martínez',
        timestamp: '2024-01-25 10:30',
        notes: 'Equipo nuevo para reemplazo'
    },
    {
        id: 2,
        action: 'RETURN',
        equipment_name: 'HP EliteBook 840',
        serial_number: 'HP-2023-005678',
        technician: 'Ana Rodríguez',
        timestamp: '2024-01-25 10:15',
        notes: 'Equipo anterior devuelto - pantalla defectuosa'
    },
    {
        id: 3,
        action: 'CHANGE',
        equipment_name: 'Lenovo ThinkPad X1',
        serial_number: 'LEN-2023-009012',
        technician: 'Juan Pérez',
        timestamp: '2024-01-20 14:45',
        notes: 'Cambio por actualización de equipo'
    }
];

// Computed
const canAssignEquipment = computed(() => {
    return props.ticketType === 'EQUIPMENT' && props.ticketStatus !== 'CLOSED';
});

const hasEquipmentAssigned = computed(() => {
    return associatedEquipments.some(e => e.action === 'ASSIGN');
});

// Methods
const getActionIcon = (action: string) => {
    const icons: Record<string, any> = {
        'ASSIGN': CheckCircle,
        'RETURN': RotateCw,
        'CHANGE': ArrowLeftRight,
    };
    return icons[action] || Package;
};

const getActionIconClass = (action: string) => {
    const classes: Record<string, string> = {
        'ASSIGN': 'size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-green-600 dark:text-green-400',
        'RETURN': 'size-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0 text-amber-600 dark:text-amber-400',
        'CHANGE': 'size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400',
    };
    return classes[action] || '';
};

const getActionBadge = (action: string) => {
    const badges: Record<string, string> = {
        'ASSIGN': 'bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800',
        'RETURN': 'bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-300 dark:border-amber-800',
        'CHANGE': 'bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800',
    };
    return badges[action] || '';
};

const getActionLabel = (action: string) => {
    const labels: Record<string, string> = {
        'ASSIGN': 'ASIGNACIÓN',
        'RETURN': 'DEVOLUCIÓN',
        'CHANGE': 'CAMBIO',
    };
    return labels[action] || action;
};

const openAssignModal = () => {
    emit('assignEquipment');
};

const openQuickView = (equipment: any) => {
    emit('viewEquipment', equipment.id);
};
</script>
