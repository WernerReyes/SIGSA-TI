<template>

    <Dialog v-model:open="open" @update:open="() => {
        handleResetForm();

    }">
        <DialogTrigger v-if="includeButton" as-child>
            <Button class="w-fit ml-auto">
                <Laptop />

                Nuevo Activo
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-4xl">
            <DialogHeader class="space-y-3 pb-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <!-- <Laptop class="size-5 text-primary" /> -->
                         
                        <component v-if="currentAsset?.type?.name" :is="assetTypeOp(currentAsset?.type?.name)?.icon" class="size-5 text-primary" />
                        <Laptop v-else class="size-5 text-primary" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <DialogTitle class="text-xl font-semibold">
                            {{ currentAsset ? `Editar ${currentAsset?.type?.name}` : 'Crear Nuevo Activo' }}
                        </DialogTitle>
                        <p class="text-sm text-muted-foreground">
                            {{ currentAsset ? `Actualiza la información de ${currentAsset?.type?.name}` : 'Completa los datos del nuevo activo tecnológico' }}
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <form id="dialogForm" @submit.prevent=" 
                handleSubmit(onSubmit)()
                " class="space-y-5 py-4 max-h-96 overflow-y-auto">
                
                <Accordion :unmountOnHide="false" type="multiple" :default-value="['info', 'specs', 'dates']" class="w-full space-y-3">
                    <!-- Sección: Información Básica -->
                    <AccordionItem value="info" class="border rounded-lg px-4">
                        <AccordionTrigger class="hover:no-underline">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <Laptop class="size-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <span class="font-semibold">Información Básica</span>
                            </div>
                        </AccordionTrigger>
                        <AccordionContent class="space-y-4 pt-4">
                            <!-- NOMBRE -->
                            <FieldGroup>
                                <VeeField name="name" v-slot="{ componentField, errors }">
                                  
                                    <Field :data-invalid="!!errors.length">
                                        <FieldLabel for="name">Nombre del Activo</FieldLabel>
                                        <Input id="name" placeholder="EJ: Laptop de Desarrollo" v-bind="componentField" />
                                        <FieldError v-if="errors.length" :errors="errors" />
                                    </Field>
                                </VeeField>
                            </FieldGroup>
                <div class="grid gap-4 items-center md:grid-cols-2">
                    <FieldGroup>
                        <VeeField name="type_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Tipo</FieldLabel>

                                <!-- <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue :placeholder="currentAsset?.type?.name ||
                                            'Seleccionar'" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>

                                        </SelectGroup>
                                        <WhenVisible data="types">
                                            <template #fallback>

                                                <div class="flex flex-col gap-2 p-3">

                                                    <Skeleton v-for="n in 4" :key="n" class="h-6 w-full" />
                                                </div>

                                            </template>

                                            <SelectGroup>
                                                <SelectItem v-if="assetTypes.length === 0" disabled value="empty">
                                                    No hay tipos de activos disponibles
                                                </SelectItem>

                                                <SelectItem v-else v-for="assetType in assetTypes" :key="assetType.id"
                                                    :value="assetType.id">
                                                    <component :is="assetTypeOp(assetType.name)?.icon"
                                                        class="inline size-4" />
                                                    {{ assetType.name }}
                                                    <Skeleton class="h-4 w-full" />
                                                </SelectItem>

                                            </SelectGroup>
                                        </WhenVisible>





                                    </SelectContent>
                                </Select> -->

                                <SelectFilters label="Tipo de Activo" :items="assetTypes" data-key="types"
                                    item-label="name" item-value="id" :default-value="componentField.modelValue"
                                    :selected-as-label="true"
                                    :show-refresh="false"
                                    :show-selected-focus="false"
                                    @select="(value) => setFieldValue('type_id', value)" filter-placeholder="Buscar tipo..."
                                    empty-text="No se encontraron tipos de activos">
                                    <template #item="{ item }">
                                        <component :is="assetTypeOp(item.name)?.icon" class="inline size-4" />
                                        {{ item.name }}
                                    </template>
                                </SelectFilters>

                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>

                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="color" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Color</FieldLabel>
                                <Input id="color" placeholder="Ingresa un color (EJ: Rojo, Azul)"
                                    v-bind="componentField" />
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
                        </AccordionContent>
                    </AccordionItem>

                    <!-- Sección: Especificaciones Técnicas -->
                    <AccordionItem value="specs" class="border rounded-lg px-4">
                        <AccordionTrigger class="hover:no-underline">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                </div>
                                <span class="font-semibold">Especificaciones Técnicas</span>
                            </div>
                        </AccordionTrigger>
                        <AccordionContent  class="space-y-4 pt-4">
                            <div class="grid gap-4 items-center md:grid-cols-2">
                                <FieldGroup>
                                    <VeeField name="serial_number" v-slot="{ componentField, errors }">
                                        <Field :data-invalid="!!errors.length">
                                            <FieldLabel for="serial_number">Número de Serie</FieldLabel>
                                            <Input id="serial_number" placeholder="EJ: SN1234567890" v-bind="componentField" />
                                            <FieldError v-if="errors.length" :errors="errors" />
                                        </Field>
                                    </VeeField>
                                </FieldGroup>

                    <FieldGroup>
                        <VeeField name="processor" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="processor">Procesador</FieldLabel>
                                <Input id="processor" placeholder="EJ: Intel Core i7-10750H" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </div>


                <div class="grid gap-4 items-center md:grid-cols-2">
                    <FieldGroup>
                        <VeeField name="ram" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="ram">Memoria RAM</FieldLabel>
                                <Input id="ram" placeholder="EJ: 16GB DDR4" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="storage" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="storage">Almacenamiento</FieldLabel>
                                <Input id="storage" placeholder="EJ: 512GB SSD" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </div>


                <div class="grid gap-4 items-center md:grid-cols-2">
                    <FieldGroup>
                        <VeeField name="phone" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="phone">Número de Teléfono</FieldLabel>
                                <Input id="phone" placeholder="EJ: +1234567890" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="imei" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="imei">IMEI</FieldLabel>
                                <Input id="imei" placeholder="EJ: 356789012345678" v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </div>
                        </AccordionContent>
                    </AccordionItem>

                    <!-- Sección: Fechas y Garantía -->
                    <AccordionItem value="dates" class="border rounded-lg px-4">
                        <AccordionTrigger class="hover:no-underline">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="font-semibold">Fechas y Garantía</span>
                            </div>
                        </AccordionTrigger>
                        <AccordionContent  class="space-y-4 pt-4">
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
                        <VeeField name="is_new" v-slot="{ value, setValue, errors }">


                            <div class="flex items-center gap-3">
                                <Checkbox :model-value="value" @update:model-value="setValue($event)" />
                                <FieldLabel for="terms">¿Es un activo nuevo?</FieldLabel>
                            </div>
                            <FieldError v-if="errors.length" :errors="errors" />

                        </VeeField>
                    </FieldGroup>
                </div>
                        </AccordionContent>
                    </AccordionItem>

                </Accordion>

            </form>

            <DialogFooter class="gap-2 pt-4 border-t">
                <Button variant="outline" @click="() => { open = false; handleResetForm(); }" :disabled="isSubmitting">
                    Cancelar
                </Button>
                <Button :disabled="isSubmitting || Object.keys(errors).length > 0" type="submit" form="dialogForm" class="min-w-32">
                    <Spinner v-if="isSubmitting" />
                    <template v-if="currentAsset">
                        {{ isSubmitting ? 'Actualizando...' : 'Actualizar Activo' }}
                    </template>
                    <template v-else>
                        {{ isSubmitting ? 'Creando...' : 'Crear Activo' }}
                    </template>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm, Field as VeeField } from 'vee-validate'
import { computed, ref, watch } from 'vue'

import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import { Checkbox } from '@/components/ui/checkbox'
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

import { Spinner } from '@/components/ui/spinner'
import { type Asset } from '@/interfaces/asset.interface'
import { assetTypeOp, TypeName } from '@/interfaces/assetType.interface'
import { router, usePage } from '@inertiajs/vue3'
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date'
import { ChevronDownIcon, Laptop } from 'lucide-vue-next'

import { useApp } from '@/composables/useApp'
import { isBefore, isEqual } from 'date-fns'
import * as z from 'zod'
import SelectFilters from '../SelectFilters.vue'


defineProps<{
    includeButton?: boolean
}>();

const { assetTypes } = useApp();
const page = usePage();

const currentAsset = defineModel<Asset | null>('current-asset', {
    type: Object as () => Asset | null,
    required: false
})

const openEditor = defineModel<boolean>('open-editor', {
    type: Boolean,
    required: true
});

const assetAccessories = computed<Asset[]>(() => {
    return page.props?.accessories as Asset[] || [];
});

const open = ref(false);
const isSubmitting = ref(false);

watch(() => openEditor.value, (editor) => {
    const newAsset = currentAsset.value;
    if (editor && newAsset) {
        open.value = true;
        setValues({
            name: newAsset.name,
            color: newAsset.color,
            brand: newAsset.brand,
            model: newAsset.model,
            serial_number: newAsset.serial_number,
            processor: newAsset.processor,
            ram: newAsset.ram,
            storage: newAsset.storage,
            type_id: newAsset.type_id,
            purchase_date: parseDate(
                newAsset.purchase_date.split('T')[0]
            ),
            warranty_expiration: parseDate(
                newAsset.warranty_expiration.split('T')[0]
            ),
            is_new: Boolean(newAsset.is_new),
            // assigned_to: newAsset.assigned_to_id || undefined,
        });
    } else {
        open.value = false;
        openEditor.value = false;
        handleResetForm();
    }
});

const formSchema = toTypedSchema(z.object({
    name: z.string({
        message: 'El nombre es obligatorio'
    }).min(1, 'El nombre es obligatorio'),
    color: z.string().optional().nullable(),
    type_id: z.number({
        message: 'El tipo de activo es obligatorio'
    }),
    brand: z.string({
        message: 'La marca es obligatoria'
    }).min(1, 'La marca es obligatoria'),
    model: z.string().optional().nullable(),
    serial_number: z.string().optional().nullable(),
    processor: z.string().optional().nullable(),
    ram: z.string().optional().nullable(),
    storage: z.string().optional().nullable(),
    phone: z.string().optional().nullable(),
    imei: z.string().optional().nullable(),
    purchase_date: z.instanceof(CalendarDate, {
        message: 'La fecha de compra es obligatoria'
    }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
    warranty_expiration: z.instanceof(CalendarDate, {
        message: 'La fecha de vencimiento de la garantía es obligatoria'
    }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())).refine((date) => {
        const purchaseDate = (values.purchase_date as CalendarDate).toDate(getLocalTimeZone()) as Date;
        return isBefore(purchaseDate, date) || isEqual(purchaseDate, date);
    }, {
        message: 'La fecha de vencimiento de la garantía no puede ser anterior a la fecha de compra'
    }),
    is_new: z.boolean().optional().default(true),
    // assigned_to: z.number().optional(),


}));


const initialFormValues = {
    name: '',
    color: '',
    brand: '',
    model: '',
    serial_number: '',
    type_id: undefined,
    purchase_date: today(getLocalTimeZone()),
    warranty_expiration: today(getLocalTimeZone()),
    processor: '',
    ram: '',
    storage: '',
    phone: '',
    imei: '',
    is_new: true,
    assigned_to: undefined,
};

const { handleSubmit, handleReset, errors, setValues, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: initialFormValues
})



function onSubmit(values: any) {
    isSubmitting.value = true;

    const only = ['assetsPaginated', 'stats'];

    if (assetTypes.value.find(type => type.id === values.type_id)?.name === TypeName.ACCESSORY
        || assetAccessories.value.find(accessory => accessory.id === currentAsset.value?.id)
    ) {
        only.push('accessories');
    }

    if (currentAsset.value) {
        router.put(`/assets/${currentAsset.value.id}`, {
            ...values,

        }, {
            only,

            onSuccess: () => {
                open.value = false;
                handleResetForm();

            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        })
        return;
    }


    router.post('/assets', values, {
        only,
        onSuccess: () => {
            open.value = false;
            handleResetForm();
        },
        onFinish: () => {
            isSubmitting.value = false;
        }


    })
}

const handleResetForm = () => {
    handleReset();
    currentAsset.value = null;
    openEditor.value = false;
    setValues(initialFormValues);


}

</script>
