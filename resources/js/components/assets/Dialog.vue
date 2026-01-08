<template>
    <Dialog v-model:open="open" @update:open="(isOpen) => {
        if (!isOpen) {
            handleResetForm();
        }
    }">
        <DialogTrigger v-if="includeButton" as-child>
            <Button class="w-fit ml-auto">
                <Laptop />

                Nuevo Activo
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-8/12 max-h-screen overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <Laptop />
                    {{ currentAsset ? 'Editar Activo' : 'Nuevo Activo' }}
                </DialogTitle>
            </DialogHeader>

            <form id="dialogForm" @submit.prevent=" 
                handleSubmit(onSubmit)()
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
                        <VeeField name="type_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Tipo</FieldLabel>

                                <!-- <Field class="flex gap-2 bg-red-400"> -->
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel class="flex justify-center">

                                                <TypeDialog :assetTypes="assetTypes" />
                                            </SelectLabel>
                                            <SelectItem v-if="assetTypes.length === 0" disabled value="empty">
                                                No hay tipos de activos disponibles
                                            </SelectItem>

                                            <SelectItem v-else v-for="assetType in assetTypes" :key="assetType.id"
                                                :value="assetType.id">
                                                {{ assetType.name }}
                                            </SelectItem>

                                        </SelectGroup>


                                    </SelectContent>
                                </Select>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>

                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="color" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Color</FieldLabel>
                                <!-- <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar" />
                                    </SelectTrigger>
                                    <SelectContent>

                                        <SelectItem
                                            v-for="status in Object.values(assetStatusOptions).filter(s => s.value !== AssetStatus.ASSIGNED)"
                                            :key="status.value" :value="status.value">
                                            <Badge :class="status.bg">{{ status.label }}</Badge>
                                        </SelectItem>
                                    </SelectContent>
                                </Select> -->
                                <Input id="color" placeholder="Ingresa un color (EJ: Rojo, Azul)" v-bind="componentField" />
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


                <!-- <div class="grid gap-4 items-center"> -->
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

            </form>

            <DialogFooter>
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0
                    " type="submit" form="dialogForm">
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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Asset, AssetType } from '@/interfaces/asset.interface'
import { type User } from '@/interfaces/user.interface'
import { router, usePage } from '@inertiajs/vue3'
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date'
import { Laptop } from 'lucide-vue-next'
import * as z from 'zod'
import TypeDialog from './TypeDialog.vue'

defineProps<{
    includeButton?: boolean
}>();

const currentAsset = defineModel<Asset | null>('current-asset', {
    type: Object as () => Asset | null,
    required: false
})

const openEditor = defineModel<boolean>('open-editor', {
    type: Boolean,
    required: true
});


const assetTypes = computed<AssetType[]>(() => (usePage().props.types || []) as AssetType[]);
const users = computed<User[]>(() => (usePage().props.users || []) as User[]);

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
    }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
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

const { handleSubmit, handleReset, errors, values, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: initialFormValues
})


function onSubmit(values: any) {
    isSubmitting.value = true;


    if (currentAsset.value) {
        router.put(`/assets/${currentAsset.value.id}`, {
            // id: currentAsset.value.id,
            ...values
        }, {
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
        onSuccess: () => {
            open.value = false;
            handleReset();
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
