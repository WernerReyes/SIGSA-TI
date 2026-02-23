<template>
    <Dialog v-model:open="open" @update:open="onClose">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="flex items-center gap-2 mb-4">
                <Tablet class="size-6 text-primary" />
                <h2 class="font-bold text-lg">{{
                    currentType ? 'Editar tipo' : 'Nuevo tipo' }}</h2>
            </DialogHeader>
            <form @submit.prevent="handleSubmit(onSubmit)()">
                <div class="space-y-4">
                    <VeeField name="name" v-slot="{ componentField, errors }">
                        <div class="space-y-2">
                            <Label :for="componentField.name" class="flex items-center gap-2">
                                Nombre *
                            </Label>
                            <InputGroup>
                                <InputGroupAddon>
                                    <Tablet class="h-4 w-4 text-muted-foreground" />
                                </InputGroupAddon>
                                <InputGroupInput placeholder="Ej: Laptop" v-bind="componentField" />
                            </InputGroup>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </div>
                    </VeeField>
                    <!-- Selector de categoría de documento -->
                    <VeeField name="doc_category" v-slot="{ componentField, errors }">
                        <div class="space-y-2">
                            <Label :for="componentField.name" class="flex items-center gap-2">
                                Categoría de documento *
                            </Label>
                            <InputGroup>
                                <InputGroupAddon>
                                    <File class="h-4 w-4 text-muted-foreground" />
                                </InputGroupAddon>
                                <Select v-bind="componentField">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Seleccionar categoría" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Categorías</SelectLabel>
                                            <SelectItem v-for="category in assetTypeUI.category.array" :key="category"
                                                :value="category">
                                                <component :is="assetTypeUI.category?.icon(category)"
                                                    class="size-4 text-muted-foreground" />
                                                {{ assetTypeUI.category?.label(category) }}

                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </InputGroup>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </div>
                    </VeeField>

                    <!-- <VeeField name="is_accessory" type="checkbox" v-slot="{ componentField }">
                        <div class="flex items-center gap-2">
                            <Checkbox v-bind="componentField" id="is_accessory" />
                            <Label for="is_accessory" class="text-sm">¿Es accesorio?</Label>
                        </div>
                    </VeeField> -->
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <Button variant="secondary" type="button" @click="onClose">Cancelar</Button>
                    <Button variant="default" type="submit" :disabled="disabledForm">Guardar</Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
import { FieldError } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { type AssetType } from '@/interfaces/assetType.interface';
import { toTypedSchema } from '@vee-validate/zod';
import { File, Tablet } from 'lucide-vue-next';
import { Field as VeeField, useForm } from 'vee-validate';
import { computed, watch } from 'vue';
import { z } from 'zod';

import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Select, SelectContent, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useApp } from '@/composables/useApp';
import { AssetTypeDocCategory, assetTypeUI } from '@/interfaces/assetType.interface';
import { isEqual } from '@/lib/utils';
import { router } from '@inertiajs/core';


const open = defineModel<boolean>('open');

const currentType = defineModel('currentType', {
    type: Object as () => AssetType | null,
    required: false,
    default: null,
});

const { assetTypes, isLoading } = useApp();

const schema = toTypedSchema(z.object({
    name: z.string({
        required_error: 'El nombre es obligatorio',
        invalid_type_error: 'El nombre debe ser una cadena de texto',
    }).min(2, 'El nombre es obligatorio'),
    // is_accessory: z.boolean().optional().default(false),
    doc_category: z.nativeEnum(AssetTypeDocCategory),
}).refine((data) => {
    const duplicate = assetTypes.value.find(type =>
        type.name.trim().toLowerCase() === data.name.trim().toLowerCase() &&
        type.id !== currentType.value?.id
    );
    return !duplicate;
}, {
    path: ['name'],
    message: 'Ya existe un tipo con este nombre',
})

);

const initialValues = computed(() => ({
    name: currentType.value?.name || '',
    // is_accessory: !!currentType.value?.is_accessory,
    doc_category: currentType.value?.doc_category || AssetTypeDocCategory.LAPTOP,
}));

const { handleSubmit, setValues, resetForm, values, errors } = useForm({
    validationSchema: schema,
    initialValues: initialValues.value,
});
type FormValues = typeof values;

const disabledForm = computed(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || isEqual(values, initialValues.value);
});


watch(currentType, (val) => {
    if (val) {
        setValues(initialValues.value);
    } else {
        resetForm();
    }
}, { immediate: true });


function onClose() {
    currentType.value = null;
    open.value = false;
    resetForm();
}

function onSubmit(values: FormValues) {
    if (currentType.value) {
        // Editar tipo existente
        router.put(`/settings/asset-types/${currentType.value.id}`, values, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                router.replaceProp('types', (types: AssetType[]) => {
                    return types.map(type => type.id === currentType.value?.id ? { ...type, ...values } : type);
                });
                onClose();
            }
        });
    } else {
        // Crear nuevo tipo
        router.post('/settings/asset-types', values, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                router.replaceProp('types', (types: AssetType[]) => [...types, flash.assetType]);
                onClose();
            }
        });
    }
}
</script>
