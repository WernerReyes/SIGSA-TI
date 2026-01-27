<template>
    <div class="rounded-lg border bg-card p-6 shadow-sm">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                <Activity class="h-4 w-4 text-blue-600" />
            </div>
            <h2 class="text-lg font-semibold">Control de Ejecución</h2>
        </div>

        <Alert class="mb-6 bg-blue-50 border-blue-200">
            <Activity class="h-4 w-4 text-blue-600" />
            <AlertTitle class="text-blue-900">Monitoreo en Tiempo Real</AlertTitle>
            <AlertDescription class="text-blue-800">
                Seguimiento del avance real versus la fecha estimada, registro de pruebas QA y gestión del pase
                a producción.
            </AlertDescription>
        </Alert>

        <!-- Estados del Proyecto -->
        <div class="space-y-6">
            <!-- Tarjeta de Resumen -->
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Progreso
                    </div>
                    <div class="mt-2">
                        <div class="relative w-full bg-muted rounded-full h-2">
                            <div
                                class="bg-blue-500 h-2 rounded-full transition-all"
                                :style="{ width: progressPercentage + '%' }"
                            ></div>
                        </div>
                        <p class="text-sm font-semibold mt-2">{{ progressPercentage }}%</p>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Estado
                    </div>
                    <div class="mt-2">
                        <Badge class="mt-2" :variant="statusVariant">
                            {{ statusText }}
                        </Badge>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Días Restantes
                    </div>
                    <p class="text-2xl font-bold mt-2 text-primary">
                        {{ daysRemaining }}
                    </p>
                </div>

                <div class="rounded-lg border bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Horas Consumidas
                    </div>
                    <p class="text-2xl font-bold mt-2">
                        {{ hoursConsumed }} / {{ totalHours }}h
                    </p>
                </div>
            </div>

            <!-- Formulario de Actualización -->
            <div class="rounded-lg border bg-card p-6">
                <h3 class="font-semibold mb-4 flex items-center gap-2">
                    <Edit3 class="h-5 w-5 text-primary" />
                    Actualizar Avance
                </h3>

                <form @submit.prevent="submitUpdate" class="space-y-6">
                    <!-- Estado Actual -->
                    <div>
                        <Label for="current_status">Estado Actual del Proyecto</Label>
                        <Select v-model="form.current_status" :disabled="isSubmitting">
                            <SelectTrigger id="current_status">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="planning">Planificación</SelectItem>
                                <SelectItem value="in_development">En Desarrollo</SelectItem>
                                <SelectItem value="testing">En Pruebas QA</SelectItem>
                                <SelectItem value="ready_production">Listo para Producción</SelectItem>
                                <SelectItem value="in_production">En Producción</SelectItem>
                                <SelectItem value="completed">Completado</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Progreso -->
                    <div>
                        <Label for="progress">Porcentaje de Avance</Label>
                        <div class="flex gap-4 items-center">
                            <input
                                id="progress"
                                v-model.number="form.progress"
                                type="range"
                                min="0"
                                max="100"
                                :disabled="isSubmitting"
                                class="flex-1"
                            />
                            <span class="text-sm font-semibold min-w-12 text-right">
                                {{ form.progress }}%
                            </span>
                        </div>
                    </div>

                    <!-- Horas Consumidas -->
                    <div>
                        <Label for="hours_consumed">Horas/Hombre Consumidas hasta ahora</Label>
                        <Input
                            id="hours_consumed"
                            v-model.number="form.hours_consumed"
                            type="number"
                            placeholder="Ej: 40"
                            :disabled="isSubmitting"
                            min="0"
                        />
                    </div>

                    <!-- Descripción del Avance -->
                    <div>
                        <Label for="progress_description">Descripción del Avance</Label>
                        <Textarea
                            id="progress_description"
                            v-model="form.progress_description"
                            placeholder="Describe lo realizado en este período, tareas completadas, cambios, etc."
                            rows="4"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <!-- Obstáculos -->
                    <div>
                        <Label for="obstacles">Obstáculos o Riesgos Identificados</Label>
                        <Textarea
                            id="obstacles"
                            v-model="form.obstacles"
                            placeholder="Describe cualquier obstáculo o riesgo que pueda afectar el cronograma..."
                            rows="3"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <!-- Próximos Pasos -->
                    <div>
                        <Label for="next_steps">Próximos Pasos</Label>
                        <Textarea
                            id="next_steps"
                            v-model="form.next_steps"
                            placeholder="Actividades planeadas para el próximo período..."
                            rows="3"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <div class="flex gap-3 border-t pt-6">
                        <Button type="submit" :disabled="isSubmitting" class="gap-2">
                            <Save class="h-4 w-4" />
                            {{ isSubmitting ? 'Guardando...' : 'Guardar Actualización' }}
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Pruebas QA -->
            <div class="rounded-lg border bg-card p-6">
                <h3 class="font-semibold mb-4 flex items-center gap-2">
                    <TestTube class="h-5 w-5 text-primary" />
                    Registro de Pruebas QA
                </h3>

                <div class="mb-4">
                    <Button @click="showQAForm = !showQAForm" variant="outline" class="gap-2">
                        <Plus class="h-4 w-4" />
                        Registrar Nueva Prueba
                    </Button>
                </div>

                <form v-if="showQAForm" @submit.prevent="submitQATest" class="space-y-4 mb-6 p-4 bg-muted rounded-lg">
                    <div>
                        <Label for="qa_test_name">Nombre de la Prueba</Label>
                        <Input
                            id="qa_test_name"
                            v-model="qaForm.test_name"
                            type="text"
                            placeholder="Ej: Validación de inicio de sesión"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <div>
                        <Label for="qa_test_result">Resultado</Label>
                        <Select v-model="qaForm.test_result" :disabled="isSubmitting">
                            <SelectTrigger id="qa_test_result">
                                <SelectValue placeholder="Selecciona" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="passed">✓ Pasado</SelectItem>
                                <SelectItem value="failed">✗ Fallido</SelectItem>
                                <SelectItem value="pending">○ Pendiente</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label for="qa_test_comments">Comentarios</Label>
                        <Textarea
                            id="qa_test_comments"
                            v-model="qaForm.test_comments"
                            placeholder="Detalles de la prueba..."
                            rows="2"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <Button type="submit" size="sm" :disabled="isSubmitting">
                        Registrar Prueba
                    </Button>
                </form>

                <!-- Lista de Pruebas -->
                <div class="space-y-2">
                    <div
                        v-for="(test, index) in form.qa_tests"
                        :key="index"
                        class="p-3 rounded-lg border flex items-start justify-between hover:bg-muted/50"
                    >
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-2 w-2 rounded-full"
                                    :class="{
                                        'bg-green-500': test.test_result === 'passed',
                                        'bg-red-500': test.test_result === 'failed',
                                        'bg-yellow-500': test.test_result === 'pending',
                                    }"
                                ></div>
                                <p class="font-medium">{{ test.test_name }}</p>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ test.test_comments }}</p>
                        </div>
                        <Button
                            @click="removeQATest(index)"
                            variant="ghost"
                            size="sm"
                            :disabled="isSubmitting"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                    <div v-if="form.qa_tests.length === 0" class="text-center py-6 text-muted-foreground">
                        <p class="text-sm">No hay pruebas registradas aún</p>
                    </div>
                </div>
            </div>

            <!-- Gestión de Pase a Producción -->
            <div
                v-if="form.current_status === 'ready_production' || form.current_status === 'in_production' || form.current_status === 'completed'"
                class="rounded-lg border bg-green-50 p-6"
            >
                <h3 class="font-semibold mb-4 flex items-center gap-2 text-green-900">
                    <Rocket class="h-5 w-5" />
                    Gestión de Pase a Producción
                </h3>

                <form @submit.prevent="submitProductionRelease" class="space-y-4">
                    <div>
                        <Label for="production_date">Fecha de Pase a Producción</Label>
                        <Input
                            id="production_date"
                            v-model="form.production_date"
                            type="date"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <div>
                        <Label for="production_notes">Notas de Pase a Producción</Label>
                        <Textarea
                            id="production_notes"
                            v-model="form.production_notes"
                            placeholder="Instrucciones, cambios en configuración, migración de datos, etc."
                            rows="3"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <div>
                        <Label for="rollback_plan">Plan de Reversión (Rollback)</Label>
                        <Textarea
                            id="rollback_plan"
                            v-model="form.rollback_plan"
                            placeholder="Pasos para revertir el cambio en caso de emergencia..."
                            rows="3"
                            :disabled="isSubmitting"
                        />
                    </div>
                    <div class="flex items-center space-x-2">
                        <input
                            id="production_approved"
                            v-model="form.production_approved"
                            type="checkbox"
                            :disabled="isSubmitting"
                        />
                        <Label for="production_approved" class="cursor-pointer">
                            Confirmar que el pase a producción ha sido validado
                        </Label>
                    </div>
                    <Button type="submit" :disabled="isSubmitting || !form.production_approved" class="gap-2 bg-green-600 hover:bg-green-700">
                        <Rocket class="h-4 w-4" />
                        {{ isSubmitting ? 'Procesando...' : 'Registrar Pase a Producción' }}
                    </Button>
                </form>
            </div>
        </div>
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
import { Badge } from '@/components/ui/badge';
import { Activity, Edit3, TestTube, Plus, X, Rocket, Save } from 'lucide-vue-next';

interface Props {
    requirementId?: number | string;
    estimatedEndDate?: string;
    totalHours?: number;
}

const props = withDefaults(defineProps<Props>(), {
    estimatedEndDate: new Date().toISOString().split('T')[0],
    totalHours: 80,
});

const isSubmitting = ref(false);
const showQAForm = ref(false);

const form = reactive({
    current_status: 'in_development',
    progress: 25,
    hours_consumed: 0,
    progress_description: '',
    obstacles: '',
    next_steps: '',
    qa_tests: [] as Array<{ test_name: string; test_result: string; test_comments: string }>,
    production_date: '',
    production_notes: '',
    rollback_plan: '',
    production_approved: false,
});

const qaForm = reactive({
    test_name: '',
    test_result: 'pending',
    test_comments: '',
});

const hoursConsumed = computed(() => form.hours_consumed);
const progressPercentage = computed(() => form.progress);
const totalHours = props.totalHours;

const daysRemaining = computed(() => {
    const today = new Date();
    const endDate = new Date(props.estimatedEndDate);
    const timeDiff = endDate.getTime() - today.getTime();
    return Math.ceil(timeDiff / (1000 * 3600 * 24));
});

const statusText = computed(() => {
    const statuses: Record<string, string> = {
        planning: 'Planificación',
        in_development: 'En Desarrollo',
        testing: 'En Pruebas QA',
        ready_production: 'Listo para Producción',
        in_production: 'En Producción',
        completed: 'Completado',
    };
    return statuses[form.current_status] || 'Desconocido';
});

const statusVariant = computed(() => {
    if (form.current_status === 'completed') return 'default';
    if (form.current_status === 'in_production') return 'outline';
    if (form.current_status === 'testing') return 'secondary';
    return 'outline';
});

const submitUpdate = async () => {
    isSubmitting.value = true;
    try {
        console.log('Actualización de ejecución:', form);
        // await router.post(`/developments/${props.requirementId}/execution-update`, form);
    } catch (error) {
        console.error('Error al actualizar ejecución:', error);
    } finally {
        isSubmitting.value = false;
    }
};

const submitQATest = () => {
    if (!qaForm.test_name) {
        alert('El nombre de la prueba es requerido');
        return;
    }
    form.qa_tests.push({ ...qaForm });
    qaForm.test_name = '';
    qaForm.test_result = 'pending';
    qaForm.test_comments = '';
    showQAForm.value = false;
};

const removeQATest = (index: number) => {
    form.qa_tests.splice(index, 1);
};

const submitProductionRelease = async () => {
    if (!form.production_date) {
        alert('La fecha de pase a producción es requerida');
        return;
    }
    isSubmitting.value = true;
    try {
        console.log('Pase a producción:', form);
        // await router.post(`/developments/${props.requirementId}/production-release`, form);
    } catch (error) {
        console.error('Error al registrar pase a producción:', error);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
