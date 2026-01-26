<template>


    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) handleReset()
    }">
        <DialogTrigger as-child v-if="includeButton">
            <Button class="w-fit ml-auto shadow-md hover:shadow-lg transition-all gap-2">
                <Tag class="size-4" />

                Nuevo Ticket
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-5/12 overflow-y-auto">
            <DialogHeader class="space-y-3 pb-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-primary/10">
                        <Tag class="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle class="text-xl">{{
                            ticket ? 'Editar Ticket' : 'Crear Nuevo Ticket'
                            }}</DialogTitle>
                        <p class="text-sm text-muted-foreground mt-1">Completa los detalles para crear un ticket de
                            soporte</p>
                    </div>
                </div>
            </DialogHeader>


            <form id="dialogForm" @submit.prevent="handleSubmit(onSubmit)()"
                class="space-y-5 py-4 max-h-96 overflow-y-auto">
                <!-- TÍTULO -->

                <div class="flex max-sm:flex-col gap-5">
                    <FieldGroup>
                        <VeeField name="title" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="title" class="text-sm font-semibold">
                                    Título
                                </FieldLabel>
                                <Input id="title" placeholder="Ej: No puedo acceder al sistema de nómina"
                                    class="h-11 mt-2" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- DESCRIPCIÓN -->
                    <FieldGroup>
                        <VeeField name="description" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="description" class="text-sm font-semibold">
                                    Descripción
                                </FieldLabel>
                                <Textarea id="description" rows="4"
                                    placeholder="Describe el problema en detalle: ¿Qué ocurrió? ¿Cuándo empezó? ¿Qué intentaste hacer?"
                                    class="mt-2 resize-none" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </div>

                <FieldGroup>
                    <VeeField name="type" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel class="text-sm font-semibold mb-3">
                                Tipo de Ticket
                            </FieldLabel>
                            <Tabs v-bind="componentField" :default-value="TicketType.SERVICE_REQUEST" class="w-full">
                                <TabsList class="grid w-full grid-cols-2">
                                    <TabsTrigger v-for="type in Object.values(ticketTypeOptions)" :key="type.value"
                                        :value="type.value">
                                        <component :is="type.icon" />
                                        {{ type.label }}
                                    </TabsTrigger>
                                </TabsList>
                            </Tabs>
                        </Field>
                    </VeeField>
                </FieldGroup>


                <div class="grid gap-5"
                    :class="values.type === TicketType.SERVICE_REQUEST ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                    <!-- CATEGORÍA -->
                    <FieldGroup v-if="values.type === TicketType.SERVICE_REQUEST">
                        <VeeField name="request_type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel class="text-sm font-semibold">
                                    Categoría
                                </FieldLabel>


                                <SelectFilters :items="Object.values(ticketRequestTypeOptions)"
                                    :show-selected-focus="false" :show-refresh="false"
                                    label="Selecciona" item-label="label" item-value="value"
                                    selected-as-label :default-value="componentField.modelValue"
                                    @select="(value) => setFieldValue('request_type', value)"
                                    filter-placeholder="Buscar estado..." empty-text="No se encontraron estados">
                                    <template #item="{ item }">
                                        <Badge :class="item.bg" class="px-3 py-1">
                                            <component :is="item.icon" class="mr-1" />
                                            {{ item.label }}
                                        </Badge>
                                    </template>
                                </SelectFilters>


                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- PRIORIDAD -->
                    <FieldGroup>
                        <VeeField name="priority" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel class="text-sm font-semibold">
                                    Prioridad
                                </FieldLabel>

                                <SelectFilters :items="Object.values(ticketPriorityOptions)"
                                    :show-selected-focus="false" :show-refresh="false"
                                    :label="'Selecciona una prioridad'" item-label="label" item-value="value"
                                    selected-as-label :default-value="componentField.modelValue"
                                    @select="(value) => setFieldValue('priority', value)"
                                    filter-placeholder="Buscar prioridad..." empty-text="No se encontraron prioridades">
                                    <template #item="{ item }">
                                        <Badge :class="item.bg" class="px-3 py-1">
                                            <component :is="item.icon" class="mr-1" />
                                            {{ item.label }}
                                        </Badge>
                                    </template>
                                </SelectFilters>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </div>


            </form>

            <DialogFooter class="pt-6 border-t gap-3">
                <Button variant="outline" type="button" @click="open = false" :disabled="isSubmitting"
                    class="flex-1 sm:flex-none">
                    Cancelar
                </Button>
                <Button :disabled="isSubmitting || Object.keys(errors).length > 0" type="submit" form="dialogForm"
                    class="flex-1 sm:flex-none shadow-md hover:shadow-lg transition-all gap-2">
                    <Spinner v-if="isSubmitting" class="size-4" />
                    <!-- <span v-if="!isSubmitting">✅</span> -->
                    <Check v-if="!isSubmitting" class="size-4" />
                    <template v-if="!ticket">{{ isSubmitting ? 'Creando...' : 'Crear Ticket' }}</template>
                    <template v-else>{{ isSubmitting ? 'Actualizando...' : 'Actualizar Ticket' }}</template>

                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm, Field as VeeField } from 'vee-validate'
import { nextTick, ref, watch } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog'
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { Ticket, TicketPriority, ticketPriorityOptions, TicketRequestType, ticketRequestTypeOptions, TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface'
import { router } from '@inertiajs/vue3'
import { Check, Tag } from 'lucide-vue-next'
import * as z from 'zod'
import SelectFilters from '../SelectFilters.vue'

const ticket = defineModel<Ticket | null>('ticket', {
    type: Object as () => Ticket | null,
    required: false
})

defineProps({
    includeButton: {
        type: Boolean,
        default: true,
    }
});

// const open = ref(false);
const open = defineModel<boolean>('open', {
    type: Boolean,
    required: false,
    default: false,
});
const isSubmitting = ref(false);


const formSchema = toTypedSchema(z.object({
    title: z.string({
        message: 'El título es obligatorio',
    }).min(5, 'Minimo 5 caracteres').max(255, 'Máximo 255 caracteres'),
    description: z.string({
        message: 'La descripción es obligatoria',
    }).min(10, 'Minimo 10 caracteres').max(1000, 'Máximo 1000 caracteres'),
    type: z.nativeEnum(TicketType, {
        errorMap: () => ({ message: 'Selecciona un tipo válido' }),
        message: 'El tipo es obligatorio',
    }).default(TicketType.SERVICE_REQUEST),


    priority: z.nativeEnum(TicketPriority, {
        errorMap: () => ({ message: 'Selecciona una prioridad válida' }),
        message: 'La prioridad es obligatoria',
    }),

    request_type: z.nativeEnum(TicketRequestType, {
        errorMap: () => ({ message: 'Selecciona un tipo de solicitud válido' }),
        message: 'El tipo de solicitud es obligatorio',
    }).optional().nullable(),


}));

const initialValues = {
    type: TicketType.SERVICE_REQUEST,
};

const { handleSubmit, errors, setValues, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues
});

watch(() => ticket?.value, (newTicket) => {
    if (newTicket) {
        open.value = true;
        setValues({
            title: newTicket.title,
            description: newTicket.description,
            type: newTicket.type,
            priority: newTicket.priority,
            request_type: newTicket.request_type,
        });
    } else {
        open.value = false;
        setValues(initialValues);
    }
}, { immediate: true });


const handleReset = () => {
    setValues(initialValues);
    ticket.value = null;
};




function onSubmit(values: any) {
    isSubmitting.value = true;
    if (ticket?.value) {
        const only: string[] = [];
        if (ticket?.value?.priority !== values.priority) {
            only.push('tickets');
        }
        router.put(`/tickets/${ticket.value.id}`, values, {
            only,
            onFlash: (flash) => {
                const updatedTicket = flash.ticket as Ticket | null;
                if (updatedTicket && !only.includes('tickets')) {
                    nextTick(() => {
                        router.replaceProp('tickets.data', (tickets: Ticket[]) => {

                            const newTickets = tickets.map(t => {
                                if (t.id === updatedTicket.id) {
                                    return {

                                        ...updatedTicket,
                                        requester: t.requester,
                                        responsible: t.responsible,
                                        histories: t.histories,


                                    }
                                }
                                return t;
                            });

                            return newTickets;
                        });
                    })

                }
            },
            onSuccess: () => {
                open.value = false;
                handleReset();

            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
        return;
    }
    router.post('/tickets', values, {
        only: ['tickets'],
        onSuccess: () => {
            open.value = false;
            handleReset();
        },
        onFinish: () => {
            isSubmitting.value = false;
        }


    })
}


</script>
