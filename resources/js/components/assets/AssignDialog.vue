<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-4xl  space-y-4">
            <DialogHeader class="space-y-2 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <!-- <Laptop class="size-6 text-primary" /> -->
                        <component :is="assetTypeOp(asset?.type?.name).icon" class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-xl font-semibold">Asignar {{ asset?.type?.name }}</DialogTitle>
                        <p class="text-sm text-muted-foreground">Completa los datos para asignar el activo y registrar
                            su entrega.</p>
                        <p v-if="asset"
                            class="text-xs text-muted-foreground mt-1 inline-flex gap-2 items-center bg-muted px-2 py-1 rounded-md">
                            <span class="font-mono">AST-{{ asset?.id }}</span>
                            <span class="text-foreground">·</span>
                            <span class="font-medium line-clamp-1">{{ asset?.name }}</span>
                        </p>
                    </div>
                </div>
            </DialogHeader>
            <div class="max-h-96 overflow-y-auto">

                <Countdown title="Tiempo para editar la asignación (15 minutos)" @timeout="() => {
                    toast.error('El tiempo para editar esta asignación ha expirado.');
                    canEdit = false;
                }" v-if="canEdit && asset?.current_assignment" :target-date="asset?.current_assignment?.created_at"
                    target-label="Tiempo restante para poder editar la información" :duration="15" />

                <Alert v-else-if="asset?.current_assignment">
                    <LockKeyhole class="size-4" />
                    <AlertTitle>No es posible editar esta asignación</AlertTitle>
                    <AlertDescription>
                        Han pasado más de 15 minutos desde que se asignó este equipo.
                    </AlertDescription>

                </Alert>

                <div class="space-y-5 py-2">
                    <div v-if="!canEdit" @vue:mounted="() => {
                        loadingAssignDocument = true;
                        router.reload({
                            data: {
                                assignment_id: asset?.current_assignment?.id || 0
                            },
                            only: ['assignDocument'],

                            preserveUrl: true,
                            onFinish: () => {
                                loadingAssignDocument = false;
                            }
                        });
                    }" class="p-4 rounded-xl border bg-muted/40">

                        <template v-if="loadingAssignDocument">
                            <Skeleton class="h-4 w-3/4 mb-2" />
                            <Skeleton class="h-4 w-1/2" />
                        </template>

                        <template v-else>
                            <p class="mb-2 text-sm font-medium">Subir documento de entrega firmado</p>
                            <p class="text-xs text-muted-foreground mb-3">Solo PDF o imagen, máx. 2MB.</p>
                            <FileUpload :current-url="url" @error="(msg) => toast.error(msg)"
                                accept="application/pdf,image/*" v-model:reset="resetUpload"
                                @update:file="handleUploadSignedDocument($event)" />
                        </template>
                    </div>

                    <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm"
                        class="space-y-4 p-4 rounded-xl border bg-card/70">

                        <FieldGroup>
                            <VeeField name="assigned_to_id" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="assigned_to_id">Empleado</FieldLabel>
                                    <SelectFilters data-key="users" :items="users" :show-selected-focus="false"
                                        :show-refresh="false"
                                        :disabled="!!asset?.current_assignment?.parent_assignment_id || !canEdit"
                                        :label="asset?.current_assignment?.assigned_to?.full_name || 'Seleccionar empleado'"
                                        item-label="full_name" item-value="staff_id" selected-as-label
                                        :default-value="componentField.modelValue"
                                        @select="(value) => setFieldValue('assigned_to_id', +value)"
                                        filter-placeholder="Buscar empleado..."
                                        empty-text="No se encontraron empleados">
                                    </SelectFilters>

                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>


                        <FieldGroup>
                            <VeeField name="assign_date" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="assign_date">Fecha de Entrega</FieldLabel>
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>

                                            <Button
                                                :disabled="asset?.current_assignment?.parent_assignment_id || !canEdit"
                                                variant="outline" class="w-48 justify-between font-normal">
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


                        <FieldGroup v-if="asset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="accessories" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="accessories">Accesorios</FieldLabel>

                                    <SelectFilters :items="assetAccessories" data-key="accessories"
                                        :show-selected-focus="false" :show-refresh="false"
                                        :label="selectLabels(componentField.modelValue)" item-label="name"
                                        item-value="id" :multiple="true" selected-as-label
                                        :default-value="componentField.modelValue"
                                        @select="(values) => setFieldValue('accessories', values)"
                                        filter-placeholder="Buscar accesorio..."
                                        empty-text="No se encontraron accesorios">
                                    </SelectFilters>

                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>



                        <FieldGroup v-if="asset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="comment" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="comment">Observaciones</FieldLabel>
                                    <Textarea id="comment" placeholder="Condiciones del equipo, accesorios incluidos..."
                                        rows="3" v-bind="componentField" />
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </form>
                </div>

            </div>

            <DialogFooter class="gap-2 pt-2 border-t">
                <Button variant="outline" @click="open = false" :disabled="isSubmitting">Cancelar</Button>
                <Button :disabled="disabledForm" type="submit" form="dialogForm" class="min-w-36">
                    <Spinner v-if="isSubmitting" />
                    {{ isSubmitting ? 'Asignando...' : 'Asignar Equipo' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>

<script setup lang="ts">

import { useForm, Field as VeeField } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import z from 'zod';

import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel
} from '@/components/ui/field';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';


import { Textarea } from '@/components/ui/textarea';

import { useAsset } from '@/composables/useAsset';
import { type Asset } from '@/interfaces/asset.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { ChevronDownIcon, LockKeyhole } from 'lucide-vue-next';

import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Skeleton } from '@/components/ui/skeleton';
import { useApp } from '@/composables/useApp';
import type { Alert as IAlert } from '@/interfaces/alert.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { addMinutes } from 'date-fns';
import { toast } from 'vue-sonner';
import Countdown from '../Countdown.vue';
import FileUpload from '../FileUpload.vue';
import SelectFilters from '../SelectFilters.vue';
import { isEqual } from '@/lib/utils';

const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const page = usePage();
const { users, assetAccessories: accessories } = useApp();
const { downloadAssignmentDocument } = useAsset();

const isSubmitting = ref(false);
const resetUpload = ref(false);
const loadingAssignDocument = ref(false);


const url = computed<string>({
    get: () => {
        const assignDocument = page.props?.assignDocument as
            | { delivery_document: { file_url: string } }
            | undefined;
        return asset.value?.current_assignment?.delivery_document?.file_url || assignDocument?.delivery_document?.file_url || '';
    },
    set: (value: string) => {
        if (asset.value && asset.value.current_assignment) {
            asset.value.current_assignment.delivery_document = {
                ...asset.value.current_assignment.delivery_document!,
                file_url: value
            }
        }
    }
});


const disabledForm = computed(() => {
    return isSubmitting.value || Object.keys(errors.value).length > 0 || !canEdit.value || isEqual(values, initialValues.value);
});

const canEdit = computed({
    get: () => {
        if (!asset.value?.current_assignment) return true;
        const targetDate = asset.value.current_assignment.created_at;


        return new Date() <= addMinutes(targetDate, 15)
    },
    set: (val: boolean) => {
        return val;
    }
});

const assignmentId = computed<number>(() => {
    return asset.value?.current_assignment?.id || 0;
});

const assetAccessories = computed<Asset[]>(() => {
    // const accessorie
    if (!accessories.value.length) {
        return [...childrenAssets.value];
    }
    return [...childrenAssets.value, ...accessories.value];
});

const assign = computed<AssetAssignment | null>(() => {
    return asset.value?.current_assignment || null;
});

const childrenAssets = computed<Asset[]>(() => {
    if (!assign.value) return [];
    return assign.value.children_assignments?.flatMap(ca => ca.asset).filter((asset): asset is Asset => !!asset) || [];
});

const accesoriesOutOfStockAlertsExists = computed<boolean>(() => {
    const alerts = (page.props?.accessoriesOutOfStockAlert || []) as IAlert[];
    return alerts.length > 0;
});

const formSchema = toTypedSchema(
    z.object({
        assigned_to_id: z.number({ invalid_type_error: 'Seleccione un empleado', required_error: 'Seleccione un empleado' }),
        assign_date: z.instanceof(CalendarDate, {
            message: 'Seleccione una fecha de entrega',
        }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
        comment: z.string().optional(),
        accessories: z.array(z.number()).optional(),
    })
);


const initialValues = computed(() => {
    return {
        assigned_to_id: assign.value?.assigned_to_id,
        assign_date: assign.value ? parseDate(assign.value.assigned_at.split('T')[0]) : today(getLocalTimeZone()),
        comment: assign.value?.comment || '',
        accessories: assign.value?.children_assignments?.map(ca => ca.asset_id) ||
            [],

    }
});


const { handleSubmit, errors, setValues, setFieldValue, resetForm, values } = useForm({
    initialValues: initialValues.value,
    validationSchema: formSchema,
    validateOnMount: false,

});

watch(assign, (assignment) => {
    if (assignment) {
        setValues(initialValues.value);
    } else {
        resetForm();
    }
}, { immediate: true });


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    const accessoriesIds = values.accessories || [];
    const type = asset.value?.type?.name;

    const accessories = assetAccessories.value.filter(acc => accessoriesIds.includes(acc.id)) as Asset[];
    if ([TypeName.LAPTOP, TypeName.CELL_PHONE].includes(type as TypeName)) {
        const includeCharger = accessories.some(acc => acc.name.toLowerCase().trim().includes('cargador'));
        if (!includeCharger) {
            toast.error('Debe incluir el cargador en los accesorios del equipo.');
            isSubmitting.value = false;
            return;
        }
    }

    const only = ['assetsPaginated', 'stats'];
    if (type === TypeName.ACCESSORY || accessories.find(acc => accessoriesIds.includes(acc.id))) {
        only.push('accessories');
    }

    router.post('/assets/assign', {
        asset_id: asset.value?.id,
        assigned_to_id: values.assigned_to_id,
        assign_date: values.assign_date.toDateString(),
        comment: type === TypeName.ACCESSORY ? null : values.comment,
        accessories: values.accessories,
    }, {
        only,
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        onFlash: (flash) => {
            if (flash.error) return;
            if (flash.assignment_id) {
                const type = asset.value?.type?.name;
                if (type) {
                    downloadAssignmentDocument(flash.assignment_id as number, type);
                }
            }

            if (flash.alert_triggered && !accesoriesOutOfStockAlertsExists.value) {
                router.reload({
                    only: ['accessoriesOutOfStockAlert'],
                });
            }

            open.value = false;
            asset.value = null;
        },


        onFinish: () => {
            isSubmitting.value = false;

        },
    });
};

const handleUploadSignedDocument = (file: File) => {
    router.post(`/assets/delivery-records/${assignmentId.value}`, {
        file: file,
        type: DeliveryRecordType.ASSIGNMENT,
    }, {
        only: [],
        onFlash(flash) {
            const fileUrl = flash.file_url as string;
            url.value = fileUrl;
        },

        onFinish: () => {
            resetUpload.value = true;
        }

    });

};

const selectLabels = (modelValue: Array<number>) => {
    if (!modelValue.length) return 'Seleccionar accesorio';

    let accesories = assetAccessories.value;
    if (!accesories.length) {
        accesories = childrenAssets.value;
    }

    if (accesories.length > 3 && modelValue.length > 3) {
        return `${modelValue.length} accesorios seleccionados`;
    }

    return accesories
        .filter(acc => modelValue.includes(acc.id))
        .map(acc => acc.name)
        .join(', ');
}




</script>

