<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'
import { computed, h, ref, watch } from 'vue'

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
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { type Department } from '@/interfaces/department.interace'
import { ticketPriorityOptions, TicketType, TicketPriority, ticketTypeOptions, TicketRequestType, ticketRequestTypeOptions } from '@/interfaces/ticket.interface'
import { Tag } from 'lucide-vue-next'
import { Spinner } from '@/components/ui/spinner'
import { toast } from 'vue-sonner'
import { router, useForm, usePage } from '@inertiajs/vue3'
import * as z from 'zod'
import createServer from '@inertiajs/vue3/server';



defineProps<{
    departments: Array<Department>,

}>();


const open = ref(false);
const isSubmitting = ref(false);


// watch(
//     () => success,
//     (success) => {
//         if (success) {
//             resetForm();
//         }
//     }
// );


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
    }).optional(),

    // technician_id: z.number({
    //     message: 'Selecciona un técnico válido',
    // }).optional(),

}));

function onSubmit(values: any, { resetForm }: { resetForm: () => void }) {
    isSubmitting.value = true;
    router.post('/tickets', values, {
        onSuccess: () => {
            open.value = false;
            resetForm();
        },
        onFinish: () => {
            isSubmitting.value = false;
        }


    })
}


</script>

<template>

    <Form :initial-values="{
        type: TicketType.SERVICE_REQUEST
    }" v-slot="{ getValues, handleSubmit, errors }" as="" keep-values :validation-schema="formSchema">

        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button class="w-fit ml-auto">
                    <Tag />
                    Nuevo Ticket
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Nuevo Ticket</DialogTitle>
                </DialogHeader>

                <form id="dialogForm" @submit.prevent=" 
                    //   router.post('/tickets', getValues())
                    handleSubmit(onSubmit)
                    " class="space-y-3">
                    <!-- TÍTULO -->
                    <FieldGroup>
                        <VeeField name="title" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="title">Título</FieldLabel>
                                <Input id="title" placeholder="Describe brevemente el problema"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- DESCRIPCIÓN -->
                    <FieldGroup>
                        <VeeField name="description" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="description">Descripción</FieldLabel>
                                <Textarea id="description" rows="4"
                                    placeholder="Proporciona detalles adicionales del problema..."
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <!-- <Input id="eventId" type="number" placeholder="12345"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" /> -->

                                <Tabs v-bind="componentField" :default-value="TicketType.SERVICE_REQUEST" class="mt-4">

                                    <TabsList>
                                        <!-- <TabsTrigger value="account">
                                            Account
                                        </TabsTrigger> -->
                                        <!-- <TabsTrigger value="password">
                                            Password
                                        </TabsTrigger> -->
                                        <TabsTrigger v-for="type in Object.values(ticketTypeOptions)" :key="type.value"
                                            :value="type.value">
                                            {{ type.label }}
                                        </TabsTrigger>


                                    </TabsList>
                                </Tabs>
                            </Field>
                        </VeeField>
                    </FieldGroup>


                    <div class="grid gap-4"
                        :class="getValues().type === TicketType.SERVICE_REQUEST ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                        <!-- CATEGORÍA -->
                        <FieldGroup v-if="getValues().type === TicketType.SERVICE_REQUEST">
                            <VeeField name="request_type" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Categoría</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>
                                        <SelectContent>

                                            <SelectItem v-for="requestType in Object.values(ticketRequestTypeOptions)"
                                                :key="requestType.value" :value="requestType.value">
                                                {{ requestType.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <!-- PRIORIDAD -->
                        <FieldGroup>
                            <VeeField name="priority" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Prioridad</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>

                                        <SelectContent>
                                            <SelectItem v-for="priority in Object.values(ticketPriorityOptions)"
                                                :key="priority.value" :value="priority.value">

                                                <Badge :class="priority.bg">
                                                    {{ priority.label }}
                                                </Badge>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>

                    <!-- SOLICITANTE -->

                    <!-- <FieldGroup>
                        <VeeField name="technician_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Técnico</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar técnico" />
                                    </SelectTrigger>

                                    <SelectContent>
                                        <SelectGroup v-for="department in departments" :key="department.id">
                                            <SelectLabel>{{ department.name }}</SelectLabel>
                                            <SelectItem v-for="tech in department.users" :key="tech.staff_id"
                                                :value="tech.staff_id">
                                                {{ tech.firstname }} {{ tech.lastname }}
                                            </SelectItem>
                                        </SelectGroup>

                                    </SelectContent>
                                </Select>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup> -->

                    <!-- ACTIVO -->
                    <!-- <FieldGroup>
                        <VeeField name="asset_id" v-slot="{ componentField, errors }">
                            <Field>
                                <FieldLabel>Activo Relacionado (opcional)</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar activo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pc-01">PC-01</SelectItem>
                                        <SelectItem value="laptop-02">Laptop</SelectItem>
                                    </SelectContent>
                                </Select>
                            </Field>
                        </VeeField>
                    </FieldGroup> -->
                </form>

                <DialogFooter>
                    <Button :disabled="isSubmitting
                        || Object.keys(errors).length > 0
                        " type="submit" form="dialogForm">
                        <Spinner v-if="isSubmitting" />
                        Crear Ticket
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </Form>
</template>
