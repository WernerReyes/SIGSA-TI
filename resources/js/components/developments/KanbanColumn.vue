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
            </div>
        </div>
    
        <draggable :scroll="true" :force-fallback="true" :scroll-sensitivity="200" :scroll-speed="20"
            :disabled="!isFromTI" v-model="devRequests" tag="transition-group" :data-status="status" :move="checkMove"
            :component-data="{
                tag: 'div',
                type: 'transition',
                name: 'fade'
            }" @end="moveDevelopment" :group="{ name: 'dev-requests', pull: true, put: true }" item-key="id"
            animation="200" >
            <!-- Professional Empty State -->
             <!-- TODO : Empty state component should have a full height to fill the column -->
            <div v-if="devRequests.length === 0" :key="`empty-${title}`"
                class="flex flex-col items-center bg-red-500  justify-center h-full text-center rounded-lg border-2 border-dashed bg-muted/20">
                <Inbox class="h-10 w-10 text-muted-foreground/40 mb-3" />
                <p class="text-sm font-medium text-muted-foreground">Sin solicitudes</p>
                <p class="text-xs text-muted-foreground/60 mt-1">Arrastra aquí para agregar</p>
            </div>

            <!-- Clean Professional Cards -->

            <div v-for="devRequest in devRequests" :key="devRequest.id"
                class="bg-card rounded-lg border shadow-card p-3 mb-3 transition-shadow">
                <div class="flex items-start justify-between"><span
                        class="font-mono text-xs text-muted-foreground">DEV-{{ devRequest.id }}</span>

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

                            <DropdownMenuItem class="cursor-pointer"
                                v-if="[DRStatus.IN_DEVELOPMENT, DRStatus.IN_TESTING, DRStatus.COMPLETED].includes(devRequest.status)"
                                @click="emit('open-progress', devRequest)">
                                <TrendingUp />
                                Registrar Avance
                            </DropdownMenuItem>

                            <DropdownMenuSeparator v-if='devRequest.status === DRStatus.REGISTERED' />
                            <DropdownMenuItem @click="emit('deleted', devRequest.id)"
                                v-if="devRequest.status === DRStatus.REGISTERED"
                                class="cursor-pointer text-destructive focus:text-destructive">
                                <Trash2 />
                                Eliminar
                            </DropdownMenuItem>


                        </DropdownMenuContent>
                    </DropdownMenu>


                </div>
                <h4 class="font-medium text-sm mt-2 line-clamp-1">{{ devRequest.title }}</h4>
                <p class="text-xs text-muted-foreground mt-1 line-clamp-2">{{ devRequest.description }}</p>
                <div class="flex items-center gap-2 mt-2 flex-wrap">
                    <Badge
                        :class="getPriorityOp(devRequest.priority).bg + ' text-xs font-medium flex items-center gap-1'">
                        <component :is="getPriorityOp(devRequest.priority).icon" class="h-3 w-3" />
                        {{ getPriorityOp(devRequest.priority).label }}
                    </Badge>
                    <div
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 text-xs">
                        {{ devRequest?.area?.descripcion_area }}
                    </div>
                </div>
                <div class="mt-2" v-if="[DRStatus.IN_DEVELOPMENT, DRStatus.IN_TESTING, DRStatus.COMPLETED].includes(devRequest.status)">
                    <div class="flex justify-between text-xs mb-1"><span
                            class="text-muted-foreground">Progreso</span><span>
                            {{ devRequest.latest_progress?.percentage || 0 }}%

                            </span></div>
                    <Progress :model-value="devRequest.latest_progress?.percentage || 0" />
                </div>

                <div class="mt-2 pt-2 border-t border-border space-y-1">
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <User class="size-3" />

                        <span class="truncate">{{ devRequest?.requested_by?.full_name }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <CalendarIcon class="size-3 text-muted-foreground" />
                        <span class="text-muted-foreground truncate">
                            Est:
                            {{ devRequest?.estimated_end_date ? format(parseDateOnly(devRequest?.estimated_end_date),
                                'dd/MM/yyyy') : 'Sin fecha estimada'

                            }}</span>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 p-1.5 bg-muted/50 rounded">

                    <button class="flex items-center gap-1 hover:opacity-80">


                        <component :is="getStatusOp(devRequest.technical_approval?.status).icon"
                            :class="['w-3.5 h-3.5', getStatusOp(devRequest.technical_approval?.status).color]" />

                        <span class="text-[10px] text-muted-foreground">Téc</span>

                    </button>



                    <button class="flex items-center gap-1 hover:opacity-80">

                        <component :is="getStatusOp(devRequest.strategic_approval?.status).icon"
                            :class="['w-3.5 h-3.5', getStatusOp(devRequest.strategic_approval?.status).color]" />
                        <span class="text-[10px] text-muted-foreground">Est</span>
                    </button>



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
import { CalendarIcon, ClipboardCheck, Eye, Inbox, MonitorCheck, MoreVertical, Pencil, Save, Trash2, User, TrendingUp } from 'lucide-vue-next';
import { computed, onUnmounted, ref } from 'vue';

import { getStatusOp } from '@/interfaces/developmentApproval.interface';
import { parseDateOnly } from '@/lib/utils';
import { format } from 'date-fns';
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

    (e: 'error', message: string): void;

}>();

const { isFromTI, isTIManager, isLoading, isTIAssistantManager, isSameUser } = useApp();

const hasMovedInAnotherColumn = ref<boolean>(false);

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
    if (fromStatus === DRStatus.IN_ANALYSIS || fromStatus === DRStatus.REJECTED || fromStatus === DRStatus.COMPLETED) {
        return false
    }

    if (fromStatus === DRStatus.IN_TESTING && toStatus === DRStatus.COMPLETED) {
        if (!item.latest_progress || item.latest_progress.percentage < 100) {
            emit('error', 'No se puede mover a Producción si el progreso no es 100%.');
            return false;
        }
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

    if (fromIndex === -1 || toIndex === -1) return;

    const returnValue = toIndex === fromIndex + 1;

    if (fromStatus !== toStatus) {
        hasMovedInAnotherColumn.value = true;
    }

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
    const sortableInstance = element[sortableKey!];
    const dataStatus = sortableInstance?.options?.dataStatus;
    return dataStatus || null;
}
</script>

<style scoped>
.fade-item {
    padding: 15px;
    margin: 8px 0;
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>