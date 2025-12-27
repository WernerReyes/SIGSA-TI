<template>

    <Form :initial-values="{
        name: '',
        type: '',
        status: '',
        brand: '',
        model: '',
        serial_number: '',
        purchase_date: today(getLocalTimeZone()),
        warranty_expiration: today(getLocalTimeZone()),
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

                                    <Field class="flex gap-2 bg-red-400">
                                        <Select v-bind="componentField" class="flex-1">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleccionar" />
                                            </SelectTrigger>
                                            <SelectContent>

                                                <SelectItem v-if="assetTypes.length === 0" disabled value="empty">
                                                    No hay tipos de activos disponibles
                                                </SelectItem>

                                                <SelectItem v-else v-for="assetType in assetTypes" :key="assetType.id"
                                                    :value="assetType.id">
                                                    {{ assetType.name }}
                                                </SelectItem>


                                            </SelectContent>
                                        </Select>



                                        <TooltipProvider>
                                            <Tooltip>
                                                <TooltipTrigger as-child>
                                                    <Button type="button" variant="outline" size="icon"
                                                        class="ml-auto w-12!">
                                                        <CirclePlusIcon />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>Agregar Tipo de Activo</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        </TooltipProvider>
                                    </Field>



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
                                        <Popover v-slot="{ close }">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" class="w-48 justify-between font-normal">
                                                    {{ componentField.modelValue
                                                        ?
                                                        componentField.modelValue.toDate(getLocalTimeZone()).toLocaleDateString()
                                                        : 'Seleccionar fecha' }}

                                                    <ChevronDownIcon />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                                <Calendar v-bind="componentField" locale="es" layout="month-and-year"
                                                    selection-mode="single" @update:model-value="(value) => {
                                                        if (value) {
                                                            componentField.onChange(value)
                                                            close()
                                                        }
                                                    }" />
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
                                        <!-- <Input type="date" id="warranty_expiration" v-bind="componentField" /> -->
                                        <Popover v-slot="{ close }">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" class="w-48 justify-between font-normal">
                                                    {{ componentField.modelValue
                                                        ?
                                                        componentField.modelValue.toDate(getLocalTimeZone()).toLocaleDateString()
                                                        : 'Seleccionar fecha' }}

                                                    <ChevronDownIcon />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                                <Calendar v-bind="componentField" locale="es" layout="month-and-year"
                                                    selection-mode="single" @update:model-value="(value) => {
                                                        if (value) {
                                                            componentField.onChange(value)
                                                            close()
                                                        }
                                                    }" />
                                            </PopoverContent>
                                        </Popover>
                                        <FieldError v-if="errors.length" :errors="errors" />
                                    </Field>
                                </VeeField>
                            </FieldGroup>


                        </div>




                </form>

                <DialogFooter>
                    <Button :disabled="isSubmitting
                        || Object.keys(errors).length > 0
                        " type="submit" form="dialogForm">
                        <Spinner v-if="isSubmitting" />
                        {{ isSubmitting ? 'Creando...' : 'Crear Activo' }}
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
import { Calendar } from '@/components/ui/calendar'
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
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { AssetStatus, assetStatusOptions, AssetType } from '@/interfaces/asset.interface'
import { router } from '@inertiajs/vue3'
import { getLocalTimeZone, today } from '@internationalized/date'
import { CirclePlus, CirclePlusIcon, Laptop } from 'lucide-vue-next'
import * as z from 'zod'
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';

defineProps<{
    // departments: Array<Department>,
    assetTypes: AssetType[]

}>();


const open = ref(false);
const isSubmitting = ref(false);


const formSchema = toTypedSchema(z.object({
    name: z.string().min(1, 'El nombre es obligatorio'),
    // type: z.nativeEnum(AssetType, {
    //     errorMap: () => ({ message: 'El tipo de activo es obligatorio' })
    // }),
    status: z.nativeEnum(
        AssetStatus,
        {
            errorMap: () => ({ message: 'El estado del activo es obligatorio' })
        }
    ),
    brand: z.string().min(1, 'La marca es obligatoria'),
    model: z.string().min(1, 'El modelo es obligatorio'),
    serial_number: z.string().min(1, 'El número de serie es obligatorio'),
    purchase_date: z.date({
        required_error: 'La fecha de compra es obligatoria',
        invalid_type_error: 'La fecha de compra debe ser una fecha válida'
    }),
    warranty_expiration: z.date({
        required_error: 'La fecha de vencimiento de la garantía es obligatoria',
        invalid_type_error: 'La fecha de vencimiento de la garantía debe ser una fecha válida'
    }),

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
