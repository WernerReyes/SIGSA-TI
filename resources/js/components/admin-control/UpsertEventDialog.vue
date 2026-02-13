<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) resetAndClose();
    }">
        <DialogContent class="sm:max-w-3xl overflow-hidden p-0">
            <DialogHeader class="space-y-3 pb-4 px-6 pt-6">
                <div class="flex items-start gap-3">
                    <div
                        class="size-11 rounded-xl bg-primary/15 ring-1 ring-primary/20 flex items-center justify-center">
                        <CalendarClock class="size-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle class="text-xl font-semibold">
                            {{
                                selectedEvent ? 'Editar evento' : 'Registrar nuevo evento'
                            }}
                        </DialogTitle>
                        <DialogDescription class="text-sm">
                            {{ selectedEvent ?
                                'Modifica los detalles del evento de infraestructura.'
                                : 'Registra un nuevo evento de infraestructura proporcionando los detalles necesarios.' }}
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>


            <ScrollArea class="dialog-content">
                <form id="event-create-form" class="space-y-5 px-6 pb-6"
                    @submit.prevent="handleSubmit(handleUpsertInfrastructureEvent)()">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <VeeField name="title" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Titulo</FieldLabel>
                                <InputGroup class="bg-background/70">
                                    <InputGroupAddon>
                                        <Type class="size-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-bind="componentField" placeholder="Titulo del evento" />
                                </InputGroup>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>

                        <VeeField name="type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Tipo</FieldLabel>
                                <InputGroup class="bg-background/70">
                                    <InputGroupAddon>
                                        <Tags class="size-4 mr-1" />
                                    </InputGroupAddon>
                                    <Select v-bind="componentField">
                                        <SelectTrigger
                                            class="w-full flex-1 border-0 bg-transparent px-0 shadow-none focus-visible:ring-0">
                                            <SelectValue placeholder="Selecciona un tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="type in infrastructureEventTypeOptionsArray"
                                                :key="type.value" :value="type.value">
                                                <Badge :class="type.bg">
                                                    <component :is="type.icon" />
                                                    {{ type.label }}
                                                </Badge>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </InputGroup>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>


                        <VeeField name="date" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Fecha</FieldLabel>
                                <InputGroup class="bg-background/70">
                                    <InputGroupAddon>
                                        <Calendar class="size-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-bind="componentField" type="date" />
                                </InputGroup>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>

                        <VeeField name="time" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Hora</FieldLabel>
                                <InputGroup class="bg-background/70">
                                    <InputGroupAddon>
                                        <Clock3 class="size-4" />
                                    </InputGroupAddon>


                                    <InputGroupInput v-bind="componentField" type="time"
                                        class="bg-background sm:w-4/12 appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none" />

                                    <InputGroupAddon align="inline-end">
                                        <RefreshCcw class="size-4 cursor-pointer"
                                            @click="setFieldValue('time', defaultTime())" />
                                    </InputGroupAddon>
                                </InputGroup>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </div>

                    <VeeField name="description" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel>Descripcion</FieldLabel>
                            <InputGroup class="bg-background/70">
                                <InputGroupAddon align="block-start">
                                    <AlignLeft class="size-4" />
                                </InputGroupAddon>
                                <InputGroupTextarea v-bind="componentField" rows="3" placeholder="Detalle del evento" />
                            </InputGroup>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>
                </form>
            </ScrollArea>

            <DialogFooter class="px-6 py-4 border-t bg-muted/30">
                <Button variant="outline" @click="open = false">Cancelar</Button>
                <Button class="gap-2" type="submit" form="event-create-form" :disabled="disabledForm">
                    <Sparkles class="size-4" />
                    Guardar evento
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupTextarea } from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Calendar, CalendarClock, Clock3, AlignLeft, Sparkles, Tags, Type, RefreshCcw } from 'lucide-vue-next';
import { ScrollArea } from '@/components/ui/scroll-area';
import * as z from 'zod';
import { type InfrastructureEvent, InfrastructureEventType, infrastructureEventTypeOptionsArray } from '@/interfaces/infrastructureEvent.interface';
import { ref } from 'vue';
import { router } from '@inertiajs/core';
import { Badge } from '../ui/badge';
import { toZonedDate, isEqual } from '@/lib/utils';
import { useApp } from '@/composables/useApp';



const selectedEvent = defineModel<InfrastructureEvent | null>('selected', { default: null });
const open = defineModel<boolean>('open', { default: false });

const { isLoading } = useApp();

const formSchema = toTypedSchema(
    z.object({
        title: z.string({
            message: 'El titulo es obligatorio.',
        }).min(1, 'El titulo es obligatorio.'),
        description: z.string().min(1, 'La descripcion es obligatoria.'),
        type: z.nativeEnum(InfrastructureEventType, {
            message: 'Selecciona un tipo de evento.',
        }),
        date: z.string({
            message: 'Selecciona una fecha válida.',
        }),
        time: z.string({
            message: 'Selecciona una hora válida.',
        }).regex(/^([0-1]\d|2[0-3]):([0-5]\d)$/, 'Selecciona una hora válida.'),

    })
);

const defaultDate = new Date().toISOString().slice(0, 10);
const defaultTime = () => {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
};

const initialValues = computed(() => ({
    title: selectedEvent.value?.title || '',
    description: selectedEvent.value?.description || '',
    type: (selectedEvent.value?.type || undefined) as InfrastructureEventType,
    date: selectedEvent.value ? new Date(selectedEvent.value.date).toISOString().slice(0, 10) : defaultDate,
    time: selectedEvent.value ? new Date(selectedEvent.value.date).toTimeString().slice(0, 5) : defaultTime(),
}));

const disabledForm = computed(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || isEqual(values, initialValues.value);
});

const { handleSubmit, errors, resetForm, setValues, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: initialValues.value,
    keepValuesOnUnmount: true,
});

type EventFormValues = typeof values;

watch(
    () => selectedEvent.value,
    () => {
        if (selectedEvent.value) {
            setValues(initialValues.value);
        } else {
            resetForm();
        }
    }
);

const resetAndClose = () => {
    resetForm();
    open.value = false;
    selectedEvent.value = null;
};


const handleUpsertInfrastructureEvent = (data: EventFormValues) => {
    const dateTime = new Date(`${data.date}T${data.time}:00`);

    if (selectedEvent.value) {
        router.put(`/admin-control/infrastructure-events/${selectedEvent.value.id}`, {
            ...data,
            date: toZonedDate(dateTime),
        }, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                resetAndClose();

                router.replaceProp('infrastructureEvents', (events: InfrastructureEvent[]) => {
                    const updatedEvent = flash.event as InfrastructureEvent;
                    return events.map(event => event.id === updatedEvent.id ? updatedEvent : event)
                        .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
                });
            }
        });
        return;
    }
    router.post('/admin-control/infrastructure-events', {
        ...data,
        date: dateTime
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            resetAndClose();

            router.replaceProp('infrastructureEvents', (events: InfrastructureEvent[]) => {
                const newEvent = flash.event as InfrastructureEvent;
                return [newEvent, ...events].sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
            });
        }
    });
};


</script>
