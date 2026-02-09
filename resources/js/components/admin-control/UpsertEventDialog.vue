<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <CalendarClock class="h-5 w-5 text-primary" />
                    Nuevo evento
                </DialogTitle>
                <DialogDescription>
                    Registra un nuevo evento de infraestructura o mantenimiento.
                </DialogDescription>
            </DialogHeader>

            <form id="event-create-form" class="space-y-4" @submit.prevent="handleSubmit(onSubmit)()">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <VeeField name="title" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Titulo</FieldLabel>
                            <Input v-bind="componentField" placeholder="Titulo del evento" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="type" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Tipo</FieldLabel>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona un tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="type in typeOptions" :key="type" :value="type">
                                        {{ type }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="system" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Sistema</FieldLabel>
                            <Input v-bind="componentField" placeholder="Ej: Infraestructura Core" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="author" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Responsable</FieldLabel>
                            <Input v-bind="componentField" placeholder="Ej: Equipo TI" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="date" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Fecha</FieldLabel>
                            <Input v-bind="componentField" type="date" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="time" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Hora</FieldLabel>
                            <Input v-bind="componentField" type="time" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="status" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Estado</FieldLabel>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona un estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="status in statusOptions" :key="status" :value="status">
                                        {{ status }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="impact" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Impacto</FieldLabel>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona un impacto" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="impact in impactOptions" :key="impact" :value="impact">
                                        {{ impact }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                    <VeeField name="window" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Ventana</FieldLabel>
                            <Select v-bind="componentField">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecciona una ventana" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="window in windowOptions" :key="window" :value="window">
                                        {{ window }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>
                </div>

                <VeeField name="description" v-slot="{ componentField, errors }">
                    <Field :data-invalid="!!errors.length">
                        <FieldLabel>Descripcion</FieldLabel>
                        <Textarea v-bind="componentField" rows="3" placeholder="Detalle del evento" />
                        <FieldError v-if="errors.length" :errors="errors" />
                    </Field>
                </VeeField>
            </form>

            <DialogFooter>
                <Button variant="outline" @click="open = false">Cancelar</Button>
                <Button type="submit" form="event-create-form" :disabled="Object.keys(errors).length > 0">
                    Guardar evento
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { useForm, Field as VeeField } from 'vee-validate';
import { watch } from 'vue';
import * as z from 'zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { CalendarClock } from 'lucide-vue-next';

type EventFormValues = {
    title: string;
    description: string;
    type: string;
    system: string;
    author: string;
    date: string;
    time: string;
    status: string;
    impact: string;
    window: string;
};

const open = defineModel<boolean>('open', { default: false });

const props = defineProps<{
    typeOptions: string[];
    statusOptions: string[];
    impactOptions: string[];
    windowOptions: string[];
}>();

const emit = defineEmits<{
    (e: 'submit', values: EventFormValues): void;
}>();

const formSchema = toTypedSchema(
    z.object({
        title: z.string().min(1, 'El titulo es obligatorio.'),
        description: z.string().min(1, 'La descripcion es obligatoria.'),
        type: z.string().min(1, 'Selecciona un tipo.'),
        system: z.string().min(1, 'El sistema es obligatorio.'),
        author: z.string().min(1, 'El responsable es obligatorio.'),
        date: z.string().min(1, 'Selecciona una fecha.'),
        time: z.string().min(1, 'Selecciona una hora.'),
        status: z.string().min(1, 'Selecciona un estado.'),
        impact: z.string().min(1, 'Selecciona un impacto.'),
        window: z.string().min(1, 'Selecciona una ventana.'),
    })
);

const { handleSubmit, errors, resetForm } = useForm<EventFormValues>({
    validationSchema: formSchema,
    initialValues: {
        title: '',
        description: '',
        type: '',
        system: '',
        author: '',
        date: '',
        time: '',
        status: '',
        impact: '',
        window: '',
    },
});

function onSubmit(values: EventFormValues) {
    emit('submit', values);
}

watch(open, (value) => {
    if (!value) {
        resetForm();
    }
});
</script>
