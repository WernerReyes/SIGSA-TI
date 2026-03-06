<template>
    <Dialog v-model:open="open" @update:open="onClose">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="flex items-center gap-2 mb-4">
                <Tag class="size-6 text-primary" />
                <h2 class="font-bold text-lg">{{ currentBrand ? 'Editar marca' : 'Nueva marca' }}</h2>
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
                                    <Tag class="h-4 w-4 text-muted-foreground" />
                                </InputGroupAddon>
                                <InputGroupInput placeholder="Ej: Lenovo" v-bind="componentField" />
                            </InputGroup>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </div>
                    </VeeField>

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
                                @select="(selected) => setFieldValue('type_id', selected)"
                            />
                            <FieldError v-if="errors.length" :errors="errors" />
                        </div>
                    </VeeField>
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
import { useApp } from '@/composables/useApp';
import { AssetType } from '@/interfaces/assetType.interface';
import { Brand } from '@/interfaces/brand.interface';
import { isEqual } from '@/lib/utils';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { MonitorSmartphone, Tag } from 'lucide-vue-next';
import { Field as VeeField, useForm } from 'vee-validate';
import { computed, watch } from 'vue';
import { z } from 'zod';
import SelectFilters from '../SelectFilters.vue';

const props = defineProps<{
    brands: Brand[];
    types: AssetType[];
}>();


const open = defineModel<boolean>('open');

const currentBrand = defineModel('currentBrand', {
    type: Object as () => Brand | null,
    required: false,
    default: null,
});

const { isLoading } = useApp();

const schema = toTypedSchema(z.object({
    name: z.string({
        required_error: 'El nombre es obligatorio',
        invalid_type_error: 'El nombre debe ser una cadena de texto',
    }).min(2, 'El nombre es obligatorio'),
    type_id: z.number({
        required_error: 'El tipo es obligatorio',
        invalid_type_error: 'Selecciona un tipo valido',
    }),
}).refine((data) => {
    const duplicate = (props.brands || []).find(brand =>
        brand.name.trim().toLowerCase() === data.name.trim().toLowerCase() &&
        brand.type_id === data.type_id &&
        brand.id !== currentBrand.value?.id,
    );

    return !duplicate;
}, {
    path: ['name'],
    message: 'Ya existe una marca con este nombre',
}));

const initialValues = computed(() => ({
    name: currentBrand.value?.name || '',
    type_id: currentBrand.value?.type_id ?? undefined,
}));

const types = computed(() => props.types ?? []);

const { handleSubmit, setValues, resetForm, values, errors, setFieldValue } = useForm({
    validationSchema: schema,
    initialValues: initialValues.value,
});

type FormValues = typeof values;

const disabledForm = computed(() => {
    return Object.keys(errors.value).length > 0 || isEqual(values, initialValues.value) || isLoading.value;
});

watch(currentBrand, (value) => {
    if (value) {
        setValues(initialValues.value);
    } else {
        resetForm();
    }
}, { immediate: true });

function onClose() {
    currentBrand.value = null;
    open.value = false;
    resetForm();
}

function onSubmit(values: FormValues) {
    if (currentBrand.value) {
        router.put(`/settings/brands/${currentBrand.value.id}`, values, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                router.replaceProp('brands', (brands: Brand[]) => {
                    return brands.map((brand) => brand.id === currentBrand.value?.id ? flash.brand : brand);
                });
                onClose();
            },
        });
        return;
    }

    router.post('/settings/brands', values, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('brands', (brands: Brand[]) => [flash.brand, ...brands]);
            onClose();
        },
    });
}
</script>
