<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Asignar Equipo</DialogTitle>

            </DialogHeader>

            <Countdown @timeout="() => {
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

            <div class="space-y-4 py-4 ">
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
                }" class="p-3 bg-muted/50 rounded-lg">

                    <template v-if="loadingAssignDocument">
                        <Skeleton class="h-4 w-3/4 mb-2" />
                        <Skeleton class="h-4 w-1/2" />
                    </template>

                    <template v-else>
                        <p class="mb-2 text-sm">Subir documento de entrega firmado:</p>
                        <FileUpload :current-url="url" @error="(msg) => toast.error(msg)"
                            accept="application/pdf,image/*" :reset="resetUpload"
                            @update:file="handleUploadSignedDocument($event)" />
                    </template>
                    <!-- </WhenVisible> -->

                </div>

                <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm" class="space-y-3">

                    <FieldGroup>
                        <VeeField name="assigned_to_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="assigned_to_id">Empleado</FieldLabel>

                                <Popover v-model:open="openUserSelect">
                                    <PopoverTrigger as-child>
                                        <Button :disabled="!canEdit" variant="outline" role="combobox"
                                            :aria-expanded="openUserSelect" class="w-full justify-between">
                                            {{
                                                componentField.modelValue
                                                    ? !users.length ? asset?.current_assignment?.assigned_to?.full_name
                                                        :
                                                        users.find(user => user.staff_id ===
                                                            componentField.modelValue)?.full_name
                                                    : 'Seleccionar empleado'
                                            }}
                                            <ChevronsUpDownIcon class="ml-2 size-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-full p-0">
                                        <Command>
                                            <CommandInput placeholder="Buscar empleado..." />
                                            <CommandList>
                                                <WhenVisible data="users">
                                                    <template #fallback>
                                                        <CommandGroup>
                                                            <CommandItem v-for="n in 5" :key="n" value="loading">
                                                                <Skeleton class="h-4 w-full" />
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </template>

                                                    <CommandEmpty>Empleado no encontrado</CommandEmpty>
                                                    <CommandGroup>
                                                        <CommandItem v-for="user in users" :key="user.staff_id"
                                                            :value="user.staff_id" @select="() => {
                                                                componentField.onChange(user.staff_id)
                                                            }">

                                                            {{ user.full_name }}
                                                            <CheckIcon
                                                                v-if="componentField.modelValue === user.staff_id"
                                                                class="ml-auto h-4 w-4" />

                                                        </CommandItem>
                                                    </CommandGroup>
                                                </WhenVisible>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>

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

                                        <Button :disabled="asset?.current_assignment?.parent_assignment_id || !canEdit"
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

                                <Popover v-model:open="openAccessorySelect">
                                    <PopoverTrigger as-child>
                                        <Button :disabled="!canEdit" variant="outline" role="combobox"
                                            :aria-expanded="openAccessorySelect" class="w-full justify-between">
                                            {{ selectLabels(componentField.modelValue) }}
                                            <ChevronsUpDownIcon class="ml-2 size-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="m-w-full p-0">
                                        <Command>
                                            <CommandInput placeholder="Buscar accesorio..." />
                                            <CommandList>
                                                <WhenVisible data="accessories">
                                                    <template #fallback>
                                                        <CommandGroup>
                                                            <CommandItem v-for="n in 5" :key="n" value="loading">
                                                                <Skeleton class="h-4 w-full" />
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </template>

                                                    <CommandEmpty>Accesorio no encontrado</CommandEmpty>
                                                    <CommandGroup>
                                                        <CommandItem v-for="accessory in assetAccessories"
                                                            :key="accessory.id" :value="accessory.id" @select="() => {
                                                                if (componentField.modelValue.includes(accessory.id)) {
                                                                    const filtered = componentField.modelValue.filter((id:number) => id !== accessory.id)
                                                                    componentField.onChange(filtered)
                                                                    return
                                                                }
                                                                componentField.onChange([...componentField.modelValue, accessory.id])

                                                            }">

                                                            {{ accessory.name }} ({{ accessory.brand }} - {{
                                                                accessory.model }})
                                                            <CheckIcon
                                                                v-if="componentField.modelValue.includes(accessory.id)"
                                                                class="ml-auto size-4" />

                                                        </CommandItem>

                                                        <CommandItem v-if="assetAccessories.length === 0" value="empty">
                                                            No hay accesorios disponibles
                                                        </CommandItem>
                                                    </CommandGroup>
                                                </WhenVisible>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>

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


            <DialogFooter>

                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0 || !canEdit
                    " type="submit" form="dialogForm">
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

import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';

import { Textarea } from '@/components/ui/textarea';

import { type Asset } from '@/interfaces/asset.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { type User } from '@/interfaces/user.interface';
import { useAsset } from '@/composables/useAsset';
import { router, usePage, WhenVisible } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { CheckIcon, ChevronDownIcon, LockKeyhole } from 'lucide-vue-next';

import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Skeleton } from '@/components/ui/skeleton';
import { addMinutes } from 'date-fns';
import { toast } from 'vue-sonner';
import Countdown from '../Countdown.vue';
import FileUpload from '../FileUpload.vue';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';

const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const page = usePage();
const { downloadAssignmentDocument } = useAsset();

const openUserSelect = ref(false);
const openAccessorySelect = ref(false);
const isSubmitting = ref(false);
const resetUpload = ref(false);
const loadingAssignDocument = ref(false);


const url = computed<string>({
    get: () => {
        const assignDocument = page.props?.assignDocument as
            | { delivery_document: { file_url: string } }
            | undefined;
        return asset.value?.current_assignment?.delivery_document?.file_url || assignDocument?.delivery_document.file_url || '';
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

const users = computed<User[]>(() => {
    return (page.props?.users || []) as User[];
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
    const accessories = (page.props?.accessories || []) as Asset[];
    return [...childrenAssets.value, ...accessories];
});

const assign = computed<AssetAssignment | null>(() => {
    return asset.value?.current_assignment || null;
});

const childrenAssets = computed<Asset[]>(() => {
    if (!assign.value) return [];
    return assign.value.children_assignments?.flatMap(ca => ca.asset).filter((asset): asset is Asset => !!asset) || [];
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

const { handleSubmit, errors, setValues } = useForm({
    initialValues: {
        assigned_to_id: undefined,
        assign_date: today(getLocalTimeZone()),
        comment: '',
        accessories: [],
    },
    validationSchema: formSchema,
    validateOnMount: false,

});

watch(assign, (assignment) => {
    if (assignment) {
        setValues({
            assigned_to_id: assignment.assigned_to_id,
            assign_date: parseDate(assignment.assigned_at.split('T')[0]),
            comment: assignment.comment || '',
            accessories: assignment.children_assignments?.map(ca => ca.asset_id) || [],
        });
    } else {
        setValues({
            assigned_to_id: undefined,
            assign_date: today(getLocalTimeZone()),
            comment: '',
            accessories: [],
        });
    }
}, { immediate: true });


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    const accessoriesIds = values.accessories || [];
    const type = asset.value?.type?.name;
    if ([TypeName.LAPTOP, TypeName.CELL_PHONE].includes(type as TypeName)) {
        const accessories = assetAccessories.value.filter(acc => accessoriesIds.includes(acc.id)) as Asset[];
        const includeCharger = accessories.some(acc => acc.name.toLowerCase().trim().includes('cargador'));
        if (!includeCharger) {
            toast.error('Debe incluir el cargador en los accesorios del equipo.');
            isSubmitting.value = false;
            return;
        }
    }

    const only = ['assetsPaginated', 'stats'];
    if (type === TypeName.ACCESSORY) {
        only.push('accessories');
    }

    router.post('/assets/assign', {
        asset_id: asset.value?.id,
        assigned_to_id: values.assigned_to_id,
        assign_date: values.assign_date,
        comment: type === TypeName.ACCESSORY ? null : values.comment,
        accessories: values.accessories,
    }, {
        only,
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        onFlash: (flash) => {
            if (flash.assignment_id) {
                const type = asset.value?.type?.name;
                if (type) {
                    downloadAssignmentDocument(flash.assignment_id as number, type);
                }
            }
        },
        onSuccess: () => {
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
