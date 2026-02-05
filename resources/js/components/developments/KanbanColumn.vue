<template>
    <div class=" shrink-0 flex flex-col bg-background    rounded-lg border border-border shadow-sm">
        <!-- Professional Header -->
        <div class="px-4 py-3.5 border-b bg-muted/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-1 h-8 rounded-full" :style="{ backgroundColor: headerColor }"></div>
                    <div>
                        <h3 class="font-semibold text-sm leading-none">{{ title }}</h3>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ devRequests.length }} {{ devRequests.length === 1 ? 'solicitud' : 'solicitudes' }}
                        </p>
                    </div>
                </div>
                 
                <Button v-if="hasPositionChanged && isFromTI && !isLoading" size="icon" variant="ghost"
                    @click="swapPositions" class="h-8 w-8 hover:bg-accent">
                    <Save class="h-4 w-4" />
                </Button>
                {{devRequests.length}}
            </div>
        </div>

        <draggable :scroll="true" :force-fallback="true" :scroll-sensitivity="200" :scroll-speed="20"
            :disabled="!isFromTI" v-model="devRequests" tag="transition-group" :data-status="status" :move="checkMove"
            :component-data="{
                tag: 'div',
                type: 'transition',
                name: 'fade'
            }" @end="moveDevelopment" @change="(e) => {
                if (e.added || e.removed) {
                    hasMovedInAnotherColumn = true;
                } else {
                    hasMovedInAnotherColumn = false;
                }
            }" :group="{ name: 'dev-requests', pull: true, put: true }" item-key="id" animation="300"
            easing="cubic-bezier(0.4, 0, 0.2, 1)" :ghost-class="'kanban-ghost'" :chosen-class="'kanban-chosen'"
            :drag-class="'kanban-drag'" class="flex-1 p-4">
            <!-- Professional Empty State -->
            <div v-if="devRequests.length === 0" :key="`empty-${title}`"
                class="flex flex-col items-center justify-center min-h-100 text-center rounded-lg bg-muted/20 p-8">
                <Inbox class="h-10 w-10 text-muted-foreground/40 mb-3" />
                <p class="text-sm font-medium text-muted-foreground">Sin solicitudes</p>
                <p class="text-xs text-muted-foreground/60 mt-1">Arrastra aquí para agregar</p>
            </div>

            
            <!-- Clean Professional Cards -->
            <div v-for="devRequest in devRequests" :key="devRequest.id"
            class="bg-card rounded-lg border shadow-card p-2.5 mb-2 transition-all hover:shadow-md">
            
                <div class="flex items-start justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs text-muted-foreground">DEV-{{ devRequest.id }}</span>
                        <Badge
                            :class="getPriorityOp(devRequest.priority).bg + ' text-[10px] h-4 px-1.5 font-medium flex items-center gap-1'">
                            <component :is="getPriorityOp(devRequest.priority).icon" class="h-2.5 w-2.5" />
                        </Badge>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button size="icon" variant="ghost" class="h-7 w-7 ">
                                <MoreVertical class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem class="cursor-pointer" @click="emit('open-view', devRequest)">
                                <Eye />
                                Ver detalles
                            </DropdownMenuItem>
                            <DropdownMenuItem :disabled="!isSameUser(devRequest.requested_by_id)" class="cursor-pointer"
                                v-if="devRequest.status == DRStatus.REGISTERED"
                                @click="emit('open-update', devRequest)">
                                <Pencil />
                                Editar
                            </DropdownMenuItem>

                            <DropdownMenuItem
                                :disabled="!!devRequest.strategic_approval || !!devRequest.technical_approval"
                                class="cursor-pointer" v-if="devRequest.status == DRStatus.IN_ANALYSIS"
                                @click="emit('open-estimation', devRequest)">
                                <Pencil />
                                Estimar
                            </DropdownMenuItem>

                            <DropdownMenuItem class="cursor-pointer"
                                :disabled="!!devRequest.technical_approval || !hasEstimation(devRequest)"
                                v-if="devRequest.status == DRStatus.IN_ANALYSIS && isTIManager"
                                @click="emit('open-technical-approval', devRequest)">
                                <MonitorCheck />
                                Aprobar
                            </DropdownMenuItem>

                            <DropdownMenuItem class="cursor-pointer"
                                :disabled="!!devRequest.strategic_approval || !hasEstimation(devRequest)"
                                v-if="devRequest.status == DRStatus.IN_ANALYSIS && isTIAssistantManager"
                                @click="emit('open-strategic-approval', devRequest)">
                                <ClipboardCheck />
                                Aprobar
                            </DropdownMenuItem>

                            <DropdownMenuItem class="cursor-pointer" :disabled="devRequest?.developers?.length === 0"
                                v-if="[DRStatus.IN_DEVELOPMENT, DRStatus.IN_TESTING, DRStatus.COMPLETED].includes(devRequest.status)"
                                @click="emit('open-progress', devRequest)">
                                <TrendingUp />
                                Avance
                            </DropdownMenuItem>

                            <DropdownMenuItem class="cursor-pointer"
                                v-if="devRequest.status === DRStatus.IN_DEVELOPMENT"
                                @click="emit('open-assign-developers', devRequest)">
                                <Users />
                                Desarrolladores
                            </DropdownMenuItem>

                            <template v-if='[DRStatus.REGISTERED, DRStatus.REJECTED].includes(devRequest.status)'>
                                <DropdownMenuSeparator />

                                <DropdownMenuItem :disabled="!isSameUser(devRequest.requested_by_id)"
                                    @click="emit('deleted', devRequest.id)"
                                    class="cursor-pointer text-destructive focus:text-destructive">
                                    <Trash2 />
                                    Eliminar
                                </DropdownMenuItem>
                            </template>


                        </DropdownMenuContent>
                    </DropdownMenu>


                </div>

                <!-- Información esencial siempre visible -->
                <h4 class="font-medium text-sm line-clamp-1 mb-1">{{ devRequest.title }}</h4>
                <p class="text-xs text-muted-foreground line-clamp-1 mb-2">{{ devRequest.description }}</p>

                <!-- Info compacta -->
                <div class="flex items-center justify-between text-[10px] text-muted-foreground mb-2">
                    <div class="flex items-center gap-1 truncate">
                        <User class="size-2.5 shrink-0" />
                        <span class="truncate">{{ devRequest?.requested_by?.full_name }}</span>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <component :is="getStatusOp(devRequest.technical_approval?.status).icon"
                            :class="['w-2.5 h-2.5', getStatusOp(devRequest.technical_approval?.status).color]" />
                        <component :is="getStatusOp(devRequest.strategic_approval?.status).icon"
                            :class="['w-2.5 h-2.5', getStatusOp(devRequest.strategic_approval?.status).color]" />
                    </div>
                </div>
                <!-- Progreso compacto -->
                <div class="mb-2"
                    v-if="[DRStatus.IN_DEVELOPMENT, DRStatus.IN_TESTING, DRStatus.COMPLETED].includes(devRequest.status)">
                    <div class="flex justify-between text-[10px] mb-0.5">
                        <span class="text-muted-foreground">Progreso</span>
                        <span class="font-medium">{{ devRequest.latest_progress?.percentage || 0 }}%</span>
                    </div>
                    <Progress :model-value="devRequest.latest_progress?.percentage || 0" class="h-1" />
                </div>

                <!-- Botón de expansión -->
                <Button @click="() => {
                    if (expandedCards.has(devRequest.id)) {
                        expandedCards.delete(devRequest.id);
                    } else {
                        expandedCards.add(devRequest.id);
                    }
                }" variant="ghost" size="sm"
                    class="w-full h-6 text-[10px] text-muted-foreground hover:text-foreground">
                    <component :is="expandedCards.has(devRequest.id) ? ChevronUp : ChevronDown" class="h-3 w-3 mr-1" />
                    {{ expandedCards.has(devRequest.id) ? 'Menos detalles' : 'Más detalles' }}
                </Button>

                <!-- Detalles expandibles -->
                <div v-if="expandedCards.has(devRequest.id)"
                    class="mt-2 pt-2 border-t space-y-2 animate-in slide-in-from-top-2">
                    <!-- Área -->
                    <div
                        class="inline-flex items-center rounded-full border px-2 py-0.5 border-transparent bg-secondary text-secondary-foreground text-[10px]">
                        {{ devRequest?.area?.descripcion_area }}
                    </div>

                    <!-- Project URL -->
                    <div
                        v-if="[DRStatus.IN_DEVELOPMENT, DRStatus.IN_TESTING, DRStatus.COMPLETED].includes(devRequest.status)">
                        <div class="flex items-center gap-1.5 p-1.5 bg-muted/30 rounded border border-border/50">
                            <Globe class="h-3 w-3 text-muted-foreground shrink-0" />
                            <div class="flex-1 min-w-0">
                                <input type="text" placeholder="URL del proyecto"
                                    class="w-full text-[10px] bg-transparent border-none outline-none focus:ring-0 p-0 placeholder:text-muted-foreground/40"
                                    :value="devRequest.project_url || ''"
                                    @input="(e) => devRequest.project_url = (e.target as HTMLInputElement).value" />
                            </div>
                            <Button size="icon" variant="ghost"
                                :disabled="!devRequest.project_url || devRequest.project_url.trim() === ''"
                                class="h-5 w-5 shrink-0" @click="saveProjectUrl(devRequest)">
                                <Check class="h-2.5 w-2.5" />
                            </Button>
                            <Button size="icon" variant="ghost" class="h-5 w-5 shrink-0"
                                :disabled="!devRequest.project_url || devRequest.project_url.trim() === ''" @click="() => {
                                    if (devRequest.project_url) {
                                        openInNewTab(devRequest.project_url);
                                    }
                                }">
                                <ExternalLink class="h-2.5 w-2.5" />
                            </Button>
                        </div>
                    </div>

                    <!-- Fechas y horas -->
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <CalendarIcon class="size-2.5 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground">
                                Est: {{ devRequest?.estimated_end_date ?
                                    format(parseDateOnly(devRequest?.estimated_end_date),
                                'dd/MM/yy') : 'Sin fecha' }}
                            </span>
                            <span v-if="devRequest.status === DRStatus.COMPLETED" :class="{
                                'text-red-600': isAfter(parseDateOnly(devRequest?.completed_at || ''), parseDateOnly(devRequest?.estimated_end_date || '')),
                                'text-green-600': !isAfter(parseDateOnly(devRequest?.completed_at || ''), parseDateOnly(devRequest?.estimated_end_date || ''))
                            }">
                                | Real: {{ devRequest?.completed_at ? format(parseDateOnly(devRequest?.completed_at),
                                'dd/MM/yy') :
                                'N/A' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <Clock class="size-2.5 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground">
                                {{ devRequest?.estimated_hours }}h est.
                            </span>
                            <span v-if="devRequest.status === DRStatus.COMPLETED" :class="{
                                'text-red-600': (devRequest?.actual_hours ?? 0) > (devRequest?.estimated_hours ?? 0),
                                'text-green-600': (devRequest?.actual_hours ?? 0) <= (devRequest?.estimated_hours ?? 0),
                            }">
                                | {{ devRequest?.actual_hours ?? 0 }}h trabajadas
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </draggable>
        <!-- </ScrollArea>  -->

    </div>
</template>

<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Progress } from '@/components/ui/progress';
import { useApp } from '@/composables/useApp';
import { DevelopmentRequestSection, DevelopmentRequestStatus as DRStatus, getPriorityOp, type DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/core';
import { CalendarIcon, Check, ChevronDown, ChevronUp, ClipboardCheck, Eye, Globe, ExternalLink, Inbox, MonitorCheck, MoreVertical, Pencil, Save, Trash2, User, TrendingUp, Clock, Users } from 'lucide-vue-next';
import { computed, onUnmounted, ref } from 'vue';
import { getStatusOp } from '@/interfaces/developmentApproval.interface';
import { parseDateOnly } from '@/lib/utils';
import { format, isAfter } from 'date-fns';
import { VueDraggableNext as draggable, SortableEvent, type MoveEvent } from 'vue-draggable-next';


const devRequests = defineModel<Array<DevelopmentRequest>>('devRequests', {
    default: () => [],
});

const developmentsByStatus = defineModel<DevelopmentRequestSection>('developmentsByStatus', {
    default: () => ({}),
});

const { status } = defineProps<{
    title: string;
    headerColor?: string;
    status: DRStatus;
}>();

const emit = defineEmits<{
    (e: 'moved', id: number, newStatus: DRStatus): void;
    (e: 'deleted', id: number): void;

    (e: 'open-view', item: DevelopmentRequest): void;
    (e: 'open-update', item: DevelopmentRequest): void;
    (e: 'open-estimation', item: DevelopmentRequest): void;
    (e: 'open-technical-approval', item: DevelopmentRequest): void;
    (e: 'open-strategic-approval', item: DevelopmentRequest): void;
    (e: 'open-progress', item: DevelopmentRequest): void;
    (e: 'open-assign-developers', item: DevelopmentRequest): void;
    (e: 'come-back-to-analysis', item: DevelopmentRequest): void;

    (e: 'error', message: string): void;

}>();

const { isFromTI, isTIManager, isLoading, isTIAssistantManager, isSameUser } = useApp();

const hasMovedInAnotherColumn = ref<boolean>(false);
const expandedCards = ref<Set<number>>(new Set());

const originalDevRequests = computed<Array<DevelopmentRequest>>({
    get: () => {
        return developmentsByStatus.value[status] ? [...developmentsByStatus.value[status]] : [];
    },
    set: (value: Array<DevelopmentRequest>) => {
        developmentsByStatus.value[status] = value;
    }
});

const hasPositionChanged = computed<boolean>(() => {
    if (hasMovedInAnotherColumn.value) return false;
    if (devRequests.value.length !== originalDevRequests.value.length) return true;

    for (let i = 0; i < devRequests.value.length; i++) {
        if (devRequests.value[i].id !== originalDevRequests.value[i].id) {
            return true;
        }
    }
    return false;
});

const saveProjectUrl = (devRequest: DevelopmentRequest) => {
    router.patch(`/developments/${devRequest.id}/project-url`, {
        project_url: devRequest.project_url,
    }, {
        onFlash: (flash) => {
            if (flash.error) {
                devRequest.project_url = '';
            }
        },
    });
};

const openInNewTab = (url: string) => {
    window.open(url, '_blank');
};

const DR_STATUS_FLOW: DRStatus[] = [
    DRStatus.REGISTERED,
    DRStatus.IN_ANALYSIS,
    DRStatus.APPROVED,
    DRStatus.IN_DEVELOPMENT,
    DRStatus.IN_TESTING,
    DRStatus.COMPLETED,
]

let timeoutId: number | null = null;

// Prevent moving locked items
const checkMove = (event: MoveEvent<DevelopmentRequest>) => {
    if (!isFromTI.value || isLoading.value) return false;
    const item = event.draggedContext?.element
    if (!item) return false

    const fromStatus = getCurrentStatus(event.from);
    const toStatus = getCurrentStatus(event.to)

    if (!fromStatus || !toStatus) return false

    // ✔️ Reordenar dentro del mismo estado
    if (fromStatus === toStatus) return true

    // ⛔ Estados terminales no se mueven
    if (fromStatus === DRStatus.IN_ANALYSIS || fromStatus === DRStatus.COMPLETED) {
        return false
    }

    if (fromStatus == DRStatus.IN_DEVELOPMENT && toStatus == DRStatus.IN_TESTING) {
        if (!item.developers || item.developers.length === 0) {
            emit('error', 'No se puede mover a Testing sin desarrolladores asignados.');
            return false;
        }
    }

    if (fromStatus === DRStatus.IN_TESTING && toStatus === DRStatus.COMPLETED) {
        if (!item.latest_progress || item.latest_progress.percentage < 100) {
            emit('error', 'No se puede mover a Producción si el progreso no es 100%.');
            return false;
        }
    }

    if (fromStatus === DRStatus.REJECTED && toStatus === DRStatus.IN_ANALYSIS) {
        return true;
    }

    const fromIndex = DR_STATUS_FLOW.indexOf(fromStatus)
    const toIndex = DR_STATUS_FLOW.indexOf(toStatus)

    if (fromIndex === -1 || toIndex === -1) return false

    const returnValue = toIndex === fromIndex + 1;




    return returnValue;
}

onUnmounted(() => {
    if (timeoutId) {
        clearTimeout(timeoutId); // Manual cleanup is crucial
    }
});


const moveDevelopment = (e: SortableEvent) => {
    const fromStatus = getCurrentStatus(e.from);
    const toStatus = getCurrentStatus(e.to)

    if (!fromStatus || !toStatus) return;

    const item = e.item._underlying_vm_;

    const fromIndex = DR_STATUS_FLOW.indexOf(fromStatus)
    const toIndex = DR_STATUS_FLOW.indexOf(toStatus)

    if (fromStatus === DRStatus.IN_ANALYSIS) {
        return;
    }

    if (fromStatus === DRStatus.REJECTED && toStatus === DRStatus.IN_ANALYSIS) {
        emit('come-back-to-analysis', item);
        return;
    }

    if (fromIndex === -1 || toIndex === -1) return;

    const returnValue = toIndex === fromIndex + 1;



    if (returnValue) {
        emit('moved', item.id, toStatus);
    }
}



const swapPositions = () => {
    router.patch('/developments/position', {
        devs_ids_in_order: devRequests.value.map(dev => dev.id),
        status,
    }, {
        onFlash: (flash) => {
            if (flash.success) {
                hasPositionChanged.value = false;
                originalDevRequests.value = [...devRequests.value];
            }
        },

    });
}


const hasEstimation = (devRequest: DevelopmentRequest) => {
    return devRequest.estimated_hours !== null && devRequest.estimated_end_date !== null;
}
function getCurrentStatus(element: HTMLElement): DRStatus | null {
    const sortableKey = Object.keys(element).find(key => key.startsWith('Sortable')) || '';
    const sortableInstance = element[sortableKey];
    const dataStatus = sortableInstance?.options?.dataStatus;
    return dataStatus || null;
}
</script>

<style scoped>
/* Transiciones suaves para las tarjetas */
.fade-enter-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 1, 1);
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(10px) scale(0.95);
}

.fade-move {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Estilos para el dragging */
:deep(.kanban-ghost) {
    opacity: 0.4;
    background: hsl(var(--primary) / 0.1);
    border: 2px dashed hsl(var(--primary));
    transform: rotate(2deg);
}

:deep(.kanban-chosen) {
    cursor: grabbing;
    transform: scale(1.02);
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
        box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.kanban-drag) {
    opacity: 0.8;
    cursor: grabbing;
    transform: rotate(-3deg) scale(1.05);
    box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.3);
    transition: none;
}

/* Animación suave de las tarjetas */
.bg-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.bg-card:hover {
    transform: translateY(-2px);
}
</style>