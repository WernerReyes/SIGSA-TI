<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            ticket = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-4xl max-h-screen overflow-y-auto space-y-5">
            <DialogHeader class="space-y-3 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <component 
                            :is="assetTypeOp(currAssetAssignment?.asset?.type.name)?.icon || MonitorSmartphone" class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-xl font-semibold">Devolver {{ currAssetAssignment?.asset?.type?.name }}
                        </DialogTitle>
                        <p class="text-sm text-muted-foreground">Registra la devolución del activo y opcionalmente
                            adjunta los accesorios.</p>
                        <p v-if="ticket"
                            class="text-xs text-muted-foreground mt-1 inline-flex gap-2 items-center bg-muted px-2 py-1 rounded-md">
                            <span class="font-mono">TK-{{ ticket?.id }}</span>
                            <span class="text-foreground">·</span>
                            <span class="font-medium line-clamp-1">{{ ticket?.title }}</span>
                        </p>
                    </div>
                </div>
            </DialogHeader>


            <Empty v-if="!currAssetAssignment" class="border border-dashed">
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <Info class="size-6 text-muted-foreground" />
                    </EmptyMedia>
                    <EmptyTitle>
                        No hay una asignación de activo seleccionada
                    </EmptyTitle>
                    <EmptyDescription>
                        Selecciona un ticket con una asignación de activo para proceder con la devolución.
                    </EmptyDescription>
                </EmptyHeader>

            </Empty>

            <div class="space-y-5 py-2" v-else>

                <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm"
                    class="space-y-4 p-4 rounded-xl border bg-card/70">


                    <FieldGroup>
                        <VeeField name="returned_date" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="returned_date">Fecha de Devolución</FieldLabel>
                                <div class="w-full flex max-sm:flex-col gap-2">
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>
                                            <Button variant="outline" class="sm:w-8/12 justify-between font-normal">
                                                <!-- {{ componentField.modelValue }} -->
                                                {{ componentField.modelValue
                                                    ?
                                                    format(

                                                        componentField.modelValue.toDate(getLocalTimeZone()),
                                                        'dd/MM/yyyy',
                                                    )
                                                    : 'Seleccionar fecha' }}

                                                <ChevronDownIcon />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                            <Calendar v-bind="componentField" locale="es" layout="month-and-year"
                                                selection-mode="single" @update:model-value="(value) => {
                                                    if (value) {
                                                        onDateSelected(value, componentField)
                                                        close()
                                                    }
                                                }" />
                                        </PopoverContent>
                                    </Popover>
                                    <InputGroup>
                                        <InputGroupInput id="time-picker" type="time" step="1"
                                            :default-value="formatTime" :key="formatTime"
                                            class="bg-background sm:w-4/12 appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none" />

                                        <InputGroupAddon align="inline-end">

                                            <RefreshCcw class="size-4 cursor-pointer" @click="refreshTime" />
                                        </InputGroupAddon>
                                    </InputGroup>
                                </div>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="return_reason" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="return_reason">Motivo de Devolución</FieldLabel>
                                <div class="flex gap-2">
                                    <Button type="button" class="flex-1 justify-start gap-2" variant="outline">
                                        <component :is="returnReasonOp(componentField.modelValue).icon"
                                            class="size-4" />
                                        {{ returnReasonOp(componentField.modelValue).label }}
                                    </Button>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button type="button" class="w-12" variant="outline" size="icon">
                                                <ChevronDownIcon />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="[--radius:1rem] min-w-56">
                                            <DropdownMenuGroup>
                                                <DropdownMenuItem v-for="option in returnReasonOptions"
                                                    :key="option.value" class="gap-2" @click="() => {
                                                        componentField.onChange(option.value)
                                                    }">
                                                    <component :is="option.icon" class="size-4" />
                                                    {{ option.label }}
                                                    <DropdownMenuShortcut
                                                        v-if="componentField.modelValue === option.value">
                                                        <Check />
                                                    </DropdownMenuShortcut>
                                                </DropdownMenuItem>
                                            </DropdownMenuGroup>

                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
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


            <DialogFooter class="gap-2 pt-2 border-t">

                <Button variant="outline" @click="open = false" :disabled="isSubmitting">Cancelar</Button>
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0 || !currAssetAssignment
                    " type="submit" form="dialogForm" class="min-w-36">
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
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, Empty } from '@/components/ui/empty';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import { type Asset } from '@/interfaces/asset.interface';
import { ReturnReason, returnReasonOp, returnReasonOptions, type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { type User } from '@/interfaces/user.interface';
import { router, usePage, WhenVisible } from '@inertiajs/vue3';
import { CalendarDateTime, DateValue, getLocalTimeZone, today, toLocalTimeZone } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { isBefore, parseISO, format } from 'date-fns';
import { Check, ChevronDownIcon, Info, RefreshCcw, Undo2, MonitorSmartphone } from 'lucide-vue-next';
import { useAsset } from '@/composables/useAsset';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { type Alert } from '@/interfaces/alert.interface';
import { toZonedDate } from '@/lib/utils';
import { Ticket } from '@/interfaces/ticket.interface';


const ticket = defineModel<Ticket | null>('ticket');
const open = defineModel<boolean>('open');

const page = usePage();
const { downloadReturnAssignmentDocument } = useAsset();

const time = reactive({
    hours: new Date().getHours(),
    minutes: new Date().getMinutes(),
    seconds: new Date().getSeconds(),
});


const currAssetAssignment = computed<AssetAssignment | null>(() => {
    return page.props.currentAssignment || null;
});

const formatTime = computed(() => {
    return `${time.hours.toString().padStart(2, '0')}:${time.minutes.toString().padStart(2, '0')}:${time.seconds.toString().padStart(2, '0')}`;
});

// const TIUsers = computed<User[]>(() => {
//     return (usePage().props.TIUsers || []) as User[];
// });


// const accessoriesToReturn = computed<Asset[]>(() => {
//     if (!assign.value) return [];
//     return assign.value.children_assignments?.flatMap(child => child.asset!) || [];
// });

const accesoriesOutOfStockAlertsExists = computed<boolean>(() => {
    const alerts = (page.props?.accessoriesOutOfStockAlert || []) as Alert[];
    return alerts.length > 0;
});

const isSubmitting = ref(false);

const formSchema = toTypedSchema(
    z.object({
        // responsible_id: z.number({ invalid_type_error: 'Seleccione un responsable', required_error: 'Seleccione un responsable' }),
        returned_date: z.instanceof(CalendarDateTime, {
            message: 'Seleccione una fecha de entrega',
        }).refine((calendarDate) => {
            const date = calendarDate.toDate(getLocalTimeZone());
            const assignedAtDate = currAssetAssignment.value ? parseISO(currAssetAssignment.value.assigned_at.split('T')[0]) : null;

            return isBefore(assignedAtDate!, date);
        }, {
            message: 'La fecha de devolución debe ser posterior a la fecha de asignación y no puede ser una fecha pasada',
        }),
        return_reason: z.nativeEnum(ReturnReason, {
            message: 'Seleccione un motivo de devolución',
            required_error: 'Seleccione un motivo de devolución',
            // errorMap: () => ({ message: 'Seleccione un motivo de devolución' }),
        }),
        // accessories: z.array(z.number()).optional(),
        return_comment: z.string().optional(),
    })
);

const { handleSubmit, errors, values } = useForm({
    initialValues: {
        // responsible_id: undefined,
        returned_date: new CalendarDateTime(today(getLocalTimeZone()).year, today(getLocalTimeZone()).month, today(getLocalTimeZone()).day, time.hours, time.minutes, time.seconds),
        return_comment: '',
        return_reason: ReturnReason.EQUIPMENT_RENOVATION,
        // accessories: accessoriesToReturn.value.map(acc => acc.id),
    },
    validationSchema: formSchema,
    validateOnMount: false,

});


const onSubmit = async (values: Record<string, any>) => {
    isSubmitting.value = true;

    const returnDate = values.returned_date.toDate(getLocalTimeZone());
    console.log('Submitting devolution with values:', values, 'and returnDate:', returnDate);
    router.post(`/assets/devolve/${currAssetAssignment.value?.id}`, {
        responsible_id: ticket?.value?.responsible_id,
        return_date: toZonedDate(returnDate),
        return_comment: values.return_comment,
        return_reason: values.return_reason,
        ticket_id: ticket?.value?.id,
        // accessories: values.accessories,
    }, {
        // only: ['assetsPaginated', 'stats', 'accessories'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        onFlash: (flash) => {
            if (flash.alert_triggered && !accesoriesOutOfStockAlertsExists.value) {
                router.reload({
                    only: ['accessoriesOutOfStockAlert'],
                });

            }
        },


        onFinish: () => {
            isSubmitting.value = false;
        },
        onSuccess: () => {
            console.log('Devolution successful');
            open.value = false;
            ticket.value = null;

            // downloadReturnAssignmentDocument(currAssetAssignment.value!.id);
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

const refreshTime = () => {
    const now = new Date();
    time.hours = now.getHours();
    time.minutes = now.getMinutes();
    time.seconds = now.getSeconds();
}
</script>
