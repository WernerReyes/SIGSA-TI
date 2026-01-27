<template>
    <div class="rounded-lg border bg-card p-6 shadow-sm">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                <ClipboardList class="h-4 w-4 text-blue-600" />
            </div>
            <h2 class="text-lg font-semibold">Análisis y Estimación Técnica</h2>
        </div>

        <Alert class="mb-6 bg-blue-50 border-blue-200">
            <AlertCircle class="h-4 w-4 text-blue-600" />
            <AlertTitle class="text-blue-900">Información Requerida</AlertTitle>
            <AlertDescription class="text-blue-800">
                Antes de proceder, el área técnica debe completar obligatoriamente la estimación de tiempos
                y recursos disponibles.
            </AlertDescription>
        </Alert>

        <form @submit.prevent="submitEstimation" class="space-y-6">
            <!-- Evaluación Inicial -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Evaluación de Viabilidad Técnica</h3>
                <div>
                    <Label for="technical_assessment">Comentarios de Evaluación Técnica</Label>
                    <Textarea
                        id="technical_assessment"
                        v-model="form.technical_assessment"
                        placeholder="Análisis técnico del requerimiento, dependencias, riesgos..."
                        rows="4"
                        :disabled="isSubmitting"
                    />
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <Label for="technical_feasibility">Viabilidad Técnica</Label>
                        <Select v-model="form.technical_feasibility" :disabled="isSubmitting">
                            <SelectTrigger id="technical_feasibility">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="feasible">Viable</SelectItem>
                                <SelectItem value="partially_feasible">Parcialmente Viable</SelectItem>
                                <SelectItem value="not_feasible">No Viable</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label for="risk_level">Nivel de Riesgo</Label>
                        <Select v-model="form.risk_level" :disabled="isSubmitting">
                            <SelectTrigger id="risk_level">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="low">Bajo</SelectItem>
                                <SelectItem value="medium">Medio</SelectItem>
                                <SelectItem value="high">Alto</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </div>

            <!-- Estimación de Tiempos (OBLIGATORIO) -->
            <div class="space-y-4 rounded-lg border-2 border-orange-200 bg-orange-50 p-4">
                <h3 class="font-medium text-sm text-orange-900 flex items-center gap-2">
                    <AlertCircle class="h-4 w-4" />
                    Estimación de Tiempos (OBLIGATORIO)
                </h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <Label for="estimated_end_date" class="text-orange-900 font-medium">
                            Fecha Estimada de Culminación *
                        </Label>
                        <Input
                            id="estimated_end_date"
                            v-model="form.estimated_end_date"
                            type="date"
                            :disabled="isSubmitting"
                            required
                            class="border-orange-300"
                        />
                        <p class="text-xs text-orange-700 mt-1">
                            Mínimo a {{ minimumDate }}
                        </p>
                    </div>
                    <div>
                        <Label for="man_hours" class="text-orange-900 font-medium">
                            Horas/Hombre Requeridas *
                        </Label>
                        <Input
                            id="man_hours"
                            v-model.number="form.man_hours"
                            type="number"
                            placeholder="Ej: 80"
                            :disabled="isSubmitting"
                            required
                            class="border-orange-300"
                        />
                        <p class="text-xs text-orange-700 mt-1">
                            Total estimado: {{ form.man_hours || 0 }} horas
                        </p>
                    </div>
                </div>
                <div>
                    <Label for="team_members" class="text-orange-900 font-medium">
                        Equipo Asignado *
                    </Label>
                    <Input
                        id="team_members"
                        v-model="form.team_members"
                        type="text"
                        placeholder="Nombres de los desarrolladores/especialistas"
                        :disabled="isSubmitting"
                        required
                    />
                </div>
            </div>

            <!-- Estimación de Recursos -->
            <div class="space-y-4 rounded-lg bg-muted/50 p-4">
                <h3 class="font-medium text-sm">Disponibilidad de Recursos</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <Label for="resource_availability">Disponibilidad de Recursos</Label>
                        <Select v-model="form.resource_availability" :disabled="isSubmitting">
                            <SelectTrigger id="resource_availability">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="available">Disponibles Inmediatamente</SelectItem>
                                <SelectItem value="partially_available">Parcialmente Disponibles</SelectItem>
                                <SelectItem value="not_available">No Disponibles</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label for="resource_notes">Observaciones de Recursos</Label>
                        <Input
                            id="resource_notes"
                            v-model="form.resource_notes"
                            type="text"
                            placeholder="Ej: A partir del mes próximo"
                            :disabled="isSubmitting"
                        />
                    </div>
                </div>
            </div>

            <!-- Tecnologías y Dependencias -->
            <div class="space-y-4">
                <h3 class="font-medium text-sm">Tecnologías y Dependencias</h3>
                <div>
                    <Label for="technologies">Tecnologías Involucradas</Label>
                    <Textarea
                        id="technologies"
                        v-model="form.technologies"
                        placeholder="Lenguajes, frameworks, librerías, etc."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
                <div>
                    <Label for="dependencies">Dependencias o Integraciones Externas</Label>
                    <Textarea
                        id="dependencies"
                        v-model="form.dependencies"
                        placeholder="Otros sistemas, APIs, bases de datos, etc."
                        rows="3"
                        :disabled="isSubmitting"
                    />
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-3 border-t pt-6">
                <Button type="submit" :disabled="isSubmitting" class="gap-2">
                    <Check class="h-4 w-4" />
                    {{ isSubmitting ? 'Guardando...' : 'Registrar Estimación' }}
                </Button>
                <Button type="button" variant="outline" @click="resetForm" :disabled="isSubmitting">
                    Limpiar
                </Button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
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
import { ClipboardList, AlertCircle, Check } from 'lucide-vue-next';

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
    technical_assessment: '',
    technical_feasibility: 'feasible',
    risk_level: 'medium',
    estimated_end_date: '',
    man_hours: null as number | null,
    team_members: '',
    resource_availability: 'available',
    resource_notes: '',
    technologies: '',
    dependencies: '',
});

const minimumDate = computed(() => {
    const date = new Date();
    date.setDate(date.getDate() + 5);
    return date.toISOString().split('T')[0];
});

const resetForm = () => {
    form.technical_assessment = '';
    form.technical_feasibility = 'feasible';
    form.risk_level = 'medium';
    form.estimated_end_date = '';
    form.man_hours = null;
    form.team_members = '';
    form.resource_availability = 'available';
    form.resource_notes = '';
    form.technologies = '';
    form.dependencies = '';
};

const submitEstimation = async () => {
    if (!form.estimated_end_date || !form.man_hours) {
        alert('La fecha estimada y las horas/hombre son obligatorias');
        return;
    }

    isSubmitting.value = true;
    try {
        console.log('Estimación a guardar:', form);
        // await router.post(`/developments/${props.requirementId}/estimation`, form);
        emit('submit', form);
    } catch (error) {
        console.error('Error al registrar estimación:', error);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
