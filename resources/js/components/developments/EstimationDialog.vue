<template>
    <Dialog v-model:open="open" @update:open="(val) => { if (!val) onReset() }">
        <DialogContent class="max-w-[min(100vw-1.5rem,500px)] sm:max-w-md p-0">
            <form id="estimate-form" @submit.prevent="handleSubmit(onSubmit)()">
                <DialogHeader class="border-b px-4 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                            <Lightbulb class="size-6 text-amber-500" />
                        </div>
                        <div class="flex text-start flex-col gap-1">
                            <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                                Estimar Requerimiento
                            </DialogTitle>
                            <p class="text-sm text-muted-foreground leading-snug">
                                Completa los detalles de estimación para {{ currentDevelopment?.title }}
                            </p>
                        </div>
                    </div>
                </DialogHeader>

                <ScrollArea class="max-h-96 sm:max-h-[60vh]">
                    <div class="space-y-6 px-4 pb-5 sm:px-6 sm:pb-6">
                        <!-- Estimation Details Section -->
                        <section class="space-y-4 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                            <div class="flex items-center gap-2">
                                <Clock class="h-4 w-4 text-amber-500" />
                                <div>
                                    <p class="text-sm font-medium">Detalles de Estimación</p>
                                    <p class="text-xs text-muted-foreground">Define el tiempo y recursos necesarios</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Estimated End Date -->
                                <VeeField name="estimated_end_date" v-slot="{ componentField, errors: fieldErrors }">
                                    <div class="space-y-2">
                                        <Label :for="componentField.name" class="flex items-center gap-2">
                                            <CalendarIcon class="h-3.5 w-3.5 text-muted-foreground" />
                                            Fecha estimada de culminación *
                                        </Label>
                                        <Popover v-slot="{ close }">
                                            <PopoverTrigger as-child>
                                                <Button id="date" variant="outline"
                                                    class="w-full justify-between font-normal">
                                                    <!-- {{ componentField.modelValue }} -->
                                                    {{ componentField.modelValue ?
                                                        format(
                                                            toDate(componentField.modelValue).getTime(),
                                                            "dd 'de' MMMM 'de' yyyy",
                                                            { locale: es }
                                                        )
                                                        :
                                                        "Seleccionar fecha" }}
                                                    <ChevronDownIcon />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                                <Calendar v-bind="componentField" layout="month-and-year" locale="es" />
                                                <!-- @update:model-value="(value) => {
                                                        if (value) {
                                                            date = value
                                                            close()
                                                        }
                                                    }"  -->
                                            </PopoverContent>
                                        </Popover>
                                        <FieldError v-if="fieldErrors.length" :errors="fieldErrors" />
                                    </div>
                                </VeeField>

                                <!-- Estimated Hours -->
                                <VeeField name="estimated_hours" v-slot="{ componentField, errors: fieldErrors }">
                                    <div class="space-y-2">
                                        <Label :for="componentField.name" class="flex items-center gap-2">
                                            <Timer class="h-3.5 w-3.5 text-muted-foreground" />
                                            Horas/Hombre estimadas *
                                        </Label>
                                        <div class="flex items-center gap-2">
                                            <Input id="estimated_hours" type="number" min="1" step="0.5"
                                                placeholder="Ej: 40" v-bind="componentField" />
                                            <span
                                                class="text-xs text-muted-foreground whitespace-nowrap font-medium">horas</span>
                                        </div>
                                        <FieldError v-if="fieldErrors.length" :errors="fieldErrors" />
                                    </div>
                                </VeeField>


                            </div>
                        </section>

                        <!-- Summary Section -->
                        <section
                            class="space-y-3 rounded-lg border border-blue-200/50 bg-blue-50/30 dark:border-blue-900/30 dark:bg-blue-950/20 p-3 sm:p-4">
                            <div class="flex items-center gap-2">
                                <Info class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Resumen de estimación
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded bg-white dark:bg-slate-950 p-2.5 text-center">
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider font-semibold">
                                        Horas totales</p>
                                    <p class="text-lg font-bold text-primary mt-1">{{ values.estimated_hours || '—' }}
                                    </p>
                                </div>

                                <div class="rounded bg-white dark:bg-slate-950 p-2.5 text-center">
                                    <p class="text-[10px] text-muted-foreground uppercase tracking-wider font-semibold">
                                        Hasta</p>
                                    <p class="text-sm font-bold text-emerald-600 mt-1">


                                        {{
                                            values.estimated_end_date ?

                                                format(
                                                    toDate(values.estimated_end_date).getTime(),
                                                    "dd 'de' MMMM 'de' yyyy",
                                                    { locale: es }
                                                ) : '—'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </ScrollArea>

                <Separator />

                <DialogFooter class="px-4 py-4 sm:px-6 flex gap-2">
                    <Button form="estimate-form" variant="outline" type="button" @click="open = false"
                        class="w-full sm:w-auto">
                        Cancelar
                    </Button>
                    <Button form="estimate-form" :disabled="Object.keys(errors).length > 0 || isLoading" type="submit"
                        class="gap-1 w-full sm:w-auto">
                        <CheckCircle class="h-4 w-4" />
                        Confirmar estimación
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import FieldError from '@/components/ui/field/FieldError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { useApp } from '@/composables/useApp';
import { DevelopmentRequestSection, DevelopmentRequestStatus, type DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { CalendarIcon, CheckCircle, ChevronDownIcon, Clock, Info, Lightbulb, Timer } from 'lucide-vue-next';
import { toDate } from 'reka-ui/date';
import { useForm, Field as VeeField } from 'vee-validate';
import { watch } from 'vue';
import { toast } from 'vue-sonner';
import z from 'zod';

const open = defineModel<boolean>('open');
const currentDevelopment = defineModel<DevelopmentRequest | null>('currentDevelopment');

const { isLoading } = useApp();

const formSchema = toTypedSchema(
    z.object({
        estimated_end_date: z.instanceof(CalendarDate, {
            message: 'Seleccione una fecha de entrega',
        })
            .refine((date) => {
                const todayDate = today(getLocalTimeZone()).toDate(getLocalTimeZone());
                return date.toDate(getLocalTimeZone()) >= todayDate;
            }, {
                message: 'La fecha estimada debe ser hoy o una fecha futura',
            }),
        estimated_hours: z.coerce.number({
            message: 'Las horas estimadas son obligatorias',
        }).min(1, 'Debe ser mínimo 1 hora').max(999999, 'Las horas estimadas no pueden exceder 6 dígitos'),

    })
);

const initialValues = {
    estimated_end_date: undefined,
    estimated_hours: undefined,
};

const { errors, handleSubmit, values, setValues, handleReset } = useForm({
    validationSchema: formSchema,
    initialValues,
    keepValuesOnUnmount: true
});

watch(open, (newVal) => {
    const dev = currentDevelopment.value;
    if (dev) {
        const date = new Date(dev?.estimated_end_date || '');
        setValues({
            estimated_end_date: dev.estimated_end_date
                ? new CalendarDate(
                    date.getUTCFullYear(),
                    date.getUTCMonth() + 1,
                    date.getUTCDate()

                )
                : undefined,
            estimated_hours: dev.estimated_hours || undefined,
        });
    } else {
        setValues(initialValues);
    }
});

const onReset = () => {
    handleReset();
    open.value = false;
};

const onSubmit = async (submitValues: any) => {
    if (!currentDevelopment.value) {
        toast.error('No se encontró la solicitud de desarrollo actual.');
        return;
    }
    const date = submitValues.estimated_end_date.toDate(getLocalTimeZone());

    router.patch(
        `/developments/${currentDevelopment.value?.id}/estimate`, {
        estimated_end_date: date,
        estimated_hours: submitValues.estimated_hours,
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                router.replaceProp('developmentsByStatus', (developments: DevelopmentRequestSection) => {
                    const status = DevelopmentRequestStatus.IN_ANALYSIS;
                    return {
                        ...developments,
                        [status]: developments[status].map((dev) => {
                            if (dev.id === currentDevelopment.value?.id) {
                                return {
                                    ...dev,
                                    estimated_end_date: format(date, 'yyyy-MM-dd'),
                                    estimated_hours: submitValues.estimated_hours,
                                };
                            }
                            return dev;
                        }

                        ),
                    }
                });
                onReset();
            }
        }
    });

};
</script>