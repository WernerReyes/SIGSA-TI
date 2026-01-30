<template>
    <div class="space-y-6">
        <!-- Tabs de Navegación -->
        <Tabs default-value="estimation" class="w-full">
            <TabsList class="grid w-full grid-cols-3">
                <TabsTrigger value="estimation" class="gap-2">
                    <ClipboardList class="h-4 w-4" />
                    <span class="hidden sm:inline">Estimación</span>
                </TabsTrigger>
                <TabsTrigger value="approval_technical" class="gap-2">
                    <CheckCircle2 class="h-4 w-4" />
                    <span class="hidden sm:inline">Aprobación Técnica</span>
                </TabsTrigger>
                <TabsTrigger value="approval_strategic" class="gap-2">
                    <Shield class="h-4 w-4" />
                    <span class="hidden sm:inline">Aprobación Estratégica</span>
                </TabsTrigger>
            </TabsList>

            <!-- TAB: ESTIMACIÓN Y ANÁLISIS -->
            <TabsContent value="estimation" class="space-y-6">
                <EstimationAnalysis
                    :requirement-id="requirementId"
                    :is-submitted="estimationSubmitted"
                    @submit="handleEstimationSubmit"
                />
            </TabsContent>

            <!-- TAB: APROBACIÓN TÉCNICA -->
            <TabsContent value="approval_technical" class="space-y-6">
                <TechnicalApproval
                    :requirement-id="requirementId"
                    :is-submitted="technicalApprovalSubmitted"
                    @submit="handleTechnicalApprovalSubmit"
                />
            </TabsContent>

            <!-- TAB: APROBACIÓN ESTRATÉGICA -->
            <TabsContent value="approval_strategic" class="space-y-6">
                <StrategicApproval
                    :requirement-id="requirementId"
                    :is-submitted="strategicApprovalSubmitted"
                    @submit="handleStrategicApprovalSubmit"
                />
            </TabsContent>
        </Tabs>

        <!-- Estado del Flujo -->
        <div class="rounded-lg border bg-card p-6 shadow-sm">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <Zap class="h-5 w-5 text-primary" />
                Estado del Flujo
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg bg-muted/50">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-yellow-500"></div>
                        <span class="text-sm font-medium">Estimación Técnica</span>
                    </div>
                    <Badge :variant="estimationSubmitted ? 'default' : 'outline'">
                        {{ estimationSubmitted ? 'Completado' : 'Pendiente' }}
                    </Badge>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-muted/50">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                        <span class="text-sm font-medium">Aprobación Técnica</span>
                    </div>
                    <Badge :variant="technicalApprovalSubmitted ? 'default' : 'outline'">
                        {{ technicalApprovalSubmitted ? 'Completado' : 'Pendiente' }}
                    </Badge>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-muted/50">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-purple-500"></div>
                        <span class="text-sm font-medium">Aprobación Estratégica</span>
                    </div>
                    <Badge :variant="strategicApprovalSubmitted ? 'default' : 'outline'">
                        {{ strategicApprovalSubmitted ? 'Completado' : 'Pendiente' }}
                    </Badge>
                </div>
            </div>
        </div>

        <!-- Botón para pasar a Control de Ejecución -->
        <div v-if="allApprovalsCompleted" class="flex gap-3 pt-4 border-t">
            <Button @click="proceedToExecution" class="gap-2">
                <PlayCircle class="h-4 w-4" />
                Proceder a Control de Ejecución
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ClipboardList, CheckCircle2, Shield, Zap, PlayCircle } from 'lucide-vue-next';
import EstimationAnalysis from './EstimationAnalysis.vue';
import TechnicalApproval from './TechnicalApprovalDialog.vue';
import StrategicApproval from './StrategicApproval.vue';

interface Props {
    requirementId?: number | string;
}

defineProps<Props>();

const emit = defineEmits<{
    proceedToExecution: [];
}>();

const estimationSubmitted = ref(false);
const technicalApprovalSubmitted = ref(false);
const strategicApprovalSubmitted = ref(false);

const allApprovalsCompleted = computed(
    () => estimationSubmitted.value && technicalApprovalSubmitted.value && strategicApprovalSubmitted.value
);

const handleEstimationSubmit = (data: any) => {
    console.log('Estimación guardada:', data);
    estimationSubmitted.value = true;
};

const handleTechnicalApprovalSubmit = (data: any) => {
    console.log('Aprobación técnica guardada:', data);
    technicalApprovalSubmitted.value = true;
};

const handleStrategicApprovalSubmit = (data: any) => {
    console.log('Aprobación estratégica guardada:', data);
    strategicApprovalSubmitted.value = true;
};

const proceedToExecution = () => {
    emit('proceedToExecution');
};
</script>
