<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Asignar Activo</DialogTitle>
            </DialogHeader>


            <div class="space-y-4 py-4">
                <div class="p-3 bg-muted/50 rounded-lg">
                    <p class="text-sm font-medium">{{ asset?.name }}</p>
                    <p class="text-xs text-muted-foreground">AST-{{ asset?.id }}</p>
                </div>

                <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm" class="space-y-3">

                    <FieldGroup>
                        <VeeField name="assigned_to_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="assigned_to_id">Empleado</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger id="assigned_to_id">
                                        <SelectValue placeholder="Seleccionar empleado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Empleados</SelectLabel>

                                            <SelectItem v-for="user in users" :key="user.staff_id"
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
                        <VeeField name="assign_date" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="assign_date">Fecha de Entrega</FieldLabel>
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
                    {{ isSubmitting ? 'Asignando...' : 'Asignar Activo' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->
</template>

<script setup lang="ts">

import { useForm, Field as VeeField } from 'vee-validate';

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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';

import { type Asset } from '@/interfaces/asset.interface';
import { computed, ref, watch } from 'vue';
import { User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { ChevronDownIcon } from 'lucide-vue-next';
import { Textarea } from '@/components/ui/textarea';
import { toTypedSchema } from '@vee-validate/zod';
import z from 'zod';
import { getCurrAssign } from '@/lib/utils';
import { AssetAssignment } from '@/interfaces/assetAssignment.interface';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const users = computed<User[]>(() => (usePage().props.users || []) as User[]);

const assign = computed<AssetAssignment | null>(() => {
    return getCurrAssign(asset.value?.assignments || []) || null;
});

const isSubmitting = ref(false);

const formSchema = toTypedSchema(
    z.object({
        assigned_to_id: z.number({ invalid_type_error: 'Seleccione un empleado', required_error: 'Seleccione un empleado' }),
        assign_date: z.instanceof(CalendarDate).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
        comment: z.string().optional(),
    })
);

const { handleSubmit, errors, setValues } = useForm({
    initialValues: {
        assigned_to_id: undefined,
        assign_date: today(getLocalTimeZone()),
        comment: '',
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
        });
    }
}, { immediate: true });


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    router.post('/assets/assign', {
        asset_id: asset.value?.id,
        assigned_to_id: values.assigned_to_id,
        assign_date: values.assign_date,
        comment: values.comment,
    }, {
        onFinish: () => {
            isSubmitting.value = false;
        },
        onSuccess: () => {
            console.log('Asignado con éxito');
            open.value = false;
            asset.value = null;
        }
    });
};

</script>
