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


            <div class="space-y-4 py-4 ">
                <div class="p-3 bg-muted/50 rounded-lg">
                    <p class="text-sm font-medium">{{ asset?.name }}</p>
                    <p class="text-xs text-muted-foreground">AST-{{ asset?.id }}</p>
                </div>

                <form :aria-disabled="true" @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm"
                    class="space-y-3">

                    <FieldGroup>
                        <VeeField name="assigned_to_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="assigned_to_id">Empleado</FieldLabel>

                                <Popover v-model:open="openUserSelect" @update:open="(val) => {
                                    if (val && users.length === 0) {
                                        getAllBasicUserInfo(PageConstKey.ASSETS)

                                    }
                                }">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox" :aria-expanded="openUserSelect"
                                            class="w-full justify-between">
                                            {{
                                                componentField.modelValue
                                                    ? !users.length ? asset?.current_assignment?.assigned_to?.full_name :
                                                        users.find(user => user.staff_id === componentField.modelValue)?.full_name
                                                    : 'Seleccionar empleado'
                                            }}
                                            <ChevronsUpDownIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-full p-0">
                                        <Command>
                                            <CommandInput placeholder="Buscar empleado..." />
                                            <CommandList>
                                                <CommandEmpty>Empleado no encontrado</CommandEmpty>
                                                <CommandGroup>
                                                    <CommandItem v-for="user in users" :key="user.staff_id"
                                                        :value="user.staff_id" @select="() => {
                                                            componentField.onChange(user.staff_id)

                                                        }">

                                                        {{ user.full_name }}
                                                        <CheckIcon v-if="componentField.modelValue === user.staff_id"
                                                            class="ml-auto h-4 w-4" />

                                                    </CommandItem>
                                                </CommandGroup>
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

                                        <Button :disabled="asset?.current_assignment?.parent_assignment_id"
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

                                <Popover v-model:open="openAccessorySelect" @vue:mounted="() => {
                                    if (accessoriesLoaded) return;
                                    getAssetAccessories(() => {

                                        accessoriesLoaded = true;
                                    })
                                }" @update:open="(val) => {
                                    if (val && refetchAccessories) {
                                        getAssetAccessories(() => {
                                            accessoriesLoaded = true;
                                            refetchAccessories = false;
                                        })

                                    }
                                }">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox" :aria-expanded="openAccessorySelect"
                                            class="w-full justify-between">

                                            {{
                                                componentField.modelValue.length
                                                    ? !assetAccessories.length ?
                                                        childrenAssets
                                                            .filter(acc => componentField.modelValue.includes(acc.id))
                                                            .map(acc => acc.name)
                                                            .join(', ') :
                                                        assetAccessories
                                                            .filter(acc => componentField.modelValue.includes(acc.id))
                                                            .map(acc => acc.name)
                                                            .join(', ')
                                                    : 'Seleccionar accesorio'
                                            }}
                                            <ChevronsUpDownIcon class="ml-2 size-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-full p-0">
                                        <Command>
                                            <CommandInput placeholder="Buscar accesorio..." />
                                            <CommandList>
                                                <CommandEmpty>Accesorio no encontrado</CommandEmpty>
                                                <CommandGroup>
                                                    <CommandItem v-for="accessory in assetAccessories"
                                                        :key="accessory.id" :value="accessory.id" @select="() => {
                                                            if (componentField.modelValue.includes(accessory.id)) {
                                                                const filtered = componentField.modelValue.filter(id => id !== accessory.id)
                                                                componentField.onChange(filtered)
                                                                return
                                                            }
                                                            componentField.onChange([...componentField.modelValue, accessory.id])

                                                        }">

                                                        {{ accessory.name }}
                                                        <CheckIcon
                                                            v-if="componentField.modelValue.includes(accessory.id)"
                                                            class="ml-auto size-4" />

                                                    </CommandItem>

                                                    <CommandItem v-if="assetAccessories.length === 0">
                                                        No hay accesorios disponibles
                                                    </CommandItem>
                                                </CommandGroup>
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
                    || Object.keys(errors).length > 0
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
import { getAllUsers } from '@/services/user.service';
import { useAssetStore } from '@/store/useAssetStore';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { CheckIcon, ChevronDownIcon } from 'lucide-vue-next';
import { storeToRefs } from 'pinia';
import { toast } from 'vue-sonner';
import { getAssetAccessories } from '../../services/asset.service';
import { getAllBasicUserInfo } from '../../services/user.service';
import { PageConstKey } from '../../constants/pages.constant';

const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

// const { setAccessoriesCreated } = useAssetStore();
const { refetchAccessories, accessoriesLoaded } = storeToRefs(useAssetStore());

const openUserSelect = ref(false);
const openAccessorySelect = ref(false);
const isSubmitting = ref(false);
const hasRequestedAccessories = ref(false);

const users = computed<User[]>(() => (usePage().props.users || []) as User[]);

const serverAssetAccessories = computed<Asset[]>(() => {
    return (usePage().props.assetAccessories || []) as Asset[];
});

const assetAccessories = computed<Asset[]>(() => {
    return [...childrenAssets.value, ...serverAssetAccessories.value];
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
    router.post('/assets/assign', {
        asset_id: asset.value?.id,
        assigned_to_id: values.assigned_to_id,
        assign_date: values.assign_date,
        comment: type === TypeName.ACCESSORY ? null : values.comment,
        accessories: values.accessories,
    }, {
        only: ['flash', 'assetsPaginated', 'stats', 'assetAccessories'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFinish: () => {
            isSubmitting.value = false;

        },
        onSuccess: (page) => {
            const type = asset.value?.type?.name;
            const assignmentId = (page.props as any).flash.assignment_id as number | undefined;
            refetchAccessories.value = true;
            if (assignmentId) {
                if (type === TypeName.CELL_PHONE) {
                    handleDownloadPhoneCargo(assignmentId);

                } else if (type === TypeName.ACCESSORY) {
                    handleDownloadAccessoryCargo(assignmentId);
                }
                else {
                    handleDownloadCargo(assignmentId);
                }
            }

            open.value = false;
            asset.value = null;
        }
    });
};

const handleDownloadCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-laptop-assignment-doc/${assignmentId}`;
}

const handleDownloadPhoneCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-phone-assignment-doc/${assignmentId}`;
}


const handleDownloadAccessoryCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-accessory-assignment-doc/${assignmentId}`;
}


</script>
