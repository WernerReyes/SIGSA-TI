<template>
    <div class="w-full shrink-0 flex flex-col bg-background rounded-lg border border-border shadow-sm">
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
        <!-- <ScrollArea class="max-h-full overflow-y-auto flex-1 p-3"> -->


        <draggable :scroll="true" :force-fallback="true" :scroll-sensitivity="200" :scroll-speed="20"
            :disabled="!isFromTI || isLoading" v-model="devRequests" tag="transition-group" :data-status="status"
            @change="hasPositionChanged = true" :move="checkMove" :component-data="{
                tag: 'div',
                type: 'transition',
                name: 'fade'
            }" :group="{ name: 'dev-requests', pull: true, put: true }" item-key="id" animation="200">
            <!-- Professional Empty State -->
            <div v-if="devRequests.length === 0" :key="`empty-${title}`"
                class="flex flex-col items-center justify-center h-60 text-center rounded-lg border-2 border-dashed bg-muted/20">
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

                            <DropdownMenuItem class="cursor-pointer" v-if="devRequest.status == DRStatus.IN_ANALYSIS"
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

                            <DropdownMenuSeparator v-if="devRequest.status !== DRStatus.REGISTERED" />
                            <DropdownMenuItem @click="() => {

                                emit('error', 'Funcionalidad de eliminación no implementada aún.')
                            }" v-if="devRequest.status !== DRStatus.REGISTERED"
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
                <div class="mt-2">
                    <div class="flex justify-between text-xs mb-1"><span
                            class="text-muted-foreground">Progreso</span><span>75%</span></div>
                    <div aria-valuemax="100" aria-valuemin="0" role="progressbar" data-state="indeterminate"
                        data-max="100" class="relative w-full overflow-hidden rounded-full bg-secondary h-1.5">
                        <div data-state="indeterminate" data-max="100"
                            class="h-full w-full flex-1 bg-primary transition-all" style="transform: translateX(-25%);">
                        </div>
                    </div>
                </div>

                <!-- // TODO: Implement the real data below -->
                <div class="mt-2 pt-2 border-t border-border space-y-1">
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <User class="size-3" />

                        <span class="truncate">{{ devRequest?.requested_by?.full_name }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar w-3 h-3 text-muted-foreground">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                            <path d="M3 10h18"></path>
                        </svg>
                        <span class="text-muted-foreground truncate">{{ devRequest?.estimated_end_date }}</span>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 p-1.5 bg-muted/50 rounded"><button
                        class="flex items-center gap-1 hover:opacity-80"><svg xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-check w-3.5 h-3.5 text-success">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg><span class="text-[10px] text-muted-foreground">Téc</span></button><button
                        class="flex items-center gap-1 hover:opacity-80"><svg xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-check w-3.5 h-3.5 text-success">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg><span class="text-[10px] text-muted-foreground">Est</span></button><span
                        class="text-[10px] text-muted-foreground ml-auto">8 eventos</span></div>
            </div>

        </draggable>
        <!-- </ScrollArea>  -->

    </div>
</template>

<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useApp } from '@/composables/useApp';
import { DevelopmentRequestStatus as DRStatus, getPriorityOp, type DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/core';
import { ClipboardCheck, Eye, Inbox, MonitorCheck, MoreVertical, Pencil, Plus, Save, Trash2, User } from 'lucide-vue-next';
import { computed, onUnmounted, ref } from 'vue';

import { VueDraggableNext as draggable, type MoveEvent } from 'vue-draggable-next';


const devRequests = defineModel<Array<DevelopmentRequest>>('devRequests', {
    default: () => [],
});

const { status } = withDefaults(defineProps<{
    title: string;
    headerColor?: string;
    status: DRStatus;
}>(), {
    headerColor: '#6366f1'
});

const emit = defineEmits<{
    (e: 'moved', id: number, newStatus: DRStatus): void;

    (e: 'open-view', item: DevelopmentRequest): void;
    (e: 'open-update', item: DevelopmentRequest): void;
    (e: 'open-estimation', item: DevelopmentRequest): void;
    (e: 'open-technical-approval', item: DevelopmentRequest): void;
    (e: 'open-strategic-approval', item: DevelopmentRequest): void;
    (e: 'error', message: string): void;

}>();

const { isFromTI, isTIManager, isLoading, isTIAssistantManager, isSameUser } = useApp();

const hasPositionChanged = ref(false);

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
    if (fromStatus === DRStatus.REJECTED || fromStatus === DRStatus.COMPLETED) {
        return false
    }

    if (fromStatus === DRStatus.IN_ANALYSIS) {
        if (!isTIManager.value && !isTIAssistantManager.value) return false
        if (toStatus === DRStatus.REJECTED) return true;
        if (toStatus === DRStatus.APPROVED) {
            if (item.estimated_hours === null || item.estimated_end_date === null) {
                emit('error', 'No se puede aprobar el requerimiento sin una estimación de tiempo y fecha de finalización.');
                return false;
            }

            if (!item.technical_approval && !item.strategic_approval) {
                emit('error', 'No se puede aprobar el requerimiento sin la aprobación técnica y estratégica.');
                return false;
            } else if (!item.technical_approval) {
                emit('error', 'No se puede aprobar el requerimiento sin la aprobación técnica.');
                return false;
            } else if (!item.strategic_approval) {
                emit('error', 'No se puede aprobar el requerimiento sin la aprobación estratégica.');
                return false;
            }

        }


    }

    const fromIndex = DR_STATUS_FLOW.indexOf(fromStatus)
    const toIndex = DR_STATUS_FLOW.indexOf(toStatus)

    if (fromIndex === -1 || toIndex === -1) return false

    const returnValue = toIndex === fromIndex + 1;

    if (returnValue) {
        // Emitir evento para actualizar el estado en el backend
        timeoutId = setTimeout(() => {
            emit('moved', item.id, toStatus);
        }, 600);
        // emit('moved', item.id, toStatus);
    }


    return returnValue;
}

onUnmounted(() => {
    if (timeoutId) {
        clearTimeout(timeoutId); // Manual cleanup is crucial
    }
});


const swapPositions = () => {
    router.patch('/developments/position', {
        devs_ids_in_order: devRequests.value.map(dev => dev.id),
        status,
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