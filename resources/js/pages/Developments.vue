<template>

    <Head title="Desarrollos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 lg:p-6">
            <!-- Header -->
            <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-muted-foreground">Módulo de Gestión</p>
                    <h1 class="text-2xl font-bold leading-tight">Gestión de Cambios y Desarrollos</h1>
                    <p class="text-sm text-muted-foreground">
                        Flujo Kanban de requerimientos desde registro hasta producción
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button @click="showNewRequirementModal = true" class="gap-2" size="sm">
                        <Plus class="h-4 w-4" />
                        Nuevo Requerimiento
                    </Button>
                   <TIUsersProjectsModal 
                :developments-by-status="developmentsByStatus" />
                </div>
            </header>

            <!-- Board Kanban -->
            <div class="grid grid-flow-col max-h-[calc(100vh-200px)] auto-cols-[minmax(240px,1fr)] w-full overflow-x-auto gap-4 kanban-board-scroll">
                <!-- // TODO: Check why it doesn't show the toast error message  -->
                <KanbanColumn v-model:dev-requests="registeredRequests" title="Registrados" header-color="#64748b"
                    v-model:developments-by-status="originalDevelopmentsByStatus" @deleted="(id: number) => {
                        alertDialogInfo = {
                            ...deleteInfo,
                            confirm: () => {
                                deleteDevelopmentRequest(id, DevelopmentRequestStatus.REGISTERED);
                            },
                        }
                        showAlertDialog = true;
                    }" @moved="(id, newStatus) => {
                        updateStatus = { requestId: id, newStatus };
                        alertDialogInfo = { ...changeStatusInfo };
                        showAlertDialog = true;

                    }" @open-update="(item) => {
                        showNewRequirementModal = true;
                        selectedRequirement = item;
                    }" @open-view="(item) => {
                        showDetailModal = true;
                        selectedRequirement = item;
                    }" :status="DevelopmentRequestStatus.REGISTERED" />

                <KanbanColumn v-model:dev-requests="analysisRequests" title="En Análisis" header-color="#3b82f6"
                    v-model:developments-by-status="originalDevelopmentsByStatus" @error="(message) => {
                        showMessage(message);
                    }" @open-estimation="(item) => {
                        showEstimateModal = true;
                        selectedRequirement = item;
                    }" @open-view="(item) => {
                        showDetailModal = true;
                        selectedRequirement = item;
                    }" @open-technical-approval="(item) => {
                        showTechnicalApprovalModal = true;
                        selectedRequirement = item;
                    }" @open-strategic-approval="(item) => {
                        showStrategicApprovalModal = true;
                        selectedRequirement = item;
                    }" :status="DevelopmentRequestStatus.IN_ANALYSIS" @moved="(id, status) => {
                        updateStatus = { requestId: id, newStatus: status };
                        alertDialogInfo = { ...changeStatusInfo };
                        showAlertDialog = true;
                    }" />

                <KanbanColumn v-model:dev-requests="approvedRequests" title="Aprobados" header-color="#6366f1"
                    @open-view="(item) => {
                        showDetailModal = true;
                        selectedRequirement = item;
                    }" @moved="(id, newStatus) => {
                        updateStatus = { requestId: id, newStatus };
                        alertDialogInfo = { ...changeStatusInfo };
                        showAlertDialog = true;
                    }" :status="DevelopmentRequestStatus.APPROVED"
                    v-model:developments-by-status="originalDevelopmentsByStatus" />

                <KanbanColumn @open-view="(item) => {
                    showDetailModal = true;
                    selectedRequirement = item;
                }" @open-progress="(item) => {
                    showProgressModal = true;
                    selectedRequirement = item;
                }" @open-assign-developers="(item) => {
                    showAssignDevelopersModal = true;
                    selectedRequirement = item;
                }" @moved="(id, newStatus) => {
                    updateStatus = { requestId: id, newStatus };
                    alertDialogInfo.description = '¿Estás seguro de que deseas mover este requerimiento a QA?. Esta acción indica que el desarrollo ha sido completado y está listo para ser probado por el equipo de aseguramiento de calidad.';
                    showAlertDialog = true;
                }" v-model:dev-requests="inDevelopmentRequests" title="En Desarrollo" header-color="#8b5cf6" v-model:developments-by-status="originalDevelopmentsByStatus"
                        :status="DevelopmentRequestStatus.IN_DEVELOPMENT" />

                <KanbanColumn @open-view="(item) => {
                    showDetailModal = true;
                    selectedRequirement = item;
                }" @open-progress="(item) => {
                    showProgressModal = true;
                    selectedRequirement = item;
                }" @moved="(id, newStatus) => {
                    updateStatus = { requestId: id, newStatus };

                    alertDialogInfo = {
                        ...changeStatusInfo,
                        description: '¿Estás seguro de que deseas mover este requerimiento a Producción?. Esta acción indica que el desarrollo ha sido completado y está listo para ser usado por los usuarios finales.'
                    };
                    showAlertDialog = true;
                }" v-model:dev-requests="inQARequests" title="En QA" header-color="#f59e0b"
                    :status="DevelopmentRequestStatus.IN_TESTING"  v-model:developments-by-status="originalDevelopmentsByStatus" />

                <KanbanColumn @open-progress="(item) => {
                    showProgressModal = true;
                    selectedRequirement = item;
                }" @open-view="(item) => {
                    showDetailModal = true;
                    selectedRequirement = item;
                }" v-model:dev-requests="inProductionRequests" title="En Producción" header-color="#10b981"
                    :status="DevelopmentRequestStatus.COMPLETED" v-model:developments-by-status="originalDevelopmentsByStatus" />

                <KanbanColumn @open-view="(item) => {
                    showDetailModal = true;
                    selectedRequirement = item;
                }" @deleted="(id: number) => {
                    alertDialogInfo = {
                        ...deleteInfo,
                        confirm: () => {
                            deleteDevelopmentRequest(id, DevelopmentRequestStatus.REJECTED);
                        },
                    }
                    showAlertDialog = true;
                }" @come-back-to-analysis="(item: DevelopmentRequest) => {
                    updateStatus = { requestId: item.id, newStatus: DevelopmentRequestStatus.IN_ANALYSIS };
                    alertDialogInfo = {
                        title: 'Volver a Análisis',
                        description: '¿Estás seguro de que deseas volver este requerimiento a Análisis para su revisión y posible re-trabajo?',
                        confirm: () => {
                            handleComeBackToAnalysis(item);
                        },
                        cancel: () => {
                            rollbackStatus(DevelopmentRequestStatus.REJECTED);

                        },

                    };
                    showAlertDialog = true;
                }" v-model:dev-requests="rejectedRequests" title="Rechazados" header-color="#ef4444"
                    :status="DevelopmentRequestStatus.REJECTED" v-model:developments-by-status="originalDevelopmentsByStatus" />
            </div>

            <DialogDetails v-model:open="showDetailModal" v-model:current-development="selectedRequirement" />
            <UpsertDialog @new-development="originalDevelopmentsByStatus[DevelopmentRequestStatus.REGISTERED].push($event)"
                v-model:open="showNewRequirementModal" v-model:current-development="selectedRequirement" />

            <EstimationDialog v-model:open="showEstimateModal" v-model:current-development="selectedRequirement" />

            <ApprovalDialog v-if="showTechnicalApprovalModal" role="Gerente TI" title="Aprobación Técnica"
                description="Revisar y aprobar el desarrollo desde una perspectiva técnica."
                v-model:open="showTechnicalApprovalModal" v-model:current-development="selectedRequirement" />
            <ApprovalDialog v-if="showStrategicApprovalModal" role="Sub-Gerente de TI" title="Aprobación Estratégica"
                description="Revisar y aprobar el desarrollo desde una perspectiva estratégica."
                v-model:open="showStrategicApprovalModal" v-model:current-development="selectedRequirement" />

            <RegisterProgressDialog v-model:open="showProgressModal"
                v-model:current-development="selectedRequirement" />
            <AssignDevelopersDialog v-model:open="showAssignDevelopersModal"
                v-model:current-development="selectedRequirement" />

            <!-- <TIUsersProjectsModal v-model:open="showTIUsersProjectsModal"
                :developments-by-status="developmentsByStatus" /> -->

            <AlertDialog v-model:open="showAlertDialog" :title="alertDialogInfo.title"
                :description="alertDialogInfo.description" @cancel="alertDialogInfo.cancel"
                @confirm="alertDialogInfo.confirm" />

        </div>

    </AppLayout>
</template>

<script setup lang="ts">
import DialogDetails from '@/components/developments/DialogDetails.vue';
import EstimationDialog from '@/components/developments/EstimationDialog.vue';
import KanbanColumn from '@/components/developments/KanbanColumn.vue';
import RegisterProgressDialog from '@/components/developments/RegisterProgressDialog.vue';
import UpsertDialog from '@/components/developments/UpsertDialog.vue';
import { Button } from '@/components/ui/button';
import { type DevelopmentRequest, DevelopmentRequestSection, DevelopmentRequestStatus } from '@/interfaces/developmentRequest.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    Plus
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import AlertDialog from '@/components/AlertDialog.vue';

import ApprovalDialog from '@/components/developments/ApprovalDialog.vue';
import { DevelopmentProgress } from '@/interfaces/developmentProgress.interface';
import AssignDevelopersDialog from '@/components/developments/AssignDevelopersDialog.vue';
import TIUsersProjectsModal from '@/components/developments/TIUsersProjectsModal.vue';
import { Users } from 'lucide-vue-next';

const { developmentsByStatus } = defineProps<{ developmentsByStatus: DevelopmentRequestSection }>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Desarrollos',
        href: '#',
    },
];

type AlertDialogInfo = {
    title: string;
    description: string;
    confirm: () => void;
    cancel: () => void;
}


const changeStatusInfo: AlertDialogInfo = {
    title: 'Cambiar Estado de Requerimiento',
    description: '¿Estás seguro de que deseas cambiar el estado de este requerimiento?',
    confirm: () => {
        if (updateStatus.value) {
            updateRequestStatus();
            updateStatus.value = null;
        };
    },
    cancel: () => {
        rollbackStatus();
        updateStatus.value = null;
    },
}

const deleteInfo: AlertDialogInfo = {
    title: 'Eliminar Requerimiento',
    description: '¿Estás seguro de que deseas eliminar este requerimiento? Esta acción no se puede deshacer.',
    confirm: () => { },
    cancel: () => { },
}

const alertDialogInfo = ref<AlertDialogInfo>({ ...changeStatusInfo });

const updateStatus = ref<{
    requestId: number;
    newStatus: DevelopmentRequestStatus;
} | null>(null);

const showDetailModal = ref(false);
const showNewRequirementModal = ref(false);
const showEstimateModal = ref(false);
const showTechnicalApprovalModal = ref(false);
const showStrategicApprovalModal = ref(false);
const showProgressModal = ref(false);
const showAssignDevelopersModal = ref(false);
const showAlertDialog = ref(false);

const originalDevelopmentsByStatus = ref<DevelopmentRequestSection>({ ...developmentsByStatus });

const selectedRequirement = ref<DevelopmentRequest | null>(null);

const registeredRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.REGISTERED] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.REGISTERED] = newValue;
    },
});

const analysisRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.IN_ANALYSIS] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.IN_ANALYSIS] = newValue;
    },
});

const approvedRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.APPROVED] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.APPROVED] = newValue;
    },
});

const inDevelopmentRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.IN_DEVELOPMENT] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.IN_DEVELOPMENT] = newValue;
    },
});

const inQARequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.IN_TESTING] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.IN_TESTING] = newValue;
    },
});

const inProductionRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.COMPLETED] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.COMPLETED] = newValue;
    },
});

const rejectedRequests = computed({
    get: () => developmentsByStatus[DevelopmentRequestStatus.REJECTED] || [],
    set: (newValue: DevelopmentRequest[]) => {
        developmentsByStatus[DevelopmentRequestStatus.REJECTED] = newValue;
    },
});


const updateRequestStatus = () => {
    if (!updateStatus.value) return;
    const { requestId, newStatus } = updateStatus.value;
    router.patch(`/developments/${requestId}/status`, {
        new_status: newStatus,
        devs_ids_in_order: developmentsByStatus[newStatus].map(dev => dev.id),
    }, {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                developmentsByStatus[newStatus] = developmentsByStatus[newStatus].map(dev => {
                    if (dev.id === requestId) {
                        if (flash.progress) {
                            dev.latest_progress = flash.progress as DevelopmentProgress;
                        }

                        if (flash.completed) {
                            dev.completed_at = (flash.completed as { completed_at: string }).completed_at;
                            dev.actual_hours = (flash.completed as { actual_hours: number }).actual_hours;
                        }
                        return { ...dev, status: newStatus };
                    }
                    return dev;
                });
                showAlertDialog.value = false;
                originalDevelopmentsByStatus.value = { ...developmentsByStatus };

            }
        },
        onError: () => {
            rollbackStatus();
        },
    });
}


const deleteDevelopmentRequest = (requestId: number, status: DevelopmentRequestStatus) => {
    router.delete(`/developments/${requestId}`, {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                developmentsByStatus[status] = developmentsByStatus[status].filter(dev => dev.id !== requestId);
                originalDevelopmentsByStatus.value = { ...developmentsByStatus };

            }
        },
    });
}

const handleComeBackToAnalysis = (item: DevelopmentRequest) => {
    router.post(`/developments/${item.id}/come-back-to-analysis`, {
        devs_ids_in_order: analysisRequests.value.map(dev => dev.id),
    }, {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                analysisRequests.value = analysisRequests.value.map(dev => {
                    if (dev.id === item.id) {
                        return { ...dev, status: DevelopmentRequestStatus.IN_ANALYSIS, approvals: [] };
                    }
                    return dev;
                });
                rejectedRequests.value = rejectedRequests.value.filter(dev => dev.id !== item.id);
                originalDevelopmentsByStatus.value = { ...developmentsByStatus };
            }

            if (flash.error) {
                rollbackStatus(DevelopmentRequestStatus.REJECTED);
            }
        },
    });
}


let onceMessageShown = ref(false);

const showMessage = (message: string) => {
    if (!onceMessageShown.value) {
        toast.error(message);
        onceMessageShown.value = true;
        setTimeout(() => {
            onceMessageShown.value = false;
        }, 3000);
    }
}

const rollbackStatus = (oldStatus?: DevelopmentRequestStatus) => {
    if (!updateStatus.value) return;
    const { newStatus } = updateStatus.value;
    if (newStatus === DevelopmentRequestStatus.IN_ANALYSIS) {
        if (oldStatus && oldStatus === DevelopmentRequestStatus.REJECTED) {
            rejectedRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.REJECTED] || [];
        } else {
            registeredRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.REGISTERED] || [];
        }
        analysisRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_ANALYSIS] || [];
        
    } else if (newStatus === DevelopmentRequestStatus.IN_DEVELOPMENT) {
        approvedRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.APPROVED] || [];
        inDevelopmentRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_DEVELOPMENT] || [];
    } else if (newStatus === DevelopmentRequestStatus.IN_TESTING) {
        inDevelopmentRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_DEVELOPMENT] || [];
        inQARequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_TESTING] || [];
    } else if (newStatus === DevelopmentRequestStatus.COMPLETED) {
        inQARequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_TESTING] || [];
        inProductionRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.COMPLETED] || [];
    }
}

</script>

<style scoped>
.kanban-board-scroll {
    scrollbar-width: thin;
    scrollbar-color: hsl(var(--muted-foreground)) transparent;
    scrollbar-gutter: stable;
    overscroll-behavior-x: contain;
}

.kanban-board-scroll::-webkit-scrollbar {
    height: 8px;
}

.kanban-board-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-board-scroll::-webkit-scrollbar-thumb {
    background-color: hsl(var(--muted-foreground));
    border-radius: 9999px;
    border: 2px solid transparent;
    background-clip: content-box;
}

.kanban-board-scroll:hover::-webkit-scrollbar-thumb {
    background-color: hsl(var(--foreground));
}
</style>
