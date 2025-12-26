<template>

    <Form :initial-values="{
        type: TicketType.SERVICE_REQUEST
    }" v-slot="{ getValues, handleSubmit, errors }" as="" keep-values :validation-schema="formSchema">

        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button class="w-fit ml-auto">
                    <Laptop />
                    Nuevo Activo
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Nuevo Activo</DialogTitle>
                </DialogHeader>

                <form id="dialogForm" @submit.prevent=" 
                    handleSubmit(onSubmit)
                    " class="space-y-3">
                    <!-- NOMBRE -->
                    <FieldGroup>
                        <VeeField name="name" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="name">Nombre</FieldLabel>
                                <Input id="name" placeholder="EJ: Laptop de Desarrollo" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <div class="grid gap-4 items-center md:grid-cols-2">
                        <FieldGroup>
                            <VeeField name="type" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Tipo</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>
                                        <SelectContent>

                                            <SelectItem v-for="assetType in Object.values(assetTypeOptions)"
                                                :key="assetType.value" :value="assetType.value">
                                                <component :is="assetType.icon" class="inline-block size-4" />
                                                {{ assetType.label }}

                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <FieldGroup>
                            <VeeField name="status" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Estado</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>
                                        <SelectContent>

                                            <SelectItem v-for="status in Object.values(assetStatusOptions)"
                                                :key="status.value" :value="status.value">
                                                <Badge :class="status.bg">{{ status.label }}</Badge>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>

                    <div class="grid gap-4 items-center md:grid-cols-2">
                        <FieldGroup>
                            <VeeField name="brand" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="brand">Marca</FieldLabel>
                                    <Input id="brand" placeholder="EJ: Dell, HP, Lenovo" v-bind="componentField" />
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <FieldGroup>
                            <VeeField name="model" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="model">Modelo</FieldLabel>
                                    <Input id="model" placeholder="EJ: Inspiron 15 3000" v-bind="componentField" />
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>

                    <FieldGroup>
                        <VeeField name="serial_number" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="serial_number">Número de Serie</FieldLabel>
                                <Input id="serial_number" placeholder="EJ: SN1234567890" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>


                    <div class="grid gap-4 items-center md:grid-cols-2">

                        <FieldGroup>
                            <VeeField name="purchase_date" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="purchase_date">Fecha de Compra</FieldLabel>
                                    <!-- <Input type="date" id="purchase_date" v-bind="componentField" /> -->
                                     <Popover v-slot="{ close }">
      <PopoverTrigger as-child>
        <Button
          id="date"
          variant="outline"
          class="w-48 justify-between font-normal"
        >
          <!-- {{ date ? date.toDate(getLocalTimeZone()).toLocaleDateString() : "Select date" }} -->
          <ChevronDownIcon />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-auto overflow-hidden p-0" align="start">
          <!-- :model-value="date" -->
        <Calendar
          layout="month-and-year"
          @update:model-value="(value) => {
            if (value) {
            //   date = value
            //   close()
            }
          }"
        />
      </PopoverContent>
    </Popover>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>


                        <FieldGroup>
                            <VeeField name="warranty_expiration" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="warranty_expiration">Vencimiento de Garantía</FieldLabel>
                                    <Input type="date" id="warranty_expiration" v-bind="componentField" />
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

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'
import { ref } from 'vue'

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
import { type Department } from '@/interfaces/department.interace'
import { TicketPriority, ticketPriorityOptions, TicketRequestType, ticketRequestTypeOptions, TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface'
import { router } from '@inertiajs/vue3'
import { Laptop, Monitor, Tag } from 'lucide-vue-next'
import * as z from 'zod'
import { assetStatusOptions, assetTypeOptions } from '@/interfaces/asset.interface'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Calendar } from '@/components/ui/calendar'

defineProps<{
    // departments: Array<Department>,

}>();


const open = ref(false);
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
