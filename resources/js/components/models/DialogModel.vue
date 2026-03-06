<template>
    <Dialog v-model:open="open" @update:open="onClose">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="flex items-center gap-2 mb-4">
                <Box class="size-6 text-primary" />
                <h2 class="font-bold text-lg">{{ currentModel ? 'Editar modelo' : 'Nuevo modelo' }}</h2>
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
                                    <Box class="h-4 w-4 text-muted-foreground" />
                                </InputGroupAddon>
                                <InputGroupInput placeholder="Ej: ThinkPad T14" v-bind="componentField" />
                            </InputGroup>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </div>
                    </VeeField>

                    <div class="grid gap-4 md:grid-cols-1">
                        <VeeField name="type_id" v-slot="{ componentField, errors }">
                            <div class="space-y-2">
                                <Label :for="componentField.name">Tipo *</Label>
                                <SelectFilters
                                    label="Tipo"
                                    :items="types"
                                    :selected-as-label="true"
                                    :icon="MonitorSmartphone"
                                    :full-width="true"
                                    :show-refresh="false"
                                    :show-selected-focus="false"
                                    item-value="id"
                                    item-label="name"
                                    :default-value="componentField.modelValue"
                                    :disabled="!!props.lockContext && !currentModel"
                                    @select="(selected) => setFieldValue('type_id', selected)"
                                />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </div>
                        </VeeField>

                        <VeeField name="brand_id" v-slot="{ componentField, errors }">
                            <div class="space-y-2">
                                <Label :for="componentField.name">Marca *</Label>
                                <!-- <Select v-model="componentField.modelValue" @update:model-value="componentField['onUpdate:modelValue']">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecciona una marca" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Marcas</SelectLabel>
                                            <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">
                                                {{ brand.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select> -->
                                <!-- <SelectFilter 
                                                v-model="componentField.modelValue"
                                                 :icon="Box" -->

                                                 
                    <SelectFilters label="Marca" :items="availableBrands" :selected-as-label="true" :icon="Tag" :full-width="true" :show-refresh="false"
                        :show-selected-focus="false" item-value="id" item-label="name" 
                        :default-value="componentField.modelValue"
                        :disabled="(!values.type_id) || (!!props.lockContext && !currentModel)"
                         @select="(selected) => setFieldValue('brand_id', selected)"  >
                        <template #item="{ item }">
                            {{ item.name }}
                        </template>
                    </SelectFilters>
                                                
                                <FieldError v-if="errors.length" :errors="errors" />
                            </div>
                        </VeeField>
                    </div>
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
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
import { FieldError } from '@/components/ui/field';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { AssetModel } from '@/interfaces/assetModel.interface';
import { AssetType } from '@/interfaces/assetType.interface';
import { Brand } from '@/interfaces/brand.interface';
import { isEqual } from '@/lib/utils';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { Box, MonitorSmartphone, Tag } from 'lucide-vue-next';
import { Field as VeeField, useForm } from 'vee-validate';
import { computed, watch } from 'vue';
import { z } from 'zod';
import SelectFilters from '../SelectFilters.vue';

const props = defineProps<{
    models: AssetModel[];
    brands: Brand[];
    types: AssetType[];
    initialBrandId?: number | null;
    initialTypeId?: number | null;
    lockContext?: boolean;
}>();

const brands = computed(() => props.brands ?? []);
const types = computed(() => props.types ?? []);
const availableBrands = computed(() => {
    if (!values.type_id) return [];
    return brands.value.filter(brand => brand.type_id === values.type_id);
});

const open = defineModel<boolean>('open');

const currentModel = defineModel('currentModel', {
    type: Object as () => AssetModel | null,
    required: false,
    default: null,
});

const schema = toTypedSchema(z.object({
    name: z.string({
        required_error: 'El nombre es obligatorio',
        invalid_type_error: 'El nombre debe ser una cadena de texto',
    }).min(2, 'El nombre es obligatorio'),
    brand_id: z.number({
        required_error: 'La marca es obligatoria',
        invalid_type_error: 'Selecciona una marca valida',
    }),
    type_id: z.number({
        required_error: 'El tipo es obligatorio',
        invalid_type_error: 'Selecciona un tipo valido',
    }),
}).refine((data) => {
    const duplicate = (props.models || []).find(model =>
        model.name.trim().toLowerCase() === data.name.trim().toLowerCase() &&
        model.brand_id === data.brand_id &&
        model.id !== currentModel.value?.id,
    );

    return !duplicate;
}, {
    path: ['name'],
    message: 'Ya existe un modelo con este nombre',
}));

const initialValues = computed(() => ({
    name: currentModel.value?.name || '',
    brand_id: currentModel.value?.brand_id ?? props.initialBrandId ?? undefined,
    type_id: currentModel.value?.brand?.type_id ?? props.initialTypeId ?? undefined,
}));

const { handleSubmit, setValues, resetForm, values, errors, setFieldValue } = useForm({
    validationSchema: schema,
    initialValues: initialValues.value,
});

type FormValues = typeof values;

const disabledForm = computed(() => {
    return Object.keys(errors.value).length > 0 || isEqual(values, initialValues.value);
});

watch(
    [currentModel, () => props.initialBrandId, () => props.initialTypeId],
    ([value]) => {
        if (value) {
            setValues(initialValues.value);
        } else {
            resetForm({ values: initialValues.value as any });
        }
    },
    { immediate: true },
);

watch(
    () => values.type_id,
    (typeId) => {
        if (!values.brand_id) return;
        const brand = brands.value.find(item => item.id === values.brand_id);
        if (brand && brand.type_id !== typeId) {
            setFieldValue('brand_id', undefined as unknown as number);
        }
    },
);

function onClose() {
    currentModel.value = null;
    open.value = false;
    resetForm();
}

function onSubmit(values: FormValues) {
    const payload = {
        name: values.name,
        brand_id: values.brand_id,
    };

    if (currentModel.value) {
        router.put(`/settings/models/${currentModel.value.id}`, payload, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                router.replaceProp('models', (models: AssetModel[]) => {
                    return models.map((model) => model.id === currentModel.value?.id ? flash.model : model);
                });
                onClose();
            },
        });
        return;
    }

    router.post('/settings/models', payload, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('models', (models: AssetModel[]) => [...models, flash.model]);
            onClose();
        },
    });
}
</script>
