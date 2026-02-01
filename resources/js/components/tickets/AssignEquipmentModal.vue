<template>
    <!-- 🎨 1️⃣ UI – Asignación de Equipo desde Ticket -->
    <Dialog v-model:open="isOpen" @update:open="(val) => {
        if (!val) currTicket = null;
    }">
        <DialogContent class="sm:max-w-2xl">
            <!-- Encabezado -->
            <DialogHeader class="space-y-3">
                <div
                    class="flex items-start gap-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800">
                    <div
                        class="size-12 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                        <MonitorSmartphone class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg font-semibold">Asignar Equipo</DialogTitle>
                        <div class="flex items-center gap-2 mt-1">
                            <Badge variant="outline" class="font-mono text-xs">TK-{{ ticketNumber }}</Badge>
                            <span class="text-sm text-muted-foreground">•</span>
                            <span class="text-sm text-muted-foreground">Solicitante: {{ requesterName }}</span>
                        </div>
                    </div>
                </div>
            </DialogHeader>

            <div class="space-y-6 py-4">
                <!-- Select: Equipo disponible -->
                <div class="space-y-3">
                    <Label for="equipment" class="text-sm font-medium flex items-center gap-2">
                        <Monitor class="size-4 text-muted-foreground" />
                        Equipo disponible
                    </Label>
                    <Select v-model="selectedEquipment">
                        <SelectTrigger id="equipment">
                            <SelectValue placeholder="Seleccione un equipo..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="equipment in availableEquipments" :key="equipment.id"
                                :value="equipment.id.toString()">
                                <div class="flex items-center gap-3 py-1">
                                    <component :is="getEquipmentIcon(equipment.type)"
                                        class="size-4 shrink-0 text-muted-foreground" />
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold">{{ equipment.brand }} {{ equipment.model
                                                }}</span>
                                            <Badge :class="getStatusBadge(equipment.status)" class="text-xs">
                                                {{ equipment.status }}
                                            </Badge>
                                        </div>
                                        <div class="text-xs text-muted-foreground flex items-center gap-2 mt-0.5">
                                            <span>{{ equipment.type }}</span>
                                            <span>•</span>
                                            <span class="font-mono">S/N: {{ equipment.serial_number }}</span>
                                        </div>
                                    </div>
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p class="text-xs text-muted-foreground flex items-center gap-1">
                        <Info class="size-3" />
                        Solo se muestran equipos disponibles para asignar
                    </p>
                </div>

                <!-- Select: Asignar a -->
                <div class="space-y-3">
                    <Label for="employee" class="text-sm font-medium flex items-center gap-2">
                        <UserCheck class="size-4 text-muted-foreground" />
                        Asignar a
                    </Label>
                    <Select v-model="selectedEmployee">
                        <SelectTrigger id="employee">
                            <SelectValue placeholder="Seleccione un empleado..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="employee in employees" :key="employee.id"
                                :value="employee.id.toString()">
                                <div class="flex items-center gap-3 py-1">
                                    <div
                                        class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                                        <User class="size-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">{{ employee.name }}</div>
                                        <div class="text-xs text-muted-foreground">{{ employee.department }}</div>
                                    </div>
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <!-- Advertencia: Usuario ya tiene equipo -->
                    <div v-if="selectedEmployeeHasEquipment"
                        class="flex items-start gap-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                        <AlertTriangle class="size-4 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0" />
                        <div class="flex-1 text-xs">
                            <p class="font-semibold text-amber-900 dark:text-amber-300">Este empleado ya tiene un equipo
                                asignado</p>
                            <p class="text-amber-700 dark:text-amber-400 mt-1">{{ currentEquipmentInfo }}</p>
                            <Label class="flex items-center gap-2 mt-2 cursor-pointer">
                                <Checkbox v-model:checked="confirmReplacement" />
                                <span class="font-medium">Confirmo que deseo cambiar su equipo actual</span>
                            </Label>
                        </div>
                    </div>
                </div>

                <!-- Campo: Observaciones -->
                <div class="space-y-3">
                    <Label for="observations" class="text-sm font-medium flex items-center gap-2">
                        <FileText class="size-4 text-muted-foreground" />
                        Observaciones
                    </Label>
                    <Textarea id="observations" v-model="observations"
                        placeholder="Ingrese cualquier observación relevante sobre esta asignación..."
                        class="min-h-[100px]" />
                    <p class="text-xs text-muted-foreground">
                        Esta nota quedará registrada en el historial del activo
                    </p>
                </div>
            </div>

            <!-- Botones de acción -->
            <DialogFooter class="flex gap-2 pt-4 border-t">
                <Button variant="outline" @click="handleCancel">
                    <X class="size-4 mr-2" />
                    Cancelar
                </Button>
                <Button @click="handleConfirm" :disabled="!canConfirm" class="bg-blue-600 hover:bg-blue-700">
                    <CheckCircle class="size-4 mr-2" />
                    Confirmar asignación
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Laptop, Monitor, Smartphone, User, UserCheck, FileText,
    Info, AlertTriangle, CheckCircle, X
} from 'lucide-vue-next';
import { type Ticket } from '@/interfaces/ticket.interface';
import { MonitorSmartphone } from 'lucide-vue-next';

const isOpen = defineModel<boolean>('open');

const currTicket = defineModel<Ticket | null>('ticket');

// Props
defineProps<{
    ticketNumber?: number;
    requesterName?: string;
}>();

// State
const selectedEquipment = ref<string>('');
const selectedEmployee = ref<string>('');
const observations = ref<string>('');
const confirmReplacement = ref<boolean>(false);

// Mock data - Equipos disponibles
const availableEquipments = [
    {
        id: 1,
        type: 'Laptop',
        brand: 'Dell',
        model: 'Latitude 5520',
        serial_number: 'DEL-2024-001234',
        status: 'AVAILABLE'
    },
    {
        id: 2,
        type: 'PC Desktop',
        brand: 'HP',
        model: 'EliteDesk 800 G6',
        serial_number: 'HP-2024-005678',
        status: 'AVAILABLE'
    },
    {
        id: 3,
        type: 'Laptop',
        brand: 'Lenovo',
        model: 'ThinkPad X1 Carbon',
        serial_number: 'LEN-2024-009012',
        status: 'AVAILABLE'
    },
    {
        id: 4,
        type: 'Móvil',
        brand: 'Samsung',
        model: 'Galaxy A54',
        serial_number: 'SAM-2024-003456',
        status: 'AVAILABLE'
    }
];

// Mock data - Empleados
const employees = [
    { id: 1, name: 'Juan Pérez García', department: 'Departamento IT', hasEquipment: false },
    { id: 2, name: 'María González López', department: 'Recursos Humanos', hasEquipment: true, currentEquipment: 'Dell Latitude 5420 (SN: DEL-2023-001111)' },
    { id: 3, name: 'Carlos Martínez Ruiz', department: 'Contabilidad', hasEquipment: false },
    { id: 4, name: 'Ana Rodríguez Díaz', department: 'Ventas', hasEquipment: true, currentEquipment: 'HP EliteBook 840 (SN: HP-2023-002222)' },
];

// Computed
const selectedEmployeeHasEquipment = computed(() => {
    if (!selectedEmployee.value) return false;
    const employee = employees.find(e => e.id.toString() === selectedEmployee.value);
    return employee?.hasEquipment ?? false;
});

const currentEquipmentInfo = computed(() => {
    if (!selectedEmployee.value) return '';
    const employee = employees.find(e => e.id.toString() === selectedEmployee.value);
    return employee?.currentEquipment ?? '';
});

const canConfirm = computed(() => {
    const hasSelections = selectedEquipment.value && selectedEmployee.value;
    if (!hasSelections) return false;

    // Si el empleado tiene equipo, debe confirmar el reemplazo
    if (selectedEmployeeHasEquipment.value) {
        return confirmReplacement.value;
    }

    return true;
});

// Methods
const getEquipmentIcon = (type: string) => {
    const icons: Record<string, any> = {
        'Laptop': Laptop,
        'PC Desktop': Monitor,
        'Móvil': Smartphone,
    };
    return icons[type] || Monitor;
};

const getStatusBadge = (status: string) => {
    const badges: Record<string, string> = {
        'AVAILABLE': 'bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800',
        'ASSIGNED': 'bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800',
    };
    return badges[status] || '';
};

const handleConfirm = () => {
    // Aquí iría la lógica real de asignación
    console.log('Asignando equipo:', {
        equipment: selectedEquipment.value,
        employee: selectedEmployee.value,
        observations: observations.value,
        isReplacement: selectedEmployeeHasEquipment.value
    });
    isOpen.value = false;
};

const handleCancel = () => {
    isOpen.value = false;
};
</script>
