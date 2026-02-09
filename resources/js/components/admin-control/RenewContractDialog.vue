<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            resetAndClose();
        }
    }">
        <DialogContent class="sm:max-w-3xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <RefreshCcw class="h-5 w-5 text-primary" />
                    Renovación de contrato
                </DialogTitle>
                <DialogDescription>
                    Actualiza la vigencia y condiciones clave del contrato seleccionado.
                </DialogDescription>
            </DialogHeader>

            <ScrollArea class="max-h-[calc(100vh-15rem)] pr-2">
                <div v-if="!selectedContract"
                    class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                    Selecciona un contrato para renovarlo.
                </div>

                <form v-else id="renew-contract-form" class="space-y-5" @submit.prevent="handleSubmit(handleRenew)()">
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <p class="text-[11px] font-mono text-muted-foreground">
                                    CTR-{{ selectedContract.id.toString().padStart(3, '0') }}
                                </p>
                                <p class="text-sm font-semibold text-foreground">
                                    {{ selectedContract.name }}
                                </p>
                                <p class="text-[11px] text-muted-foreground">
                                    {{ selectedContract.provider || 'Proveedor no definido' }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Badge class="text-[11px]" :class="getContractOp('type', selectedContract.type).bg">
                                    {{ getContractOp('type', selectedContract.type).label || 'Contrato' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('period', selectedContract.period).bg">
                                    {{ getContractOp('period', selectedContract.period).label || 'Periodo' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('status', selectedContract.status).bg">
                                    {{ getContractOp('status', selectedContract.status).label || 'Estado' }}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CalendarClock class="h-4 w-4 text-primary" />
                            Vigencia renovada
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <VeeField name="new_end_date" v-slot="{ componentField, errors }" class="space-y-2">
                                <div class="space-y-2">
                                    <Label>Nueva fecha de fin</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <CalendarCheck2 class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="date" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>

                            </VeeField>


                            <VeeField name="alert_days_before" v-slot="{ componentField, errors }" class="space-y-2">
                                <div class="space-y-2">
                                    <Label>Dias antes de alerta</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Bell class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput :v-model="componentField.modelValue"
                                            :default-value="componentField.modelValue" @update:model-value="(val: string | number) => {
                                                if (val === '') {
                                                    setFieldValue('alert_days_before', undefined);
                                                } else {
                                                    setFieldValue('alert_days_before', Number(val));
                                                }
                                            }" type="number" min="0" placeholder="Ej: 15" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />

                                </div>
                            </VeeField>


                        </div>
                    </div>

                    <VeeField name="notes" v-slot="{ componentField, errors }">
                        <div class="rounded-lg border bg-muted/30 p-4">
                            <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <StickyNote class="h-4 w-4 text-primary" />
                                Notas de renovacion
                            </div>
                            <div class="mt-4">
                                <Textarea v-bind="componentField" rows="3" placeholder="Notas (opcional)" />
                            </div>
                        </div>
                        <FieldError :errors="errors" />
                    </VeeField>
                </form>
            </ScrollArea>

            <DialogFooter class="mt-4">
                <Button variant="outline" @click="resetAndClose">Cancelar</Button>
                <Button class="gap-2" type="submit" form="renew-contract-form"
                    :disabled="isLoading || !selectedContract">
                    <Sparkles class="h-4 w-4" />
                    Renovar contrato
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import { type Contract, getContractOp } from '@/interfaces/contract.interface';
import { parseDateOnly } from '@/lib/utils';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { differenceInDays, isAfter, isSameDay } from 'date-fns';
import { Bell, CalendarCheck2, CalendarClock, RefreshCcw, Sparkles, StickyNote } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import z from 'zod';
import { FieldError } from '../ui/field';

const open = defineModel<boolean>('open', { default: false });
const selectedContract = defineModel<Contract | null>('selectedContract', { default: null });

const { isLoading } = useApp();


const formSchema = toTypedSchema(z.object({
    new_end_date: z.string({
        required_error: 'Este campo es requerido',
        invalid_type_error: 'Este campo debe ser una fecha válida',
    }).refine((date) => {
        const parsedDate = Date.parse(date);
        return !isNaN(parsedDate);
    }, {
        message: 'Este campo debe ser una fecha válida',
    }).refine((date) => {
        const today = new Date();
        const endDate = parseDateOnly(date);
        return isSameDay(endDate, today) || isAfter(endDate, today);
    }, {
        message: 'La fecha de fin debe ser hoy o una fecha futura',
    }),
    alert_days_before: z.number({
        invalid_type_error: 'Este campo debe ser un número',
    }).min(0, {
        message: 'El valor debe ser mayor o igual a 0',
    }),
    notes: z.string().optional(),
}).refine((data) => {
    if (data.alert_days_before === undefined) return true; // Permitir undefined
    const today = new Date();
    const endDate = parseDateOnly(data.new_end_date);
    const diff = differenceInDays(endDate, today);
    return data.alert_days_before <= diff;
}, {
    path: ['alert_days_before'],
    message: 'Los días de alerta deben ser menores o iguales a la diferencia entre la fecha de fin y hoy',
})
);

const initialValues = {
    new_end_date: '',
    alert_days_before: undefined,
    notes: '',
};

const { setFieldValue, handleSubmit, resetForm, values } = useForm({
    validationSchema: formSchema,
    initialValues
});

type FormValues = typeof values;




const resetAndClose = () => {
    resetForm();
    selectedContract.value = null;
    open.value = false;
};



const handleRenew = (values: FormValues) => {
    if (!selectedContract.value) return;

    router.post(`/admin-control/${selectedContract.value.id}/renew`, values, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            // const contract = flash.contract as Contract;
            router.replaceProp('contracts', (contracts: Contract[]) => {
                return contracts.map(c => c.id === selectedContract.value?.id ? {
                    ...c,
                    new_end_date: values.new_end_date,
                    expiration: {
                        ...c.expiration,
                        alert_days_before: values.alert_days_before
                    },
                    // notes: values.notes
                } : c);
            });
            resetAndClose();
        }
    });
};
</script>
