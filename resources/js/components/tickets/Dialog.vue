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
        <DialogContent class="sm:max-w-3xl overflow-y-auto">
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
            <ScrollArea class="max-h-96 sm:max-h-[70vh] pr-2">

                <form id="dialogForm" @submit.prevent="handleSubmit(onSubmit)()" class="space-y-5 py-4">
                    <!-- TÍTULO -->

                    <div class="flex max-sm:flex-col gap-5">
                        <FieldGroup>
                            <VeeField name="title" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="title" class="text-sm font-semibold">
                                        Título
                                    </FieldLabel>
                                    <Input id="title" placeholder="Ej: No puedo acceder al sistema de nómina"
                                        class="h-11" v-bind="componentField" />
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
                                        class="resize-none" v-bind="componentField" />
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>

                    <FieldGroup>
                        <VeeField name="type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel class="text-sm font-semibold">
                                    Tipo de Ticket
                                </FieldLabel>
                                <Tabs v-bind="componentField" :default-value="TicketType.SERVICE_REQUEST"
                                    class="w-full">
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
                                        :show-selected-focus="false" :show-refresh="false" label="Selecciona"
                                        item-label="label" item-value="value" selected-as-label
                                        :default-value="componentField.modelValue"
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
                                        filter-placeholder="Buscar prioridad..."
                                        empty-text="No se encontraron prioridades">
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


                    <FieldGroup>
                        <VeeField name="requester_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="requester_id">Solicitante</FieldLabel>
                                <!-- :disabled="!!asset?.current_assignment?.parent_assignment_id || !canEdit" -->
                                <div class="flex items-center gap-2">
                                    <Button type="button" variant="outline" size="icon-sm" class=""
                                        :disabled="!values.requester_id" @click="handleOpenRequesterAssets">
                                        <MonitorSmartphone class="size-4" />
                                    </Button>
                                    <div class="flex-1">

                                        <SelectFilters data-key="users" :items="users" :show-selected-focus="false"
                                            :show-refresh="false" :label="requesterLabel" :full-width="true"
                                            item-label="full_name" item-value="staff_id" selected-as-label
                                            :default-value="componentField.modelValue"
                                            @select="(value) => setFieldValue('requester_id', +value)"
                                            filter-placeholder="Buscar solicitante..."
                                            empty-text="No se encontraron solicitantes">
                                        </SelectFilters>
                                    </div>
                                </div>

                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                </form>

            </ScrollArea>

            <DialogFooter class="pt-6 border-t gap-3">
                <Button variant="outline" type="button" @click="open = false" :disabled="isLoading"
                    class="flex-1 sm:flex-none">
                    Cancelar
                </Button>
                <Button :disabled="disableForm" type="submit" form="dialogForm"
                    class="flex-1 sm:flex-none shadow-md hover:shadow-lg transition-all gap-2">

                    <Check class="size-4" />
                    <template v-if="!ticket">{{ 'Crear Ticket' }}</template>
                    <template v-else>{{ 'Actualizar Ticket' }}</template>

                </Button>
            </DialogFooter>
        </DialogContent>
        <RequesterAssetsSheet v-model:open="openRequesterAssets" :assignments="requesterAssignments"
            :requester-id="values.requester_id" />
    </Dialog>
    <!-- </Form> -->
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm, Field as VeeField } from 'vee-validate'
import { computed, ref, watch } from 'vue'

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
import { ScrollArea } from '@/components/ui/scroll-area'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { useApp } from '@/composables/useApp'
import type { AssetAssignment } from '@/interfaces/assetAssignment.interface'
import { Ticket, TicketPriority, ticketPriorityOptions, TicketRequestType, ticketRequestTypeOptions, TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface'
import { isEqual } from '@/lib/utils'
import { router, usePage } from '@inertiajs/vue3'
import { Check, MonitorSmartphone, Tag } from 'lucide-vue-next'
import * as z from 'zod'
import SelectFilters from '../SelectFilters.vue'
import RequesterAssetsSheet from './RequesterAssetsSheet.vue'


const { isLoading, users, userAuth } = useApp();
const page = usePage();

const open = defineModel<boolean>('open', {
    type: Boolean,
    required: false,
    default: false,
});

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



const disableForm = computed(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || isEqual(initialValues.value, values);
});

const openRequesterAssets = ref(false);

const requesterAssignments = computed<AssetAssignment[]>(() => {
    return (page.props as unknown as { requesterAssignments?: AssetAssignment[] })?.requesterAssignments || [];
});



const requesterLabel = computed(() => {
    if (ticket.value?.requester) {
        return ticket.value.requester.full_name;
    }
    return userAuth.value ? userAuth.value.full_name : 'Seleccionar solicitante...';
})

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


    requester_id: z.number({
        invalid_type_error: 'Selecciona un solicitante válido',
        required_error: 'El solicitante es obligatorio',
    }).optional().nullable(),


}).refine((data) => {
    if (data.type === TicketType.SERVICE_REQUEST) {
        return data.request_type !== null && data.request_type !== undefined;
    }
    return true;
}, {
    path: ['request_type'],
    message: 'El tipo de solicitud es obligatorio para tickets de tipo "Solicitud de Servicio"',
}));

const initialValues = computed(() => ({
    title: ticket.value?.title || '',
    description: ticket.value?.description || '',
    type: ticket.value?.type || TicketType.SERVICE_REQUEST,
    priority: ticket.value?.priority || TicketPriority.MEDIUM,
    request_type: ticket.value?.request_type || null,
    requester_id: ticket.value?.requester_id || userAuth.value?.staff_id || null,
}))

const { handleSubmit, errors, setValues, values, setFieldValue, resetForm } = useForm({
    validationSchema: formSchema,
    initialValues: initialValues.value,
    keepValuesOnUnmount: true,
});

type StoreTicket = typeof values;


watch(values, (values) => {
    if (values.type === TicketType.INCIDENT) {
        setFieldValue('request_type', initialValues.value.request_type || null);
    }
}, { deep: true })



const handleReset = () => {
    resetForm();
    ticket.value = null;
};

const handleOpenRequesterAssets = () => {
    if (!values.requester_id) {
        openRequesterAssets.value = true;
        return;
    }

    router.reload({
        only: ['requesterAssignments'],
        data: { requester_id: values.requester_id },
        preserveUrl: true,
        onSuccess: () => {
            openRequesterAssets.value = true;
        },
       
    });
};



function onSubmit(values: StoreTicket) {
    if (ticket?.value) {
        const only: string[] = [];
        if (ticket?.value?.priority !== values.priority) {
            only.push('tickets');
        }


        router.put(`/tickets/${ticket.value.id}`, values, {
            only,
            onFlash: (flash) => {
                if (flash.error) return;
                const updatedTicket = flash.ticket as Ticket | null;
                if (updatedTicket && !only.includes('tickets')) {
                    // nextTick(() => {
                    router.replaceProp('tickets.data', (tickets: Ticket[]) => {

                        const newTickets = tickets.map(t => {
                            if (t.id === updatedTicket.id) {
                                return {

                                    ...updatedTicket,
                                    requester: updatedTicket.requester || t.requester,
                                    responsible: t.responsible,
                                    histories: t.histories,


                                }
                            }
                            return t;
                        });

                        return newTickets;
                    });
                    // })

                }

                open.value = false;
                handleReset();
            },
           

        });
        return;
    }
    router.post('/tickets', values, {
        only: ['tickets'],
        onFlash: (flash) => {
            if (flash.error) return;
           open.value = false;
            handleReset();
        
        },
       

    })
}


</script>
