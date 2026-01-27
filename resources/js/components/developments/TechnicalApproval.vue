<template>
    <div class="rounded-lg border bg-card p-6 shadow-sm">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                <CheckCircle2 class="h-4 w-4 text-green-600" />
            </div>
            <h2 class="text-lg font-semibold">Aprobación Técnica</h2>
            <Badge variant="outline" class="ml-auto">Gerente de TI</Badge>
        </div>

        <Alert class="mb-6 bg-green-50 border-green-200">
            <CheckCircle2 class="h-4 w-4 text-green-600" />
            <AlertTitle class="text-green-900">Primera Validación</AlertTitle>
            <AlertDescription class="text-green-800">
                Valida la disponibilidad de recursos y la corrección de la estimación de tiempos antes de autorizar
                el inicio del proyecto.
            </AlertDescription>
        </Alert>

        <form @submit.prevent="submitApproval" class="space-y-6">
            <!-- Información a Revisar -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Información de la Estimación</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded bg-white p-3 border">
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">
                            Fecha Estimada de Culminación
                        </p>
                        <p class="text-sm font-medium mt-1">{{ estimatedEndDate || 'No registrada' }}</p>
                    </div>
                    <div class="rounded bg-white p-3 border">
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">
                            Horas/Hombre Estimadas
                        </p>
                        <p class="text-sm font-medium mt-1">{{ manHours || 'No registradas' }} horas</p>
                    </div>
                    <div class="md:col-span-2 rounded bg-white p-3 border">
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">
                            Equipo Asignado
                        </p>
                        <p class="text-sm font-medium mt-1">{{ teamMembers || 'No registrado' }}</p>
                    </div>
                </div>
            </div>

            <!-- Validación de Recursos -->
            <div class="space-y-4 rounded-lg border-2 border-green-200 bg-green-50 p-4">
                <h3 class="font-medium text-sm text-green-900 flex items-center gap-2">
                    <Check class="h-4 w-4" />
                    Validación de Disponibilidad de Recursos
                </h3>
                <div class="space-y-3">
                    <div>
                        <Label for="resource_validation">¿Están disponibles los recursos estimados?</Label>
                        <Select v-model="form.resource_validation" :disabled="isSubmitting">
                            <SelectTrigger id="resource_validation">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="approved">Sí, Recursos Disponibles</SelectItem>
                                <SelectItem value="conditional">Condicional (con observaciones)</SelectItem>
                                <SelectItem value="rejected">No, Recursos No Disponibles</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label for="resource_comments">Comentarios sobre Disponibilidad</Label>
                        <Textarea
                            id="resource_comments"
                            v-model="form.resource_comments"
                            placeholder="Observaciones sobre la disponibilidad de recursos..."
                            rows="3"
                            :disabled="isSubmitting"
                        />
                    </div>
                </div>
            </div>

            <!-- Validación de Estimación -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Validación de Estimación</h3>
                <div>
                    <Label for="estimation_validation">¿Es correcta la estimación de tiempos?</Label>
                    <Select v-model="form.estimation_validation" :disabled="isSubmitting">
                        <SelectTrigger id="estimation_validation">
                            <SelectValue placeholder="Selecciona" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="accurate">Precisa y Realista</SelectItem>
                            <SelectItem value="requires_adjustment">Requiere Ajuste</SelectItem>
                            <SelectItem value="unrealistic">No Realista</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="estimation_comments">Comentarios sobre la Estimación</Label>
                    <Textarea
                        id="estimation_comments"
                        v-model="form.estimation_comments"
                        placeholder="Si requiere ajuste, especifica el nuevo estimado..."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
                <div class="grid gap-4 md:grid-cols-2" v-if="form.estimation_validation === 'requires_adjustment'">
                    <div>
                        <Label for="adjusted_hours">Horas/Hombre Ajustadas (Opcional)</Label>
                        <Input
                            id="adjusted_hours"
                            v-model.number="form.adjusted_hours"
                            type="number"
                            placeholder="Ej: 100"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <div>
                        <Label for="adjusted_date">Fecha Ajustada (Opcional)</Label>
                        <Input
                            id="adjusted_date"
                            v-model="form.adjusted_date"
                            type="date"
                            :disabled="isSubmitting"
                        />
                    </div>
                </div>
            </div>

            <!-- Identificación del Aprobador -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Firma Digital del Aprobador</h3>
                <div>
                    <Label for="approver_name">Nombre del Gerente de TI</Label>
                    <Input
                        id="approver_name"
                        v-model="form.approver_name"
                        type="text"
                        placeholder="Nombre completo"
                        :disabled="isSubmitting"
                        required
                    />
                </div>
                <div>
                    <Label for="approver_email">Correo Electrónico</Label>
                    <Input
                        id="approver_email"
                        v-model="form.approver_email"
                        type="email"
                        placeholder="correo@empresa.com"
                        :disabled="isSubmitting"
                        required
                    />
                </div>
                <div>
                    <Label for="approver_notes">Observaciones Generales</Label>
                    <Textarea
                        id="approver_notes"
                        v-model="form.approver_notes"
                        placeholder="Cualquier comentario adicional..."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Decisión Final -->
            <div class="space-y-4 rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                <h3 class="font-medium text-sm text-blue-900">Decisión de Aprobación</h3>
                <div class="flex gap-4">
                    <div class="flex items-center space-x-2">
                        <input
                            type="radio"
                            id="approve"
                            v-model="form.decision"
                            value="approved"
                            :disabled="isSubmitting"
                        />
                        <Label for="approve" class="cursor-pointer">
                            ✓ Aprobado
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
                        <Label for="reject" class="cursor-pointer">
                            ✗ Rechazado
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
                    <Check class="h-4 w-4" />
                    {{ isSubmitting ? 'Procesando...' : 'Enviar Aprobación' }}
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
import { CheckCircle2, Check } from 'lucide-vue-next';

interface Props {
    requirementId?: number | string;
    isSubmitted?: boolean;
    estimatedEndDate?: string;
    manHours?: number;
    teamMembers?: string;
}

const props = withDefaults(defineProps<Props>(), {
    estimatedEndDate: '',
    manHours: 0,
    teamMembers: '',
});

const emit = defineEmits<{
    submit: [data: any];
}>();

const isSubmitting = ref(false);

const form = reactive({
    resource_validation: 'approved',
    resource_comments: '',
    estimation_validation: 'accurate',
    estimation_comments: '',
    adjusted_hours: null as number | null,
    adjusted_date: '',
    approver_name: '',
    approver_email: '',
    approver_notes: '',
    decision: 'approved',
});

const resetForm = () => {
    form.resource_validation = 'approved';
    form.resource_comments = '';
    form.estimation_validation = 'accurate';
    form.estimation_comments = '';
    form.adjusted_hours = null;
    form.adjusted_date = '';
    form.approver_name = '';
    form.approver_email = '';
    form.approver_notes = '';
    form.decision = 'approved';
};

const submitApproval = async () => {
    if (!form.approver_name || !form.approver_email) {
        alert('El nombre y correo del aprobador son obligatorios');
        return;
    }

    isSubmitting.value = true;
    try {
        console.log('Aprobación técnica a guardar:', form);
        // await router.post(`/developments/${props.requirementId}/technical-approval`, form);
        emit('submit', form);
    } catch (error) {
        console.error('Error al registrar aprobación técnica:', error);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
