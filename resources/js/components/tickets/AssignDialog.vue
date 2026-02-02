<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            ticket = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-4xl  space-y-4">
            <DialogHeader class="space-y-2 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <!-- <Laptop class="size-6 text-primary" /> -->
                        <!-- <MonitorSmartphone class="size-6 text-primary" /> -->
                        <component
                            :is="assetTypeOp(currentAssetAssignment?.asset?.type?.name)?.icon || MonitorSmartphone"
                            class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-xl font-semibold">Asignar Equipo</DialogTitle>
                        <p class="text-sm text-muted-foreground">Completa los datos para asignar el equipo y registrar
                            su entrega.</p>
                        <p v-if="ticket"
                            class="text-xs text-muted-foreground mt-1 inline-flex gap-2 items-center bg-muted px-2 py-1 rounded-md">
                            <span class="font-mono">TK-{{ ticket?.id }}</span>
                            <span class="text-foreground">·</span>
                            <span class="font-medium line-clamp-1">{{ ticket?.title }}</span>
                        </p>
                    </div>
                </div>
            </DialogHeader>
            <div class="max-h-96 overflow-y-auto">

                <Countdown title="Tiempo para editar la asignación (15 minutos)" @timeout="() => {
                    toast.error('El tiempo para editar esta asignación ha expirado.');
                    canEdit = false;
                }" v-if="canEdit && currentAssetAssignment" :target-date="currentAssetAssignment?.created_at"
                    target-label="Tiempo restante para poder editar la información" :duration="15" />

                <Alert v-else-if="currentAssetAssignment">
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
                                assignment_id: currentAssetAssignment?.id || 0
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
                            <VeeField name="asset_id" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="asset_id">Equipo</FieldLabel>
                                    <SelectFilters data-key="availableAssets" :items="availableAssets"
                                        :show-selected-focus="false" :show-refresh="false"
                                        :disabled="!!currentAssetAssignment?.parent_assignment_id || !canEdit"
                                        :label="currentAssetAssignment?.asset?.name || 'Seleccionar equipo'"
                                        item-label="name" item-value="id" selected-as-label
                                        :default-value="componentField.modelValue"
                                        @select="(value) => setFieldValue('asset_id', +value)"
                                        filter-placeholder="Buscar empleado..." empty-text="No se encontraron equipos">
                                        <template #item="{ item: asset }">
                                            <div class="flex items-center gap-3 py-1">
                                                <component :is="assetTypeOp(asset.type?.name)?.icon"
                                                    class="size-4 shrink-0 text-muted-foreground" />
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold">{{ asset.full_name }}</span>
                                                        <Badge
                                                            class="bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800 text-xs">
                                                            Disponible
                                                        </Badge>
                                                    </div>

                                                </div>
                                            </div>
                                        </template>
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

                                            <Button :disabled="currentAssetAssignment?.parent_assignment_id || !canEdit"
                                                variant="outline" class="w-48 justify-between font-normal">
                                                {{ componentField.modelValue
                                                    ?
                                                    format(componentField.modelValue.toDate(getLocalTimeZone()),
                                                        'dd/MM/yyyy')
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

                            {{ assetAccessories.length }}
                        <FieldGroup v-if="selectedAsset && selectedAsset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="accessories" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="accessories">Accesorios</FieldLabel>

                                    <SelectFilters 
                                     :disabled="!canEdit"
                                    :items="assetAccessories" data-key="accessories"
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



                        <FieldGroup v-if="ticket?.current_asset_assignment?.asset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="comment" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="comment">Observaciones</FieldLabel>
                                    <Textarea 
                                     :disabled="!canEdit"
                                    id="comment" placeholder="Condiciones del equipo, accesorios incluidos..."
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
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0 || !canEdit
                    " type="submit" form="dialogForm" class="min-w-36">
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
import Countdown from '@/components/Countdown.vue';
import { Alert } from '@/components/ui/alert';

import { Textarea } from '@/components/ui/textarea';

import { useAsset } from '@/composables/useAsset';
import { type Asset } from '@/interfaces/asset.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { ChevronDownIcon, LockKeyhole, MonitorSmartphone } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { useApp } from '@/composables/useApp';
import type { Alert as IAlert } from '@/interfaces/alert.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { type Ticket } from '@/interfaces/ticket.interface';
import { addMinutes, format } from 'date-fns';
import { toast } from 'vue-sonner';
import SelectFilters from '../SelectFilters.vue';

import { AlertTitle, AlertDescription } from '@/components/ui/alert';
import { Skeleton } from '@/components/ui/skeleton';
import FileUpload from '../FileUpload.vue';

const ticket = defineModel<Ticket | null>('ticket');
const open = defineModel<boolean>('open');

const page = usePage();
const { availableAssets: availables, assetAccessories: accessories } = useApp();
const { downloadAssignmentDocument } = useAsset();


const currentAssetAssignment = computed<AssetAssignment | null>(() => {
    return page.props?.currentAssignment as AssetAssignment | null;
});

const isSubmitting = ref(false);
const resetUpload = ref(false);
const loadingAssignDocument = ref(false);


const url = computed<string>({
    get: () => {
        const assignDocument = page.props?.assignDocument as
            | { delivery_document: { file_url: string } }
            | undefined;
        return currentAssetAssignment.value?.delivery_document?.file_url || assignDocument?.delivery_document?.file_url || '';
    },
    set: (value: string) => {
        if (currentAssetAssignment.value) {
            currentAssetAssignment.value.delivery_document = {
                ...currentAssetAssignment.value.delivery_document!,
                file_url: value
            }
        }
    }
});


const canEdit = computed({
    get: () => {

        if (!currentAssetAssignment.value) return true;
        const targetDate = currentAssetAssignment.value.created_at;


        return new Date() <= addMinutes(targetDate, 15)
    },
    set: (val: boolean) => {
        return val;
    }
});

const assignmentId = computed<number>(() => {
    return currentAssetAssignment.value?.id || 0;
});


const availableAssets = computed<Asset[]>(() => {
    if (currentAssetAssignment.value) {
       
        return [
            currentAssetAssignment.value.asset!,
            ...availables.value,
        ];
    }
    return availables.value;
});

const assetAccessories = computed<Asset[]>(() => {
    // const accessorie
    if (!accessories.value.length) {
        return [...childrenAssets.value];
    }
    return [...childrenAssets.value, ...accessories.value];
});


const childrenAssets = computed<Asset[]>(() => {
    if (!currentAssetAssignment.value) return [];
    return currentAssetAssignment.value?.children_assignments?.flatMap(ca => ca.asset).filter((asset): asset is Asset => !!asset) || [];
});

const accesoriesOutOfStockAlertsExists = computed<boolean>(() => {
    const alerts = (page.props?.accessoriesOutOfStockAlert || []) as IAlert[];
    return alerts.length > 0;
});

const selectedAsset = computed<Asset | null>(() => {
    if (!values.asset_id) return null;
    return availableAssets.value.find(a => a.id === values.asset_id) || null;
});

const formSchema = toTypedSchema(
    z.object({
        asset_id: z.number({
            message: 'Seleccione un equipo',
        }),
        // assigned_to_id: z.number({ invalid_type_error: 'Seleccione un empleado', required_error: 'Seleccione un empleado' }),
        assign_date: z.instanceof(CalendarDate, {
            message: 'Seleccione una fecha de entrega',
        }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
        comment: z.string().optional(),
        accessories: z.array(z.number()).optional(),
    })
);

const { handleSubmit, errors, setValues, setFieldValue, values } = useForm({
    initialValues: {
        asset_id: undefined,
        assign_date: today(getLocalTimeZone()),
        comment: '',
        accessories: [],
    },
    validationSchema: formSchema,
    validateOnMount: false,

});

watch(currentAssetAssignment, (assignment) => {
    if (assignment) {
        setValues({
            asset_id: assignment.asset_id,
            assign_date: parseDate(assignment.assigned_at.split('T')[0]),
            comment: assignment.comment || '',
            accessories: assignment.children_assignments?.map(ca => ca.asset_id) || [],
        });
    } else {
        setValues({
            asset_id: undefined,
            assign_date: today(getLocalTimeZone()),
            comment: '',
            accessories: [],
        });
    }
}, { immediate: true });


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    const accessoriesIds = values.accessories || [];
    const type = selectedAsset.value?.type?.name || null;

    const accessories = assetAccessories.value.filter(acc => accessoriesIds.includes(acc.id)) as Asset[];

    const only: string[] = [];
    if ([TypeName.LAPTOP, TypeName.CELL_PHONE].includes(type as TypeName)) {
        // only.push('accessories');
        const includeCharger = accessories.some(acc => acc.name.toLowerCase().trim().includes('cargador'));
        if (!includeCharger) {
            toast.error('Debe incluir el cargador en los accesorios del equipo.');
            isSubmitting.value = false;
            return;
        }
    }


    

    router.post('/assets/assign', {
        asset_id: values.asset_id,
        ticket_id: ticket.value?.id,
        responsible_id: ticket.value?.responsible_id,
        assigned_to_id: ticket.value?.requester_id,
        assign_date: values.assign_date.toDateString(),
        comment: type === TypeName.ACCESSORY ? null : values.comment,
        accessories: values.accessories,
    }, {
        only: ['availableAssets', 'accessories'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        onFlash: (flash) => {
            if (flash.assignment_id) {
                const type = ticket.value?.current_asset_assignment?.asset?.type?.name || null;
                if (type) {
                    downloadAssignmentDocument(flash.assignment_id as number, type);
                }
            }

            if (flash.success) {
                open.value = false;
                ticket.value = null;
                if (!only.length) {

                    router.replaceProp('availableAssets', (availables: Asset[]) => {
                        return availables.filter(a => a.id !== values.asset_id);
                    });
                }
            }

            if (flash.alert_triggered && !accesoriesOutOfStockAlertsExists.value) {
                toast.success('Se ha enviado una alerta de stock bajo para los accesorios seleccionados.');
            }
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
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,


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
    if (!modelValue?.length) return 'Seleccionar accesorio';

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
