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
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6 overflow-x-auto">
                <KanbanColumn 
                    v-model:dev-requests="registeredRequests" 
                    title="Registrados"
                    header-color="#64748b"
                />

                <KanbanColumn 
                    v-model:dev-requests="analysisRequests" 
                    title="En Análisis"
                    header-color="#3b82f6"
                />

                <KanbanColumn 
                    v-model:dev-requests="approvedRequests" 
                    title="Aprobados"
                    header-color="#6366f1"
                />

                <KanbanColumn 
                    v-model:dev-requests="inDevelopmentRequests" 
                    title="En Desarrollo"
                    header-color="#8b5cf6"
                />

                <KanbanColumn 
                    v-model:dev-requests="inQARequests" 
                    title="En QA"
                    header-color="#f59e0b"
                />

                <KanbanColumn 
                    v-model:dev-requests="inProductionRequests" 
                    title="En Producción"
                    header-color="#10b981"
                />
            </div>


            <!-- {{ developments }} -->

            <!-- Modal: Nuevo Requerimiento -->
            <UpsertDialog v-model:open="showNewRequirementModal" />
            <!-- <Dialog v-model:open="showNewRequirementModal">
                <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Crear Nuevo Requerimiento</DialogTitle>
                    </DialogHeader>
                    <RequirementForm @submitted="handleNewRequirement" />
                </DialogContent>
            </Dialog> -->
        </div>





    </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    FileText,
    Plus,
    ClipboardList,
    Clock,
    CheckCircle2,
    TestTube,
    Rocket,
    Edit3,
    Trash2,
} from 'lucide-vue-next';
import KanbanColumn from '@/components/developments/KanbanColumn.vue';
import RequirementForm from '@/components/developments/RequirementForm.vue';
import type { BreadcrumbItem } from '@/types';
import UpsertDialog from '@/components/developments/UpsertDialog.vue';
import { type DevelopmentRequest, DevelopmentRequestStatus } from '@/interfaces/developmentRequest.interface';

import { VueDraggableNext as draggable } from 'vue-draggable-next'
const { developments } = defineProps<{ developments: DevelopmentRequest[] }>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Desarrollos',
        href: '#',
    },
];

const showDetailModal = ref(false);
const showNewRequirementModal = ref(false);
const selectedRequirement = ref<DevelopmentRequest | null>(null);


const registeredRequests = ref<DevelopmentRequest[]>([]);
const analysisRequests = ref<DevelopmentRequest[]>([]);
const approvedRequests = ref<DevelopmentRequest[]>([]);
const inDevelopmentRequests = ref<DevelopmentRequest[]>([]);
const inQARequests = ref<DevelopmentRequest[]>([]);
const inProductionRequests = ref<DevelopmentRequest[]>([]);


onMounted(() => {
    // Aquí puedes filtrar las solicitudes de desarrollo según su estado
    registeredRequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.REGISTERED
    );
    analysisRequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.IN_ANALYSIS
    );
    approvedRequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.APPROVED
    );
    analysisRequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.IN_DEVELOPMENT
    );
    inQARequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.IN_TESTING
    );
    inProductionRequests.value = developments.filter(
        (req) => req.status === DevelopmentRequestStatus.COMPLETED
    );
});

</script>
