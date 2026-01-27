<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-w-[min(100vw-1.5rem,900px)] sm:max-w-3xl p-0">
            <form id="development-form" @submit.prevent="handleSubmit(onSubmit)()">
                <DialogHeader class="border-b px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                <CodeXml class="size-6 text-primary" />
                            </div>
                            <div class="flex text-start flex-col gap-1 max-w-[72vw] sm:max-w-none">
                                <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                                    {{ currentDevelopment
                                        ? `Editar ${currentDevelopment?.name}`
                                        : 'Crear Nuevo Requerimiento' }}
                                </DialogTitle>
                                <p class="text-sm text-muted-foreground leading-snug">
                                    {{ currentDevelopment ? `Actualiza la información de ${currentDevelopment?.name}` :
                                        'Completa el formulario para crear un nuevo requerimiento de desarrollo.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </DialogHeader>

                <ScrollArea class="max-h-96 sm:max-h-[65vh]">
                    <div class="space-y-8 px-4 pb-5 sm:px-6 sm:pb-6">
                        <section class="space-y-4 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2">
                                    <FileText class="h-4 w-4 text-primary" />
                                    <div>
                                        <p class="text-sm font-medium">Información del requerimiento</p>
                                        <p class="text-xs text-muted-foreground">Título, descripción e impacto esperado.
                                        </p>
                                    </div>
                                </div>
                                <Badge variant="secondary" class="w-fit text-[11px] flex items-center gap-1 self-start">
                                    <Sparkles class="h-3.5 w-3.5" /> Requerido
                                </Badge>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <VeeField name="title" v-slot="{ componentField, errors }">

                                    <div class="space-y-2">
                                        <Label :for="componentField.name" class="flex items-center gap-2">
                                            <Type class="h-3.5 w-3.5 text-muted-foreground" /> Título *
                                        </Label>
                                        <Input placeholder="Ej: Módulo de reportes avanzados" v-bind="componentField" />
                                        <FieldError v-if="errors.length" :errors="errors" />
                                    </div>
                                </VeeField>

                                <VeeField name="priority" v-slot="{ componentField, errors }">
                                    <div class="space-y-2">
                                        <Label :for="componentField.name" class="flex items-center gap-2">
                                            <Flag class="h-3.5 w-3.5 text-muted-foreground" /> Prioridad *
                                        </Label>

                                        <SelectFilters label="Prioridades" :default-value="componentField.modelValue"
                                            :items="Object.values(developmentRequestPriorityOptions)" item-value="value"
                                            item-label="label" :show-refresh="false" :show-selected-focus="false"
                                            empty-text="No se encontraron prioridades" :selected-as-label="true"
                                            :full-width="true" @select="(select) => setFieldValue('priority', select)">
                                            <template #item="{ item }">
                                                <Badge :class="item.bg">
                                                    <component :is="item.icon" />
                                                    {{ item.label }}
                                                </Badge>
                                            </template>
                                        </SelectFilters>
                                        <FieldError v-if="errors.length" :errors="errors" />

                                    </div>
                                </VeeField>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4 space-y-2">
                                <div class="flex flex-col space-y-4">
                                    <VeeField name="area_id" v-slot="{ componentField, errors }">
                                        <div class="space-y-2">
                                            <Label :for="componentField.name" class="flex items-center gap-2">
                                                <MapPin class="h-3.5 w-3.5 text-muted-foreground" /> Área solicitante
                                            </Label>
                                            <SelectFilters label="Áreas" dataKey="areas" :items="areas"
                                                :default-value="componentField.modelValue" item-icon="descripcion_area"
                                                :show-refresh="false" item-label="descripcion_area" item-value="id_area"
                                                :show-selected-focus="false" empty-text="No se encontraron áreas"
                                                :selected-as-label="true" :full-width="true"
                                                @select="(select) => setFieldValue('area_id', select)" />

                                            <FieldError v-if="errors.length" :errors="errors" />
                                        </div>
                                    </VeeField>

                                    <VeeField name="description" v-slot="{ componentField, errors }">
                                        <div class="space-y-2">
                                            <Label :for="componentField.name" class="flex items-center gap-2">
                                                <AlignLeft class="h-3.5 w-3.5 text-muted-foreground" /> Descripción
                                                detallada
                                                *
                                            </Label>
                                            <Textarea rows="3"
                                                placeholder="Describe la funcionalidad requerida con el mayor detalle posible..."
                                                v-bind="componentField" />
                                            <FieldError v-if="errors.length" :errors="errors" />
                                        </div>
                                    </VeeField>


                                    <VeeField name="impact" v-slot="{ componentField, errors }">
                                        <div class="space-y-2">
                                            <Label :for="componentField.name" class="flex items-center gap-2">
                                                <TrendingUp class="h-3.5 w-3.5 text-muted-foreground" /> Impacto
                                                esperado
                                            </Label>
                                            <Textarea rows="2"
                                                placeholder="¿Qué beneficio traerá? ¿Qué problema resuelve?"
                                                v-bind="componentField" />
                                            <FieldError v-if="errors.length" :errors="errors" />
                                        </div>
                                    </VeeField>

                                </div>

                                <FileUpload @error="(msg) => toast.error(msg)"
                                    @update:file="(file) => setFieldValue('requirement_file', file)"
                                    accept="application/pdf" label="Adjuntar archivo relacionado (opcional)" />
                            </div>

                        </section>



                        <!-- <section
                        class="space-y-3 rounded-lg border border-dashed border-primary/30 bg-primary/5 p-3 sm:p-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2">
                                <Wrench class="h-4 w-4 text-primary" />
                                <div>
                                    <p class="text-sm font-semibold">Preparado para análisis técnico</p>
                                    <p class="text-xs text-muted-foreground">Campos reservados para el equipo técnico.
                                    </p>
                                </div>
                            </div>
                            <Badge class="bg-primary/15 text-primary flex items-center gap-1 w-fit self-start"
                                variant="secondary">
                                <Lock class="h-3.5 w-3.5" /> Solo lectura
                            </Badge>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2 opacity-60">
                                <Label for="hours" class="flex items-center gap-2">
                                    <Timer class="h-3.5 w-3.5 text-muted-foreground" /> Horas/Hombre estimadas
                                </Label>
                                <Input id="hours" v-model="form.hours" type="number" placeholder="0" disabled />
                            </div>
                            <div class="space-y-2 opacity-60">
                                <Label for="eta" class="flex items-center gap-2">
                                    <CalendarClock class="h-3.5 w-3.5 text-muted-foreground" /> Fecha estimada de
                                    culminación
                                </Label>
                                <Input id="eta" v-model="form.eta" type="date" disabled />
                            </div>
                        </div>
                    </section> -->
                    </div>
                </ScrollArea>

                <Separator />

                <DialogFooter class="px-4 py-4 sm:px-6">

                    <Button form="development-form" :disabled="Object.keys(errors).length > 0 || isLoading"
                        type="submit" class="gap-1 w-full sm:w-auto">
                        <Rocket class="h-4 w-4" />
                        Crear requerimiento
                    </Button>

                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { DevelopmentRequestPriority, developmentRequestPriorityOptions } from '@/interfaces/developmentRequest.interface';
import { toTypedSchema } from '@vee-validate/zod';
import { AlignLeft, CodeXml, FileText, Flag, MapPin, Rocket, Sparkles, TrendingUp, Type } from 'lucide-vue-next';
import { Field as VeeField } from 'vee-validate';
import { toast } from 'vue-sonner';
import FileUpload from '../FileUpload.vue';
import SelectFilters from '../SelectFilters.vue';

import FieldError from '@/components/ui/field/FieldError.vue';
import { type Area } from '@/interfaces/area.interface';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import z from 'zod';
// import { DevelopmentRequestPriority } from '@/interfaces/developmentRequest.interface';
import { useForm } from 'vee-validate';
import { useApp } from '@/composables/useApp';

const open = defineModel<boolean>('open');

const currentDevelopment = defineModel<any>('currentDevelopment');

const page = usePage();
const { isLoading } = useApp();

const areas = computed<Area[]>(() => {
    return (page.props.areas || []) as Area[];
});

const formSchema = toTypedSchema(
    z.object({
        title: z.string({
            message: 'El título es obligatorio',
        }).min(3, 'El título es obligatorio'),
        priority: z.nativeEnum(DevelopmentRequestPriority, {
            errorMap: () => ({ message: 'La prioridad es obligatoria' }),
        }),
        description: z.string().optional(),
        impact: z.string().optional(),
        area_id: z.number({
            message: 'El área solicitante es obligatoria',
        }),
        requirement_file: z
            .instanceof(File)
            .optional()
            .refine((file) => {
                if (!file) return true;
                return file.size <= 4 * 1024 * 1024;
            }, 'El archivo debe ser menor a 4MB'),

    })
);

const { setFieldValue, errors, handleSubmit, values } = useForm({
    validationSchema: formSchema,
    initialValues: {
        title: '',
        priority: undefined,
        description: '',
        impact: '',
        area_id: undefined,
        requirement_file: undefined,
    },
});


const onSubmit = (values: any) => {
    router.post('/developments', { ...values }, {
        onSuccess: () => {

            open.value = false;
        },

    });
    // Aquí puedes agregar la lógica para enviar los datos al servidor o procesarlos según sea necesario.
};

// <VeeField name="color" v-slot="{ componentField, errors }">
//                             <Field :data-invalid="!!errors.length">
//                                 <FieldLabel>Color</FieldLabel>
//                                 <Input id="color" placeholder="Ingresa un color (EJ: Rojo, Azul)"
//                                     v-bind="componentField" />
//                                 <FieldError v-if="errors.length" :errors="errors" />
//                             </Field>
//                         </VeeField>

</script>