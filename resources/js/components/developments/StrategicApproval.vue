<template>
    <div class="rounded-lg border bg-card p-6 shadow-sm">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100">
                <Shield class="h-4 w-4 text-purple-600" />
            </div>
            <h2 class="text-lg font-semibold">Aprobación Estratégica</h2>
            <Badge variant="outline" class="ml-auto">Sub-Gerente de TI</Badge>
        </div>

        <Alert class="mb-6 bg-purple-50 border-purple-200">
            <Shield class="h-4 w-4 text-purple-600" />
            <AlertTitle class="text-purple-900">Segunda Validación - Fase Final</AlertTitle>
            <AlertDescription class="text-purple-800">
                Autoriza el inicio del proyecto alineado a las prioridades del negocio y objetivos estratégicos
                de la organización.
            </AlertDescription>
        </Alert>

        <form @submit.prevent="submitApproval" class="space-y-6">
            <!-- Revisión de Aprobaciones Previas -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Revisión de Aprobaciones Previas</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-3 rounded border">
                        <span class="text-sm font-medium">Aprobación Técnica (Gerente de TI)</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-green-500"></div>
                            <span class="text-xs font-medium text-green-600">Aprobado</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded border">
                        <span class="text-sm font-medium">Estimación Técnica</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-green-500"></div>
                            <span class="text-xs font-medium text-green-600">Completada</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alineación Estratégica -->
            <div class="space-y-4 rounded-lg border-2 border-purple-200 bg-purple-50 p-4">
                <h3 class="font-medium text-sm text-purple-900 flex items-center gap-2">
                    <Compass class="h-4 w-4" />
                    Alineación Estratégica del Requerimiento
                </h3>
                <div>
                    <Label for="business_alignment">¿Está alineado con los objetivos del negocio?</Label>
                    <Select v-model="form.business_alignment" :disabled="isSubmitting">
                        <SelectTrigger id="business_alignment">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="highly_aligned">Altamente Alineado</SelectItem>
                            <SelectItem value="aligned">Alineado</SelectItem>
                            <SelectItem value="partially_aligned">Parcialmente Alineado</SelectItem>
                            <SelectItem value="not_aligned">No Alineado</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="strategic_impact">Impacto Estratégico</Label>
                    <Select v-model="form.strategic_impact" :disabled="isSubmitting">
                        <SelectTrigger id="strategic_impact">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="critical">Crítico - Impacto Alto</SelectItem>
                            <SelectItem value="high">Alto Impacto</SelectItem>
                            <SelectItem value="medium">Impacto Medio</SelectItem>
                            <SelectItem value="low">Impacto Bajo</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="strategic_justification">Justificación Estratégica</Label>
                    <Textarea
                        id="strategic_justification"
                        v-model="form.strategic_justification"
                        placeholder="Explica cómo este proyecto contribuye a los objetivos estratégicos de la empresa..."
                        rows="4"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Evaluación de Prioridades -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Evaluación de Prioridades</h3>
                <div>
                    <Label for="priority_ranking">Clasificación de Prioridad Ejecutiva</Label>
                    <Select v-model="form.priority_ranking" :disabled="isSubmitting">
                        <SelectTrigger id="priority_ranking">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="critical">Crítica (Iniciar Inmediatamente)</SelectItem>
                            <SelectItem value="high">Alta (Siguientes 2 semanas)</SelectItem>
                            <SelectItem value="medium">Media (Próximo mes)</SelectItem>
                            <SelectItem value="low">Baja (Cuando sea posible)</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="priority_justification">Justificación de Prioridad</Label>
                    <Textarea
                        id="priority_justification"
                        v-model="form.priority_justification"
                        placeholder="Razones por las cuales se asigna esta prioridad..."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Impacto en Iniciativas Actuales -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Análisis de Impacto en Iniciativas Actuales</h3>
                <div>
                    <Label for="impact_on_initiatives">¿Impacta otras iniciativas en curso?</Label>
                    <Select v-model="form.impact_on_initiatives" :disabled="isSubmitting">
                        <SelectTrigger id="impact_on_initiatives">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="no_impact">Sin Impacto</SelectItem>
                            <SelectItem value="positive_impact">Impacto Positivo</SelectItem>
                            <SelectItem value="negative_impact">Impacto Negativo</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="affected_initiatives">Iniciativas Afectadas (si aplica)</Label>
                    <Textarea
                        id="affected_initiatives"
                        v-model="form.affected_initiatives"
                        placeholder="Nombres de iniciativas afectadas y descripción del impacto..."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Identificación del Aprobador Estratégico -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Firma Digital del Aprobador Estratégico</h3>
                <div>
                    <Label for="strategic_approver_name">Nombre del Sub-Gerente de TI</Label>
                    <Input
                        id="strategic_approver_name"
                        v-model="form.strategic_approver_name"
                        type="text"
                        placeholder="Nombre completo"
                        :disabled="isSubmitting"
                        required
                    />
                </div>
                <div>
                    <Label for="strategic_approver_email">Correo Electrónico</Label>
                    <Input
                        id="strategic_approver_email"
                        v-model="form.strategic_approver_email"
                        type="email"
                        placeholder="correo@empresa.com"
                        :disabled="isSubmitting"
                        required
                    />
                </div>
                <div>
                    <Label for="strategic_approver_notes">Observaciones del Aprobador</Label>
                    <Textarea
                        id="strategic_approver_notes"
                        v-model="form.strategic_approver_notes"
                        placeholder="Comentarios y recomendaciones..."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Condiciones de Inicio -->
            <div class="space-y-4 rounded-lg border-2 border-yellow-200 bg-yellow-50 p-4">
                <h3 class="font-medium text-sm text-yellow-900 flex items-center gap-2">
                    <AlertCircle class="h-4 w-4" />
                    Condiciones de Inicio del Proyecto
                </h3>
                <div>
                    <Label for="startup_conditions">Condiciones Especiales para el Inicio</Label>
                    <Textarea
                        id="startup_conditions"
                        v-model="form.startup_conditions"
                        placeholder="Ej: Obtener aprobación presupuestaria, validar integraciones, etc."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
                <div>
                    <Label for="budget_approval">¿Requiere Aprobación Presupuestaria?</Label>
                    <Select v-model="form.budget_approval" :disabled="isSubmitting">
                        <SelectTrigger id="budget_approval">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="not_required">No Requerida</SelectItem>
                            <SelectItem value="pending">Pendiente</SelectItem>
                            <SelectItem value="approved">Aprobada</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Decisión Final Estratégica -->
            <div class="space-y-4 rounded-lg border-2 border-green-200 bg-green-50 p-4">
                <h3 class="font-medium text-sm text-green-900 flex items-center gap-2">
                    <Check class="h-4 w-4" />
                    Decisión de Aprobación Estratégica
                </h3>
                <div class="flex gap-4">
                    <div class="flex items-center space-x-2">
                        <input
                            type="radio"
                            id="approve"
                            v-model="form.decision"
                            value="approved"
                            :disabled="isSubmitting"
                        />
                        <Label for="approve" class="cursor-pointer font-medium text-green-900">
                            ✓ Autorizar Inicio del Proyecto
                        </Label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input
                            type="radio"
                            id="reject"
                            v-model="form.decision"
                            value="rejected"
                            :disabled="isSubmitting"
                        />
                        <Label for="reject" class="cursor-pointer font-medium text-red-900">
                            ✗ No Autorizar
                        </Label>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-3 border-t pt-6">
                <Button
                    type="submit"
                    :disabled="isSubmitting"
                    class="gap-2"
                    :variant="form.decision === 'approved' ? 'default' : 'destructive'"
                >
                    <Shield class="h-4 w-4" />
                    {{ isSubmitting ? 'Procesando...' : 'Enviar Aprobación Estratégica' }}
                </Button>
                <Button type="button" variant="outline" @click="resetForm" :disabled="isSubmitting">
                    Limpiar
                </Button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Shield, Check, Compass, AlertCircle } from 'lucide-vue-next';

interface Props {
    requirementId?: number | string;
    isSubmitted?: boolean;
}

defineProps<Props>();

const emit = defineEmits<{
    submit: [data: any];
}>();

const isSubmitting = ref(false);

const form = reactive({
    business_alignment: 'aligned',
    strategic_impact: 'high',
    strategic_justification: '',
    priority_ranking: 'high',
    priority_justification: '',
    impact_on_initiatives: 'no_impact',
    affected_initiatives: '',
    strategic_approver_name: '',
    strategic_approver_email: '',
    strategic_approver_notes: '',
    startup_conditions: '',
    budget_approval: 'not_required',
    decision: 'approved',
});

const resetForm = () => {
    form.business_alignment = 'aligned';
    form.strategic_impact = 'high';
    form.strategic_justification = '';
    form.priority_ranking = 'high';
    form.priority_justification = '';
    form.impact_on_initiatives = 'no_impact';
    form.affected_initiatives = '';
    form.strategic_approver_name = '';
    form.strategic_approver_email = '';
    form.strategic_approver_notes = '';
    form.startup_conditions = '';
    form.budget_approval = 'not_required';
    form.decision = 'approved';
};

const submitApproval = async () => {
    if (!form.strategic_approver_name || !form.strategic_approver_email) {
        alert('El nombre y correo del aprobador estratégico son obligatorios');
        return;
    }

    isSubmitting.value = true;
    try {
        console.log('Aprobación estratégica a guardar:', form);
        // await router.post(`/developments/${props.requirementId}/strategic-approval`, form);
        emit('submit', form);
    } catch (error) {
        console.error('Error al registrar aprobación estratégica:', error);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
