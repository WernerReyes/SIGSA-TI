<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Devolver Equipo</DialogTitle>
            </DialogHeader>


            <div class="space-y-4 py-4 ">
                <div class="p-3 bg-muted/50 rounded-lg">
                    <p class="text-sm font-medium">{{ asset?.name }}</p>
                    <p class="text-xs text-muted-foreground">AST-{{ asset?.id }}</p>

                    <div class="flex gap-2 flex-wrap">

                        <div v-if="asset?.assignment?.responsible_id" class="flex flex-col w-fit gap-2 mt-2 mx-auto">

                            <div class="flex items-center gap-2">

                                <Button variant="secondary" size="sm" @click="handleDownloadCargo">
                                    <Download /> Cargo
                                    <MonitorSmartphone />
                                </Button>



                            </div>

                            <div class="w-48 mx-auto">

                                <FileUpload label="Subir documento firmado" accept="application/pdf" @error="(msg) => {
                                    console.log('Upload error:', msg);
                                }" @update:file="(file) => {
                                    console.log('Uploaded file:', file);
                                }" />

                            </div>

                        </div>

                    </div>



                </div>


                <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm" class="space-y-3">

                    <FieldGroup>
                        <VeeField name="responsible_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="responsible_id">Responsable</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger id="responsible_id">
                                        <SelectValue placeholder="Seleccionar responsable" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Responsable (TI)</SelectLabel>

                                            <SelectItem v-for="user in TIUsers" :key="user.staff_id"
                                                :value="user.staff_id">
                                                {{ user.full_name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>


                    <FieldGroup>
                        <VeeField name="returned_date" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="returned_date">Fecha de Devolución</FieldLabel>
                                <Popover v-slot="{ close }">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" class="w-48 justify-between font-normal">
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



                    <FieldGroup>
                        <VeeField name="return_comment" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="return_comment">Observaciones</FieldLabel>
                                <Textarea id="return_comment" placeholder="Condiciones del equipo, accesorios incluidos..."
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
                    {{ isSubmitting ? 'Devolviendo...' : 'Devolver Equipo' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->
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
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { Textarea } from '@/components/ui/textarea';
import { type Asset } from '@/interfaces/asset.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { type User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { isBefore, parseISO } from 'date-fns';
import { ChevronDownIcon, Download, MonitorSmartphone } from 'lucide-vue-next';
import FileUpload from '../FileUpload.vue';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const TIUsers = computed<User[]>(() => {
    return (usePage().props.TIUsers || []) as User[];
});

const assign = computed<AssetAssignment | null>(() => {
    return asset.value?.current_assignment || null;
});

const isSubmitting = ref(false);

const formSchema = toTypedSchema(
    z.object({
        responsible_id: z.number({ invalid_type_error: 'Seleccione un responsable', required_error: 'Seleccione un responsable' }),
        returned_date: z.instanceof(CalendarDate, {
            message: 'Seleccione una fecha de entrega',
        }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())).refine((date) => {
            const assignedAtDate = assign.value ? parseISO(assign.value.assigned_at.split('T')[0]) : null;

            return isBefore(assignedAtDate!, date);
        }, {
            message: 'La fecha de devolución debe ser posterior a la fecha de asignación y no puede ser una fecha pasada',
        }),
        return_comment: z.string().optional(),
    })
);

const { handleSubmit, errors, setValues } = useForm({
    initialValues: {
        responsible_id: undefined,
        returned_date: today(getLocalTimeZone()),
        return_comment: '',
    },
    validationSchema: formSchema,
    validateOnMount: false,

});

// watch(assign, (assignment) => {
//     if (assignment) {
//         setValues({
//             responsible_id: assignment.responsible_id || undefined,
//             // returned_date: parseDate(assignment?.returned_at?.split('T')[0] || '') ,
//             // comment: assignment.return_comment || '',
//         });
//     }
// }, { immediate: true });


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    
    router.post(`/assets/devolve/${assign.value?.id}`, {
        responsible_id: values.responsible_id,
        return_date: values.returned_date,
        return_comment: values.return_comment,
    }, {
        onFinish: () => {
            isSubmitting.value = false;
        },
        onSuccess: () => {
            open.value = false;
            asset.value = null;
        }
    });

    // router.post('/assets/assign', {
    //     asset_id: asset.value?.id,
    //     responsible_id: values.responsible_id,
    //     returned_date: values.returned_date,
    //     comment: values.comment,
    // }, {
    //     onFinish: () => {
    //         isSubmitting.value = false;
    //     },
    //     onSuccess: () => {
    //         open.value = false;
    //         asset.value = null;
    //     }
    // });
};

const handleDownloadCargo = () => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-laptop-assignment-doc/${asset.value.id}`;
}

const handleDownloadPhoneCargo = () => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-phone-assignment-doc/${asset.value.id}`;
}
</script>
