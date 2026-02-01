<template>
    <div class="min-h-screen bg-background p-6">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Header -->
            <div class="space-y-2">
                <h1 class="text-4xl font-bold">Sistema de Asignación de Equipos desde Tickets</h1>
                <p class="text-lg text-muted-foreground">
                    Demo completo de las interfaces de asignación y gestión de equipos vinculados a tickets
                </p>
            </div>

            <!-- Tabs de navegación -->
            <div class="flex gap-2 border-b overflow-x-auto">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-4 py-2 font-medium transition whitespace-nowrap',
                        activeTab === tab.id 
                            ? 'border-b-2 border-blue-600 text-blue-600' 
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Contenido de cada tab -->
            
            <!-- Tab 1: Modal de Asignación -->
            <div v-show="activeTab === 'assign'" class="space-y-4">
                <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800">
                    <h3 class="font-semibold mb-2">🎨 1️⃣ Modal de Asignación de Equipo</h3>
                    <p class="text-sm text-muted-foreground mb-3">
                        Interface para asignar un equipo a un empleado desde un ticket. Incluye validaciones, 
                        selección de equipo disponible, elección de empleado y detección de equipos ya asignados.
                    </p>
                    <div class="flex gap-2">
                        <Button @click="showAssignModal = true" class="bg-blue-600 hover:bg-blue-700">
                            <Laptop class="size-4 mr-2" />
                            Abrir Modal de Asignación
                        </Button>
                    </div>
                </div>

                <!-- Preview del flujo -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">✅ Características:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Selección de equipos disponibles con detalles</li>
                            <li>• Información completa por equipo (tipo, marca, modelo, S/N)</li>
                            <li>• Selección de cualquier empleado</li>
                            <li>• Advertencia si el empleado ya tiene equipo</li>
                            <li>• Confirmación de reemplazo</li>
                            <li>• Campo de observaciones</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">🎯 Datos de ejemplo:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Ticket: TK-123</li>
                            <li>• 4 equipos disponibles</li>
                            <li>• 4 empleados</li>
                            <li>• 2 con equipos ya asignados</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Sección de Equipos del Ticket -->
            <div v-show="activeTab === 'equipment'" class="space-y-4">
                <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800">
                    <h3 class="font-semibold mb-2">📋 2️⃣ Sección de Equipos Asociados</h3>
                    <p class="text-sm text-muted-foreground">
                        Muestra todos los equipos que han sido vinculados a un ticket con acciones de asignación, 
                        devolución o cambio. Incluye timeline cronológico e información del técnico.
                    </p>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <TicketEquipmentSection 
                        ticket-type="EQUIPMENT"
                        ticket-status="IN_PROGRESS"
                        @assign-equipment="showAssignModal = true"
                        @view-equipment="showQuickView = true"
                    />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">✅ Características:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Timeline cronológico de acciones</li>
                            <li>• Badges de color por tipo de acción</li>
                            <li>• Información del técnico</li>
                            <li>• Notas y observaciones</li>
                            <li>• Botón de vista rápida del equipo</li>
                            <li>• Manejo de estado vacío</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">🏷️ Tipos de acciones:</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge class="bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800">
                                ASIGNACIÓN
                            </Badge>
                            <Badge class="bg-amber-500/20 text-amber-700 dark:text-amber-400 border-amber-300 dark:border-amber-800">
                                DEVOLUCIÓN
                            </Badge>
                            <Badge class="bg-blue-500/20 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-800">
                                CAMBIO
                            </Badge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Timeline del Ticket -->
            <div v-show="activeTab === 'timeline'" class="space-y-4">
                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800">
                    <h3 class="font-semibold mb-2">🕓 3️⃣ Historial del Ticket (Timeline)</h3>
                    <p class="text-sm text-muted-foreground">
                        Timeline completo del ticket con eventos de estado, asignaciones, comentarios y 
                        acciones sobre activos. Diseñado para auditoría y trazabilidad ITIL.
                    </p>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <TicketTimeline />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">✅ Características:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Línea de tiempo vertical</li>
                            <li>• Iconos diferenciados por tipo</li>
                            <li>• Información de usuario y rol</li>
                            <li>• Detalles adicionales para eventos de activos</li>
                            <li>• Orden cronológico descendente</li>
                            <li>• Badges de colores por categoría</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">📌 Tipos de eventos:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Creación del ticket</li>
                            <li>• Cambios de estado</li>
                            <li>• Asignación de técnico</li>
                            <li>• Comentarios</li>
                            <li>• Acciones sobre activos</li>
                            <li>• Resolución</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Vista Rápida del Activo -->
            <div v-show="activeTab === 'quickview'" class="space-y-4">
                <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                    <h3 class="font-semibold mb-2">🧾 4️⃣ Vista Rápida del Activo</h3>
                    <p class="text-sm text-muted-foreground mb-3">
                        Panel lateral (Sheet/Drawer) que muestra información completa del activo sin salir del ticket. 
                        Solo lectura, pensado para consulta rápida del técnico.
                    </p>
                    <Button @click="showQuickView = true" class="bg-amber-600 hover:bg-amber-700">
                        <Eye class="size-4 mr-2" />
                        Abrir Vista Rápida
                    </Button>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">✅ Características:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Panel lateral (Sheet/Side panel)</li>
                            <li>• Información básica del activo</li>
                            <li>• Empleado asignado actualmente</li>
                            <li>• Especificaciones técnicas</li>
                            <li>• Historial resumido (últimas 4 acciones)</li>
                            <li>• Información de garantía</li>
                            <li>• Solo lectura</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-lg border bg-card space-y-2">
                        <h4 class="font-semibold text-sm">📋 Información mostrada:</h4>
                        <ul class="text-sm space-y-1 text-muted-foreground">
                            <li>• Tipo, marca, modelo</li>
                            <li>• Número de serie</li>
                            <li>• Estado actual</li>
                            <li>• Procesador, RAM, Almacenamiento</li>
                            <li>• Fechas de compra y garantía</li>
                            <li>• Última actualización</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Guía de Implementación -->
            <div v-show="activeTab === 'guide'" class="space-y-4">
                <div class="p-4 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800">
                    <h3 class="font-semibold mb-2">📚 Guía de Implementación</h3>
                    <p class="text-sm text-muted-foreground">
                        Información sobre cómo usar estos componentes en tu aplicación
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Archivos creados -->
                    <div class="p-6 rounded-lg border bg-card">
                        <h4 class="text-lg font-semibold mb-4">📁 Archivos Creados</h4>
                        <div class="space-y-2 text-sm font-mono">
                            <div class="flex items-center gap-2 p-2 rounded bg-muted/50">
                                <FileCode class="size-4 text-blue-600" />
                                <span>resources/js/components/tickets/AssignEquipmentModal.vue</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded bg-muted/50">
                                <FileCode class="size-4 text-purple-600" />
                                <span>resources/js/components/tickets/TicketEquipmentSection.vue</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded bg-muted/50">
                                <FileCode class="size-4 text-green-600" />
                                <span>resources/js/components/tickets/TicketTimeline.vue</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded bg-muted/50">
                                <FileCode class="size-4 text-amber-600" />
                                <span>resources/js/components/tickets/AssetQuickView.vue</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cómo usar -->
                    <div class="p-6 rounded-lg border bg-card">
                        <h4 class="text-lg font-semibold mb-4">🚀 Cómo Usar</h4>
                        <div class="space-y-4">
                            <div>
                                <h5 class="font-semibold text-sm mb-2">1. Modal de Asignación:</h5>
                                <pre class="text-xs bg-muted p-3 rounded overflow-auto">{{ assignModalCode }}</pre>
                            </div>
                            <div>
                                <h5 class="font-semibold text-sm mb-2">2. Sección de Equipos:</h5>
                                <pre class="text-xs bg-muted p-3 rounded overflow-auto">{{ equipmentSectionCode }}</pre>
                            </div>
                            <div>
                                <h5 class="font-semibold text-sm mb-2">3. Timeline:</h5>
                                <pre class="text-xs bg-muted p-3 rounded overflow-auto">{{ timelineCode }}</pre>
                            </div>
                            <div>
                                <h5 class="font-semibold text-sm mb-2">4. Vista Rápida:</h5>
                                <pre class="text-xs bg-muted p-3 rounded overflow-auto">{{ quickViewCode }}</pre>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Mock -->
                    <div class="p-6 rounded-lg border bg-card">
                        <h4 class="text-lg font-semibold mb-4">💾 Datos Mock Incluidos</h4>
                        <p class="text-sm text-muted-foreground mb-4">
                            Todos los componentes incluyen datos de ejemplo. Para conectar con tu backend, 
                            reemplaza las constantes mock con llamadas a tu API.
                        </p>
                        <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                            <p class="text-sm font-semibold mb-2">⚠️ Importante:</p>
                            <ul class="text-sm space-y-1 text-muted-foreground">
                                <li>• Los componentes son solo interfaz (UI)</li>
                                <li>• No incluyen lógica de backend</li>
                                <li>• Debes implementar los endpoints correspondientes</li>
                                <li>• Todos los datos son estáticos para demostración</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales -->
        <AssignEquipmentModal 
            v-model:open="showAssignModal"
            :ticket-number="123"
            requester-name="Juan Pérez García"
        />

        <AssetQuickView v-model:open="showQuickView" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Laptop, Eye, FileCode } from 'lucide-vue-next';

import AssignEquipmentModal from './AssignEquipmentModal.vue';
import TicketEquipmentSection from './TicketEquipmentSection.vue';
import TicketTimeline from './TicketTimeline.vue';
import AssetQuickView from './AssetQuickView.vue';

const activeTab = ref('assign');
const showAssignModal = ref(false);
const showQuickView = ref(false);

const tabs = [
    { id: 'assign', label: '1️⃣ Modal de Asignación' },
    { id: 'equipment', label: '2️⃣ Equipos Asociados' },
    { id: 'timeline', label: '3️⃣ Timeline' },
    { id: 'quickview', label: '4️⃣ Vista Rápida' },
    { id: 'guide', label: '📚 Guía' },
];

const assignModalCode = `<AssignEquipmentModal 
  v-model:open="showModal"
  :ticket-number="123"
  requester-name="Juan Pérez"
/>`;

const equipmentSectionCode = `<TicketEquipmentSection 
  ticket-type="EQUIPMENT"
  ticket-status="IN_PROGRESS"
  @assign-equipment="handleAssign"
  @view-equipment="handleView"
/>`;

const timelineCode = `<TicketTimeline />`;

const quickViewCode = `<AssetQuickView 
  v-model:open="showQuickView"
/>`;
</script>
