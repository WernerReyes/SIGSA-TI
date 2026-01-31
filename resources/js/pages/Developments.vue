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
                </div>
            </header>

            <!-- Métricas Resumen -->
            <!-- <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">Total</div>
                    <p class="text-3xl font-bold mt-2">{{ requirements.length }}</p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">En Análisis</div>
                    <p class="text-3xl font-bold mt-2 text-blue-600">
                        {{ getCountByStatus('analysis') }}
                    </p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">En QA</div>
                    <p class="text-3xl font-bold mt-2 text-amber-600">
                        {{ getCountByStatus('qa') }}
                    </p>
                </div>
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">En Producción</div>
                    <p class="text-3xl font-bold mt-2 text-green-600">
                        {{ getCountByStatus('production') }}
                    </p>
                </div>
            </div> -->

            <!-- Board Kanban -->
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-7 overflow-x-auto">
                <!-- // TODO: Check why it doesn't show the toast error message  -->
                <KanbanColumn v-model:dev-requests="registeredRequests" title="Registrados" header-color="#64748b"
                    v-model:developments-by-status="originalDevelopmentsByStatus" @deleted="(id: number) => {
                        alertDialogInfo = {
                            title: 'Eliminar Requerimiento',
                            description: '¿Estás seguro de que deseas eliminar este requerimiento? Esta acción no se puede deshacer.',
                            confirm: () => {
                                deleteDevelopmentRequest(id);
                            },
                            cancel: () => { },
                        };
                        showAlertDialog = true;
                    }" @moved="(id, newStatus) => {
                        updateStatus = { requestId: id, newStatus };
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
                    }" :status="DevelopmentRequestStatus.IN_ANALYSIS" @moved="(id, status) => {
                        updateStatus = { requestId: id, newStatus: status };
                        showAlertDialog = true;
                    }" />

                <KanbanColumn @error="() => {
                    console.log('test event received');
                    // toast.error(message);
                    // showMessage(message);

                }" v-model:dev-requests="approvedRequests" title="Aprobados" header-color="#6366f1"
                    :status="DevelopmentRequestStatus.APPROVED"
                    v-model:developments-by-status="originalDevelopmentsByStatus" />

                <KanbanColumn @error="(message) => {
                    console.log(message);
                    // toast.error(message);
                    showMessage(message);

                }" v-model:dev-requests="inDevelopmentRequests" title="En Desarrollo" header-color="#8b5cf6"
                    :status="DevelopmentRequestStatus.IN_DEVELOPMENT" />

                <KanbanColumn v-model:dev-requests="inQARequests" title="En QA" header-color="#f59e0b"
                    :status="DevelopmentRequestStatus.IN_TESTING" />

                <KanbanColumn v-model:dev-requests="inProductionRequests" title="En Producción" header-color="#10b981"
                    :status="DevelopmentRequestStatus.COMPLETED" />

                <KanbanColumn v-model:dev-requests="rejectedRequests" title="Rechazados" header-color="#ef4444"
                    :status="DevelopmentRequestStatus.REJECTED" />
            </div>

            <DialogDetails v-model:open="showDetailModal" v-model:current-development="selectedRequirement" />
            <UpsertDialog v-model:open="showNewRequirementModal" v-model:current-development="selectedRequirement" />
            <EstimationDialog v-model:open="showEstimateModal" v-model:current-development="selectedRequirement" />
            <TechnicalApprovalDialog v-if="showTechnicalApprovalModal" v-model:open="showTechnicalApprovalModal"
                v-model:current-development="selectedRequirement" />

            <AlertDialog v-model:open="showAlertDialog" :title="alertDialogInfo.title"
                :description="alertDialogInfo.description" @cancel="alertDialogInfo.cancel"
                @confirm="alertDialogInfo.confirm" />

        </div>


    </AppLayout>
</template>

<script setup lang="ts">
import EstimationDialog from '@/components/developments/EstimationDialog.vue';
import KanbanColumn from '@/components/developments/KanbanColumn.vue';
import UpsertDialog from '@/components/developments/UpsertDialog.vue';
import DialogDetails from '@/components/developments/DialogDetails.vue';
import TechnicalApprovalDialog from '@/components/developments/TechnicalApprovalDialog.vue';
import { Button } from '@/components/ui/button';
import { type DevelopmentRequest, DevelopmentRequestSection, DevelopmentRequestStatus } from '@/interfaces/developmentRequest.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    Plus
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
// import { AlertDialog } from '@/components/ui/alert-dialog';
import AlertDialog from '@/components/AlertDialog.vue';

const { developmentsByStatus } = defineProps<{ developmentsByStatus: DevelopmentRequestSection }>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Desarrollos',
        href: '#',
    },
];

const alertDialogInfo = ref<{
    title: string;
    description: string;
    confirm: () => void;
    cancel: () => void;
}>({
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
});

const updateStatus = ref<{
    requestId: number;
    newStatus: DevelopmentRequestStatus;
} | null>(null);

const showDetailModal = ref(false);
const showNewRequirementModal = ref(false);
const showEstimateModal = ref(false);
const showTechnicalApprovalModal = ref(false);
const showAlertDialog = ref(false);


// const developmentsByStatus<DevelopmentRequestSection>({});

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
    }, {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        onSuccess: () => {
            showAlertDialog.value = false;
        },
        onError: () => {
            rollbackStatus();
        },
    });
}

const deleteDevelopmentRequest = (requestId: number) => {
    router.delete(`/developments/${requestId}`, {
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        onSuccess: () => {
            registeredRequests.value = registeredRequests.value.filter(dev => dev.id !== requestId);

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

const rollbackStatus = () => {
    if (!updateStatus.value) return;
    const { newStatus } = updateStatus.value;
    if (newStatus === DevelopmentRequestStatus.IN_ANALYSIS) {
        registeredRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.REGISTERED] || [];
        analysisRequests.value = originalDevelopmentsByStatus.value[DevelopmentRequestStatus.IN_ANALYSIS] || [];
    }
}

</script>
