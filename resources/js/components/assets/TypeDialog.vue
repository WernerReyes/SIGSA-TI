<template>
    
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button type="button" variant="outline">
                <CirclePlusIcon />
                Agregar
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Nuevo Tipo</DialogTitle>
            </DialogHeader>

            <form id="typeDialogForm" @submit.prevent=" 
                handleSubmit(onSubmit)()
                " class="space-y-3">

                <!-- NOMBRE -->
                <FieldGroup>
                    <VeeField name="name" v-slot="{ componentField, errors }">
                        <Field :data-invalid="!!errors.length">
                            <FieldLabel for="name">Nombre</FieldLabel>
                            <Input id="name" placeholder="EJ: Laptop" v-bind="componentField" />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>
                </FieldGroup>
            </form>

            <TypeTable :assetTypes="assetTypes" @update:current-type="(selected) => {
                selectedType = selected;
                isEditing = true;
            }" />

            <DialogFooter>
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0
                    " type="submit" form="typeDialogForm">
                    <Spinner v-if="isSubmitting" />


                    <template v-if="isEditing">
                        {{ isSubmitting ? 'Actualizando...' : 'Actualizar Tipo' }}
                    </template>
                    <template v-else>{{ isSubmitting ? 'Creando...' : 'Crear Tipo' }}</template>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>



</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm, Field as VeeField } from 'vee-validate'
import { ref, watch } from 'vue'

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
import { Spinner } from '@/components/ui/spinner'
import { AssetType } from '@/interfaces/asset.interface'
import { router } from '@inertiajs/vue3'
import { CirclePlusIcon } from 'lucide-vue-next'
import * as z from 'zod'
import TypeTable from './TypeTable.vue'


const { assetTypes } = defineProps<{
    assetTypes: Array<AssetType>,
}>()


const open = ref(false);
const isSubmitting = ref(false);

const isEditing = ref(false);

const selectedType = ref<AssetType | null>(null);


const formSchema = toTypedSchema(
    z.object({
        name: z.string({
            message: 'El nombre es obligatorio'
        }).min(1, 'El nombre es obligatorio').refine((val) => assetTypes.every((type) => type.name.trim().toLowerCase() !== val.trim().toLowerCase()), {
            message: 'Ya existe un tipo con este nombre'
        }),
    })
)

const {
    handleSubmit,
    setFieldValue,
    resetForm,

    errors,

} = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: ''
    }
})


watch(selectedType, (val) => {
    if (val) {
        setFieldValue('name', val.name)
    } else {
        resetForm()
    }
})


function onSubmit(values: any) {
    isSubmitting.value = true;
    router.post('/assets/types', {
        id: selectedType.value ? selectedType.value.id : null,
        name: values.name
    }, {
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
