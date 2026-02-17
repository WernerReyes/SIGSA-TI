<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            open = false;
            asset = null;
        }
    }">
        <DialogContent class="max-h-screen sm:max-w-2xl overflow-y-auto">
            <DialogHeader class="space-y-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                            Cambiar Estado de Activo
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            AST-{{ asset?.id }}
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <div class="space-y-6 py-6">
                <!-- Información del activo actual -->
                <div v-if="asset"
                    class="rounded-lg bg-gray-50 dark:bg-gray-800/50 p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado Actual</p>
                            <div class="mt-2">
                                <Badge :class="assetStatusOptions[asset.status]?.bg || 'bg-gray-100'">
                                    {{ assetStatusOptions[asset.status]?.label || asset.status }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selector de nuevo estado -->
                <VeeField name="status" v-slot="{ componentField, errors }">
                    <Field>
                        <FieldLabel for="status" class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Seleccionar Nuevo Estado
                        </FieldLabel>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Elige el estado al que deseas cambiar este activo
                        </p>

                        <SelectFilters :items="Object.values(assetStatusOptions).filter(
                            (status) => status.value !== AssetStatus.ASSIGNED
                        )" :show-selected-focus="false" :show-refresh="false" :label="'Selecciona un nuevo estado'"
                            item-label="label" item-value="value" selected-as-label
                            :default-value="componentField.modelValue"
                            @select="(value) => setFieldValue('status', value)" filter-placeholder="Buscar estado..."
                            empty-text="No se encontraron estados">
                            <template #item="{ item }">
                                <Badge :class="item.bg">
                                    <component :is="item.icon" />
                                    {{ item.label }}
                                </Badge>
                            </template>
                        </SelectFilters>
                        <FieldError v-if="errors.length" :errors="errors" class="mt-2" />
                    </Field>
                </VeeField>
            </div>

            <DialogFooter class="gap-3 sm:gap-3">
                <Button type="button" variant="outline" @click="open = false" :disabled="isLoading"
                    class="flex-1 sm:flex-initial">
                    Cancelar
                </Button>
                <!-- class="flex-1 sm:flex-initial bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700"> -->
                <Button :disabled="disabled" @click=" handleSubmit(handleFormSubmit)()">
                    <Spinner v-if="isLoading" />

                    <Check v-else />
                    {{ isLoading ? 'Cambiando estado...' : 'Cambiar Estado' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { useApp } from '@/composables/useApp';
import { Asset, AssetStatus, assetStatusOptions } from '@/interfaces/asset.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Check } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { watch } from 'vue';
import z from 'zod';
import SelectFilters from '../SelectFilters.vue';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');


const { assetAccessories, isLoading } = useApp();

const formSchema = toTypedSchema(z.object({
    status: z.nativeEnum(AssetStatus, {
        required_error: 'El estado es obligatorio',
        invalid_type_error: 'El estado seleccionado no es válido',
        message: 'El estado seleccionado no es válido'
    })
}));

const { handleSubmit, errors, handleReset, setFieldValue, values } = useForm({
    validationSchema: formSchema,
    initialValues: {
        status: asset?.value?.status
    }

});


const disabled = computed(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || asset.value?.status === values.status;
});

watch(() => asset.value, (newAsset) => {
    if (newAsset) {
        setFieldValue('status', newAsset.status);
    }
    else {
        handleReset();
    }
});


const handleFormSubmit = (values: { status: AssetStatus }) => {
    const only = ['assetsPaginated', 'stats'];
    if (assetAccessories.value.some(acc => acc.id === asset?.value?.id) || asset?.value?.type?.name === TypeName.ACCESSORY) {
        only.push('accessories');
    }

    router.put(
        `/assets/status/${asset?.value?.id}`,
        {
            status: values.status,
        },
        {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            only,
            onFlash: (flash) => {
                if (flash.error) return;
                handleReset();
                asset.value = null;
                open.value = false;
            }
            


        }
    );
};

</script>