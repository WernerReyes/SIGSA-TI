<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            open = false;
            asset = null;
        }
    }">
        <DialogContent class=" max-h-screen sm:max-w-106.25  overflow-y-auto">
            <DialogHeader>
                <h2 id="radix-:r88:" class="text-lg font-semibold leading-none tracking-tight">Cambiar Estado
                    AST-{{ asset?.id }}</h2>

            </DialogHeader>

            <div class="space-y-4 py-4">
                <VeeField name="status" v-slot="{ componentField, errors }">
                    <Field>
                        <FieldLabel for="status">Nuevo Estado</FieldLabel>

                        <Select v-bind="componentField">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Selecciona un nuevo estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Estados</SelectLabel>

                                    <SelectItem v-for="{ value, label, bg } in Object.values(assetStatusOptions).filter(option => option.value !== AssetStatus.ASSIGNED)"
                                        :key="value" :value="value">
                                        <Badge :class="bg">{{ label }}</Badge>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </Field>
                    <FieldError v-if="errors.length" :errors="errors" />
                </VeeField>

            </div>

            <DialogFooter>
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0
                    " @click="handleSubmit(handleFormSubmit)()">
                    <Spinner v-if="isSubmitting" />
                    {{ isSubmitting ? 'Cambiando estado...' : 'Cambiar Estado' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>


</template>

<script setup lang="ts">
import { ref } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Asset, AssetStatus, assetStatusOptions } from '@/interfaces/asset.interface';
import { router } from '@inertiajs/vue3';
import { useForm, Field as VeeField } from 'vee-validate';
import { watch } from 'vue';
import z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

// const open = ref(false);
const isSubmitting = ref(false);

const formSchema = toTypedSchema(z.object({
    status: z.nativeEnum(AssetStatus, {
        required_error: 'El estado es obligatorio',
        invalid_type_error: 'El estado seleccionado no es válido',
        message: 'El estado seleccionado no es válido'
    })
}));

const { handleSubmit, errors, handleReset, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: {
        status: asset?.value?.status
    }

});

watch(() => asset.value, (newAsset) => {
    if (newAsset) {
        setValues({
            status: newAsset.status
        });
    }
});

const handleFormSubmit = (values: { status: AssetStatus }) => {
    isSubmitting.value = true;

    router.put(
        `/assets/status`,
        {
            status: values.status,
            asset_id: asset?.value?.id
        },
        {
            onSuccess: () => {
                handleReset();
                open.value = false;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }

        }
    );
};

</script>