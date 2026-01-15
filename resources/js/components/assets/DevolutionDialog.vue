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
                                        <SelectLabel>Responsable (TI)</SelectLabel>
                                        <WhenVisible data="TIUsers">
                                            <template #fallback>
                                               <div class="flex flex-col gap-2 p-3">

                                                    <Skeleton v-for="n in 4" :key="n" class="h-6 w-full" />
                                                </div>
                                            </template>
                                            <SelectGroup>
                                                <SelectItem v-for="user in TIUsers" :key="user.staff_id"
                                                    :value="user.staff_id">
                                                    {{ user.full_name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </WhenVisible>

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
                                <div class="w-full flex max-sm:flex-col gap-2">
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>
                                            <Button variant="outline" class="sm:w-8/12 justify-between font-normal">
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
                                                        onDateSelected(value, componentField)
                                                        // componentField.onChange(value)
                                                        close()
                                                    }
                                                }" />
                                        </PopoverContent>
                                    </Popover>
                                    <Input id="time-picker" type="time" step="1" :default-value="formatTime"
                                        class="bg-background sm:w-4/12 appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none" />
                                </div>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="return_reason" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="return_reason">Motivo de Devolución</FieldLabel>
                                <ButtonGroup class="w-full">
                                    <Button type="button" class="w-11/12" variant="outline">

                                        <component :is="returnReasonOp(componentField.modelValue).icon" />
                                        {{ returnReasonOp(componentField.modelValue).label }}
                                    </Button>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button type="button" class="w-1/12" variant="outline" size="icon">
                                                <ChevronDownIcon />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="[--radius:1rem]">
                                            <DropdownMenuGroup>
                                                <DropdownMenuItem v-for="option in returnReasonOptions"
                                                    :key="option.value" @click="() => {

                                                        componentField.onChange(option.value)
                                                    }">
                                                    <component :is="option.icon" />
                                                    {{ option.label }}
                                                    <DropdownMenuShortcut
                                                        v-if="componentField.modelValue === option.value">
                                                        <Check />
                                                    </DropdownMenuShortcut>
                                                </DropdownMenuItem>
                                            </DropdownMenuGroup>

                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </ButtonGroup>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>



                    <FieldGroup>
                        <VeeField name="return_comment" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="return_comment">Observaciones</FieldLabel>
                                <Textarea id="return_comment"
                                    placeholder="Condiciones del equipo, accesorios incluidos..." rows="3"
                                    v-bind="componentField" />
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
import { computed, reactive, ref } from 'vue';
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
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel
} from '@/components/ui/field';
import { Input } from '@/components/ui/input';
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

import { ButtonGroup } from '@/components/ui/button-group';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import { type Asset } from '@/interfaces/asset.interface';
import { ReturnReason, returnReasonOp, returnReasonOptions, type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { type User } from '@/interfaces/user.interface';
import { router, usePage, WhenVisible } from '@inertiajs/vue3';
import { CalendarDateTime, DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { isBefore, parseISO } from 'date-fns';
import { Check, ChevronDownIcon } from 'lucide-vue-next';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const time = reactive({
    hours: new Date().getHours(),
    minutes: new Date().getMinutes(),
    seconds: new Date().getSeconds(),
});

const formatTime = computed(() => {
    return `${time.hours.toString().padStart(2, '0')}:${time.minutes.toString().padStart(2, '0')}:${time.seconds.toString().padStart(2, '0')}`;
});

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
        returned_date: z.instanceof(CalendarDateTime, {
            message: 'Seleccione una fecha de entrega',
        }).transform((date: CalendarDateTime) => date.toDate(getLocalTimeZone())).refine((date) => {
            const assignedAtDate = assign.value ? parseISO(assign.value.assigned_at.split('T')[0]) : null;

            return isBefore(assignedAtDate!, date);
        }, {
            message: 'La fecha de devolución debe ser posterior a la fecha de asignación y no puede ser una fecha pasada',
        }),
        return_reason: z.nativeEnum(ReturnReason, {
            message: 'Seleccione un motivo de devolución',
            required_error: 'Seleccione un motivo de devolución',
            // errorMap: () => ({ message: 'Seleccione un motivo de devolución' }),
        }),
        return_comment: z.string().optional(),
    })
);

const { handleSubmit, errors } = useForm({
    initialValues: {
        responsible_id: undefined,
        returned_date: new CalendarDateTime(today(getLocalTimeZone()).year, today(getLocalTimeZone()).month, today(getLocalTimeZone()).day, time.hours, time.minutes, time.seconds),
        return_comment: '',
        return_reason: ReturnReason.EQUIPMENT_RENOVATION,
    },
    validationSchema: formSchema,
    validateOnMount: false,

});




const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    router.post(`/assets/devolve/${assign.value?.id}`, {
        responsible_id: values.responsible_id,
        return_date: values.returned_date,
        return_comment: values.return_comment,
        return_reason: values.return_reason,
    }, {
        only: ['assetsPaginated', 'stats', 'accessories'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        

        onFinish: () => {
            isSubmitting.value = false;
        },
        onSuccess: () => {
            open.value = false;
            asset.value = null;
        }
    });
};

const onDateSelected = (date: DateValue, componentField: any) => {
    const dateTime = new CalendarDateTime(
        date.year,
        date.month,
        date.day,
        time.hours,
        time.minutes,
        time.seconds
    )

    componentField.onChange(dateTime)
}
</script>
