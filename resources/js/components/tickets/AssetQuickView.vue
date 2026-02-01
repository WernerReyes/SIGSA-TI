<template>
    <!-- 🧾 4️⃣ UI – Detalle del Activo desde el Ticket (Vista rápida) -->
    <Sheet v-model:open="isOpen">
        <SheetContent side="right" class="w-full sm:w-[540px] overflow-y-auto">
            <SheetHeader class="space-y-3 pb-4">
                <div class="flex items-start gap-4">
                    <div class="size-14 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <Monitor class="size-7 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <SheetTitle class="text-xl">{{ asset.name }}</SheetTitle>
                        <p class="text-sm text-muted-foreground mt-1">Vista rápida del activo</p>
                    </div>
                </div>
            </SheetHeader>

            <div class="space-y-6 py-4">
                <!-- Información básica -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Información Básica</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-lg border bg-card">
                            <p class="text-xs text-muted-foreground mb-1">Tipo</p>
                            <p class="text-sm font-semibold">{{ asset.type }}</p>
                        </div>
                        <div class="p-3 rounded-lg border bg-card">
                            <p class="text-xs text-muted-foreground mb-1">Marca</p>
                            <p class="text-sm font-semibold">{{ asset.brand }}</p>
                        </div>
                        <div class="p-3 rounded-lg border bg-card">
                            <p class="text-xs text-muted-foreground mb-1">Modelo</p>
                            <p class="text-sm font-semibold">{{ asset.model }}</p>
                        </div>
                        <div class="p-3 rounded-lg border bg-card">
                            <p class="text-xs text-muted-foreground mb-1">Estado</p>
                            <Badge :class="getStatusBadge(asset.status)" class="text-xs">
                                {{ asset.status }}
                            </Badge>
                        </div>
                    </div>

                    <div class="p-3 rounded-lg border bg-card">
                        <p class="text-xs text-muted-foreground mb-1">Número de Serie</p>
                        <p class="text-sm font-mono font-semibold">{{ asset.serial_number }}</p>
                    </div>
                </div>

                <!-- Asignación actual -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Asignación Actual</h4>
                    
                    <div v-if="asset.assigned_to" class="p-4 rounded-lg border bg-green-50 dark:bg-green-950/30 border-green-200 dark:border-green-800">
                        <div class="flex items-start gap-3">
                            <div class="size-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0">
                                <User class="size-5 text-green-600 dark:text-green-400" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold">{{ asset.assigned_to.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ asset.assigned_to.department }}</p>
                                <div class="flex items-center gap-1 mt-2 text-xs text-muted-foreground">
                                    <Calendar class="size-3" />
                                    <span>Asignado desde: {{ asset.assigned_date }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-4 rounded-lg border border-dashed bg-muted/30 text-center">
                        <p class="text-sm text-muted-foreground">Sin asignación actual</p>
                    </div>
                </div>

                <!-- Especificaciones técnicas -->
                <div v-if="asset.specs" class="space-y-3">
                    <h4 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Especificaciones</h4>
                    
                    <div class="space-y-2">
                        <div v-for="(value, key) in asset.specs" :key="key" class="flex items-center justify-between p-2 rounded-lg bg-muted/50">
                            <span class="text-xs text-muted-foreground">{{ formatSpecKey(key) }}</span>
                            <span class="text-xs font-semibold font-mono">{{ value }}</span>
                        </div>
                    </div>
                </div>

                <!-- Historial resumido -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Historial Resumido</h4>
                        <Button variant="link" size="sm" class="h-auto p-0 text-xs" @click="viewFullHistory">
                            Ver historial completo
                            <ArrowRight class="size-3 ml-1" />
                        </Button>
                    </div>

                    <div class="space-y-2">
                        <div 
                            v-for="(history, index) in asset.recent_history" 
                            :key="index"
                            class="flex items-start gap-3 p-3 rounded-lg border bg-card"
                        >
                            <div :class="getHistoryIconClass(history.action)">
                                <component :is="getHistoryIcon(history.action)" class="size-3" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold">{{ history.action }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ history.description }}</p>
                                <p class="text-xs text-muted-foreground mt-1">{{ history.date }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Garantía y fechas -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Información Adicional</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-lg border bg-card">
                            <div class="flex items-center gap-2 mb-1">
                                <ShoppingCart class="size-3 text-muted-foreground" />
                                <p class="text-xs text-muted-foreground">Fecha de Compra</p>
                            </div>
                            <p class="text-sm font-semibold">{{ asset.purchase_date }}</p>
                        </div>
                        <div class="p-3 rounded-lg border bg-card">
                            <div class="flex items-center gap-2 mb-1">
                                <ShieldCheck class="size-3 text-muted-foreground" />
                                <p class="text-xs text-muted-foreground">Garantía hasta</p>
                            </div>
                            <p class="text-sm font-semibold" :class="isWarrantyExpired ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                {{ asset.warranty_expiration }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer con acciones opcionales -->
            <div class="pt-4 border-t mt-6">
                <p class="text-xs text-muted-foreground text-center">
                    Vista de solo lectura • Última actualización: {{ asset.last_updated }}
                </p>
            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    Monitor, User, Calendar, ShoppingCart, ShieldCheck, ArrowRight,
    CheckCircle, RotateCw, Wrench, Package 
} from 'lucide-vue-next';

const isOpen = defineModel<boolean>('open');

const emit = defineEmits<{
    viewFullHistory: [];
}>();

// Mock data - Información del activo
const asset = {
    id: 1,
    name: 'Dell Latitude 5520',
    type: 'Laptop',
    brand: 'Dell',
    model: 'Latitude 5520',
    serial_number: 'DEL-2024-001234',
    status: 'ASSIGNED',
    assigned_to: {
        name: 'Juan Pérez García',
        department: 'Departamento IT'
    },
    assigned_date: '2024-01-25',
    specs: {
        processor: 'Intel Core i7-12700H',
        ram: '16 GB DDR5',
        storage: '512 GB SSD',
        screen: '15.6" FHD',
        os: 'Windows 11 Pro'
    },
    purchase_date: '2024-01-15',
    warranty_expiration: '2026-01-15',
    last_updated: '2024-01-25 15:30',
    recent_history: [
        {
            action: 'Asignado',
            description: 'Equipo asignado a Juan Pérez García',
            date: '2024-01-25 14:45'
        },
        {
            action: 'Mantenimiento',
            description: 'Actualización de BIOS y drivers',
            date: '2024-01-20 10:30'
        },
        {
            action: 'Inspección',
            description: 'Revisión técnica completa - OK',
            date: '2024-01-18 09:00'
        },
        {
            action: 'Recibido',
            description: 'Equipo recibido del proveedor',
            date: '2024-01-15 08:00'
        }
    ]
};

// Computed
const isWarrantyExpired = computed(() => {
    const expirationDate = new Date(asset.warranty_expiration);
    return expirationDate < new Date();
});

// Methods
const getStatusBadge = (status: string) => {
    const badges: Record<string, string> = {
        'ASSIGNED': 'bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800',
        'AVAILABLE': 'bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800',
        'IN_REPAIR': 'bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-300 dark:border-amber-800',
    };
    return badges[status] || '';
};

const formatSpecKey = (key: string) => {
    const labels: Record<string, string> = {
        'processor': 'Procesador',
        'ram': 'Memoria RAM',
        'storage': 'Almacenamiento',
        'screen': 'Pantalla',
        'os': 'Sistema Operativo'
    };
    return labels[key] || key;
};

const getHistoryIcon = (action: string) => {
    const icons: Record<string, any> = {
        'Asignado': CheckCircle,
        'Mantenimiento': Wrench,
        'Inspección': Package,
        'Recibido': RotateCw,
    };
    return icons[action] || Package;
};

const getHistoryIconClass = (action: string) => {
    const classes: Record<string, string> = {
        'Asignado': 'size-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-green-600 dark:text-green-400',
        'Mantenimiento': 'size-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400',
        'Inspección': 'size-6 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0 text-amber-600 dark:text-amber-400',
        'Recibido': 'size-6 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0 text-purple-600 dark:text-purple-400',
    };
    return classes[action] || classes['Recibido'];
};

const viewFullHistory = () => {
    emit('viewFullHistory');
};
</script>
