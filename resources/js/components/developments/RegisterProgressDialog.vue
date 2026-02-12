<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            resetForm();
        }
    }">
        <DialogContent class="sm:max-w-2xl p-0">
            <DialogHeader class="border-b px-4 py-4 sm:px-6">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-blue-500/10 flex items-center justify-center ring-2 ring-blue-500/10">
                        <Activity class="size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                            {{ hasCompleted ? 'Historial de Progreso' : 'Registrar Progreso' }}
                        </DialogTitle>
                        <DialogDescription>

                            {{ hasCompleted ?
                             'El desarrollo ha sido completado. A continuación se muestra el historial de progreso registrado.' 
                             : 'Registra el progreso actual del desarrollo y añade notas relevantes sobre los avances realizados.' }}

                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>


            <ProgressHistory v-if="hasCompleted" :currDev="currDev" v-model:loaded-history-for="loadedHistoryFor" />
            <Tabs v-else default-value="register" class="w-full sm:px-6">
                <TabsList class="grid w-full grid-cols-2 ">
                    <TabsTrigger value="register" class="gap-2">
                        <PlusCircle class="h-4 w-4" />
                        Registrar
                    </TabsTrigger>
                    <TabsTrigger value="history" class="gap-2">
                        <History class="h-4 w-4" />
                        Historial
                    </TabsTrigger>
                </TabsList>

                <!-- Tab: Registrar Progreso -->
                <TabsContent value="register" class="overflow-hidden">
                    <ScrollArea class="dialog-content">
                        <div class="space-y-5 px-4 py-4 sm:px-2">
                            <!-- Información del Desarrollo -->
                            <section class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="p-4 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-mono text-xs text-muted-foreground">DEV-{{ currDev?.id }}
                                            </p>
                                            <p class="font-medium mt-1">{{ currDev?.title }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{{ currDev?.description }}</p>
                                </div>
                            </section>

                            <!-- Último Progreso Registrado -->
                            <section v-if="latestProgress"
                                class="rounded-lg border-2 border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-950/30 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <TrendingUp class="h-4 w-4 text-primary" />
                                    <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Último
                                        Progreso
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="size-16 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-primary">{{ latestProgress.percentage
                                            }}%</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <CalendarIcon class="h-3 w-3 text-muted-foreground" />
                                            <p class="text-xs text-muted-foreground">
                                                {{ format(new Date(latestProgress.created_at), 'dd MMM yyyy HH:mm',
                                                    { locale: es }) }}
                                            </p>
                                        </div>
                                        <Progress :model-value="latestProgress.percentage" class="h-2" />
                                        <p v-if="latestProgress.notes" class="text-xs text-foreground/70 mt-2">
                                            {{ latestProgress.notes }}
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <!-- Formulario de Progreso -->
                            <form class="space-y-4" id="progress-form" @submit.prevent="handleSubmit(onSubmit)()">
                                <div class="space-y-2">
                                    <Label for="percentage" class="flex items-center gap-2">
                                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                                        Porcentaje de Avance (%)
                                    </Label>
                                    <div class="flex flex-col items-center gap-3">

                                        <VeeField name="percentage" v-slot="{ componentField, errors }">
                                            <div class="flex items-center w-full gap-4">

                                                <Slider :default-value="[componentField.modelValue]"
                                                    @update:model-value="(val) => {
                                                        componentField.onChange(val?.[0] || 0);
                                                    }" :min="0" :max="100" :step="5" class="flex-1"
                                                    :disabled="isLoading" />
                                                <span class="text-sm font-semibold min-w-12 text-right">
                                                    {{ values.percentage }}%
                                                </span>
                                            </div>
                                            <FieldError :errors="errors" />
                                        </VeeField>

                                    </div>

                                    <Progress :model-value="values.percentage" class="h-2" />


                                </div>

                                <div class="space-y-2">
                                    <Label for="notes" class="flex items-center gap-2">
                                        <FileText class="h-4 w-4 text-muted-foreground" />
                                        Notas del Progreso
                                    </Label>
                                    <VeeField name="notes" v-slot="{ componentField, errors }">
                                        <Textarea id="notes" v-bind="componentField"
                                            placeholder="Describe los avances realizados, obstáculos encontrados, próximos pasos..."
                                            rows="5" :disabled="isLoading" />
                                        <FieldError :errors="errors" />
                                    </VeeField>
                                    <!-- <Textarea id="notes" v-model="form.notes"
                                        placeholder="Describe los avances realizados, obstáculos encontrados, próximos pasos..."
                                        rows="5" :disabled="isLoading" /> -->
                                    <p class="text-xs text-muted-foreground">
                                        Detalla el trabajo realizado en este período
                                    </p>
                                </div>
                            </form>
                        </div>
                    </ScrollArea>

                    <DialogFooter class="border-t px-4 py-3 sm:px-6">
                        <Button variant="outline" @click="open = false" :disabled="isLoading">
                            Cancelar
                        </Button>
                        <Button type="submit" form="progress-form"
                            :disabled="isLoading || Object.keys(errors).length > 0" class="gap-2">
                            <Save class="h-4 w-4" />
                            Guardar Progreso
                        </Button>
                    </DialogFooter>
                </TabsContent>

                <!-- Tab: Historial de Progreso -->
                <TabsContent value="history" class="overflow-hidden">
                    <ProgressHistory :currDev="currDev" v-model:loadedHistoryFor="loadedHistoryFor" />
                </TabsContent>


            </Tabs>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import FieldError from '@/components/ui/field/FieldError.vue';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { ScrollArea } from '@/components/ui/scroll-area';
import Slider from '@/components/ui/slider/Slider.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import type { DevelopmentProgress } from '@/interfaces/developmentProgress.interface';
import type { DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import {
    Activity,
    CalendarIcon,
    FileText,
    History,
    PlusCircle,
    Save,
    TrendingUp
} from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import z from 'zod';
import ProgressHistory from './ProgressHistory.vue';
import { DevelopmentRequestStatus } from '@/interfaces/developmentRequest.interface';



const open = defineModel<boolean>('open');
const currDev = defineModel<DevelopmentRequest | null>('currentDevelopment');

const { isLoading } = useApp();

const loadedHistoryFor = ref<number | null>(null)
const historyVisible = ref(false);

const latestProgress = computed({
    get: () => {
        const lastest = currDev.value?.latest_progress || null;
        if (lastest) {
            setFieldValue('percentage', lastest.percentage);
        };
        return lastest;

    }, set: (val: DevelopmentProgress) => {
        currDev.value!.latest_progress = val;

    }
});

const hasCompleted = computed(() => {
    return currDev.value?.status === DevelopmentRequestStatus.COMPLETED;
});



const initialValues = {
    percentage: 0,
    notes: '',
};

const formSchema = toTypedSchema(z.object({
    percentage: z.number().min(0).max(100).refine((val) => {
        return val !== latestProgress.value?.percentage;
    }, {
        message: 'No se puede registrar el mismo porcentaje que el último',
    }),
    notes: z.string({
        message: 'Las notas son obligatorias',
    }).min(10, {
        message: 'Las notas deben tener al menos 10 caracteres',
    }).max(1000, {
        message: 'Las notas no pueden exceder los 1000 caracteres',
    }),
}));


const { setFieldValue, values, handleReset, errors, handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues,
    validateOnMount: false,
});



const resetForm = () => {
    handleReset();

    currDev.value = null;
    historyVisible.value = false;
    loadedHistoryFor.value = null;
};

const onSubmit = (vals: (typeof values)): void => {
    if (!currDev.value) return;


    router.post(
        `/developments/${currDev.value.id}/progress`,
        {
            percentage: vals.percentage,
            notes: vals.notes,
        },
        {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.success) {
                    resetForm();
                    open.value = false;
                    const progress = flash.progress as DevelopmentProgress || null;
                    if (progress) {
                        latestProgress.value = progress;
                    }

                }

            }

        }
    );
};
</script>
