<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-3xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <FileText class="h-5 w-5 text-primary" />
                    Nuevo contrato
                </DialogTitle>
                <DialogDescription>
                    Define la forma de contrato para ver únicamente los campos necesarios.
                </DialogDescription>
            </DialogHeader>


            <ScrollArea class="max-h-[70vh] pr-2">

                <form id="contract-form" class="space-y-5" @submit.prevent="handleSubmit(handleSave)()">

                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Info class="h-4 w-4 text-primary" />
                            Información general
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <VeeField name="name" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Nombre del contrato</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Tags class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField"
                                            placeholder="Ej: Microsoft 365 Business" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="provider" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Proveedor</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Building2 class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" placeholder="Ej: Microsoft" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="type" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Tipo </Label>
                                    <div class="relative">
                                        <Package
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar tipo" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="type in Object.values(contractTypeOptions)"
                                                    :key="type.value" :value="type.value">
                                                    <Badge :class="type.bg">
                                                        <component :is="type.icon" />
                                                        {{ type.label }}
                                                    </Badge>
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="period" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Forma del contrato</Label>
                                    <div class="relative">
                                        <Layers
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar tipo de contrato" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="period in Object.values(contractPeriodOptions)"
                                                    :key="period.value" :value="period.value">
                                                    <Badge :class="period.bg">
                                                        <component :is="period.icon" />
                                                        {{ period.label }}
                                                    </Badge>
                                                </SelectItem>


                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="status" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Estado</Label>
                                    <div class="relative">
                                        <ShieldCheck
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar estado" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="status in Object.values(contractStatusOptions)"
                                                    :key="status.value" :value="status.value">
                                                    <Badge :class="status.bg">
                                                        <component :is="status.icon" />
                                                        {{ status.label }}
                                                    </Badge>
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="start_date" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Fecha de inicio</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Calendar class="size-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="date" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>


                            <VeeField name="notes" v-slot="{ errors, componentField }">
                                <div class="space-y-2 md:col-span-2">
                                    <Label>Notas / observaciones</Label>
                                    <div class="relative">
                                        <StickyNote class="h-4 w-4 text-muted-foreground absolute left-3 top-3" />
                                        <Textarea v-bind="componentField" rows="3" class="pl-9"
                                            placeholder="Condiciones, SLA, observaciones relevantes..." />
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>
                        </div>
                    </div>

                    <div v-if="values.period === ContractPeriod.RECURRING" class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Repeat class="h-4 w-4 text-primary" />
                            Contrato recurrente
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

                            <VeeField name="frequency" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Frecuencia de facturación</Label>
                                    <div class="relative">
                                        <CreditCard
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar frecuencia" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="frequency in Object.values(billingFrequencyOptions)"
                                                    :key="frequency.value" :value="frequency.value">
                                                    <Badge :class="frequency.bg">
                                                        <component :is="frequency.icon" />
                                                        {{ frequency.label }}
                                                    </Badge>
                                                </SelectItem>

                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>


                            <div class="space-y-2">
                                <Label>¿Monto fijo?</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="values.hasFixedAmount"
                                        @update:model-value="(val) => setFieldValue('hasFixedAmount', val)" />
                                    <span class="text-sm">{{ values.hasFixedAmount ? 'Sí' : 'No' }}</span>
                                </div>
                            </div>


                            <VeeField name="amount" v-slot="{ errors, componentField }">
                                <div v-if="values.hasFixedAmount" class="space-y-2">
                                    <Label>Monto</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <BadgeDollarSign class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput :model-value="componentField.modelValue" @update:model-value="(val: string | number) => {
                                            if (val === '') {
                                                setFieldValue('amount', undefined);
                                            } else {
                                                setFieldValue('amount', Number(val));
                                            }
                                        }" type="number" min="0" step="1" placeholder="$ 0" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>

                            <VeeField name="currency" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Moneda</Label>
                                    <div class="relative">
                                        <Coins
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar moneda" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="currency in Object.values(CurrencyType)"
                                                    :key="currency" :value="currency">
                                                    {{ currency }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>


                            <div class="space-y-2">
                                <Label>Renovación automática</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="values.autoRenew"
                                        @update:model-value="(val) => setFieldValue('autoRenew', val)" />
                                    <span class="text-sm">{{ values.autoRenew ? 'Sí' : 'No' }}</span>
                                </div>
                            </div>

                            <VeeField name="next_billing_date" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Fecha del próximo cobro</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <CalendarCheck2 class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="date" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>



                            <VeeField name="billing_cycle_days" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Días antes para alerta de renovación</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Bell class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="number" min="1"
                                            placeholder="Ej: 15" />
                                    </InputGroup>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>
                        </div>
                    </div>

                    <div v-if="values.period === ContractPeriod.FIXED_TERM" class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CalendarCheck2 class="h-4 w-4 text-primary" />
                            Contrato por periodo fijo
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">


                            <VeeField name="end_date" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Fecha de fin (obligatoria)</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <CalendarCheck2 class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="date" />
                                    </InputGroup>
                                    <ErrorField :errors="errors" />
                                    <!-- <p v-if="showError('endDate')" class="text-xs text-destructive">{{ errors.endDate }}</p> -->
                                </div>
                            </VeeField>


                            <VeeField name="billing_cycle_days" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Días antes para alerta de vencimiento</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Bell class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="number" min="1"
                                            placeholder="Ej: 30" />
                                    </InputGroup>
                                    <ErrorField :errors="errors" />
                                </div>
                            </VeeField>
                        </div>
                    </div>

                    <div v-if="values.period === ContractPeriod.ONE_TIME" class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Wallet class="h-4 w-4 text-primary" />
                            Contrato de pago único
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

                            <VeeField name="amount" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Monto</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <BadgeDollarSign class="h-4 w-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput v-bind="componentField" type="number" min="0" step="0.01"
                                            placeholder="$ 0" />
                                    </InputGroup>
                                    <ErrorField :errors="errors" />
                                </div>
                            </VeeField>


                            <VeeField name="currency" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Moneda</Label>
                                    <div class="relative">
                                        <Coins
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select v-bind="componentField">
                                            <SelectTrigger class="pl-9 w-full">
                                                <SelectValue placeholder="Seleccionar moneda" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="currency in Object.values(CurrencyType)"
                                                    :key="currency" :value="currency">
                                                    {{ currency }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <FieldError :errors="errors" />
                                </div>
                            </VeeField>



                            <div class="space-y-2">
                                <Label>¿Tiene garantía?</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="values.hasWarranty"
                                        @update:model-value="(val) => setFieldValue('hasWarranty', val)" />
                                    <span class="text-sm">{{ values.hasWarranty ? 'Sí' : 'No' }}</span>
                                </div>
                            </div>


                            <template v-if="values.hasWarranty">


                                <VeeField name="end_date" v-slot="{ errors, componentField }">
                                    <div class="space-y-2">
                                        <Label>Fecha fin de garantía</Label>
                                        <InputGroup>
                                            <InputGroupAddon>
                                                <CalendarCheck2 class="h-4 w-4" />
                                            </InputGroupAddon>
                                            <InputGroupInput v-bind="componentField" type="date" />
                                        </InputGroup>
                                        <ErrorField :errors="errors" />
                                    </div>
                                </VeeField>


                                <VeeField name="billing_cycle_days" v-slot="{ errors, componentField }">

                                    <div class="space-y-2">
                                        <Label>Días antes para alerta de garantía</Label>
                                        <InputGroup>
                                            <InputGroupAddon>
                                                <Bell class="h-4 w-4" />
                                            </InputGroupAddon>
                                            <InputGroupInput v-bind="componentField" type="number" min="1"
                                                placeholder="Ej: 20" />
                                        </InputGroup>
                                        <ErrorField :errors="errors" />
                                    </div>
                                </VeeField>
                            </template>
                        </div>
                    </div>
                </form>
            </ScrollArea>
            {{ values }}

            <DialogFooter class="mt-4">
                <Button variant="outline" @click="open = false">Cancelar</Button>
                <Button class="gap-2" type="submit" form="contract-form" :disabled="!isFormValid">
                    <Sparkles class="h-4 w-4" />
                    Guardar contrato
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import FieldError from '@/components/ui/field/FieldError.vue';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import { ContractPeriod, contractPeriodOptions, ContractStatus, contractStatusOptions, ContractType, contractTypeOptions } from '@/interfaces/contract.interface';
import { BillingFrequency, billingFrequencyOptions, CurrencyType } from '@/interfaces/contractBilling.interface';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { BadgeDollarSign, Bell, Building2, Calendar, CalendarCheck2, Coins, CreditCard, FileText, Info, Layers, Package, Repeat, ShieldCheck, Sparkles, StickyNote, Tags, Wallet } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed } from 'vue';
import z from 'zod';

const open = defineModel<boolean>('open', { default: false });

const { isLoading } = useApp();

const isFormValid = computed(() => {
    return !isLoading.value && Object.keys(errors.value).length === 0;
});

const formSchema = toTypedSchema(

    z.object({
        name: z.string({
            message: 'Este campo es obligatorio.',
        }).min(1, 'Este campo es obligatorio.'),
        provider: z.string({
            message: 'Este campo es obligatorio.',
        }).min(1, 'Este campo es obligatorio.'),
        type: z.nativeEnum(ContractType, {
            errorMap: () => ({ message: 'Selecciona un tipo de contrato.' }),
            message: 'Selecciona un tipo de contrato.',
        }),
        period: z.nativeEnum(ContractPeriod, {
            errorMap: () => ({ message: 'Selecciona una forma de contrato.' }),
            message: 'Selecciona una forma de contrato.',
        }),
        status: z.nativeEnum(ContractStatus, {
            errorMap: () => ({ message: 'Selecciona un estado.' }),
            message: 'Selecciona un estado.',
        }),
        currency: z.nativeEnum(CurrencyType, {
            errorMap: () => ({ message: 'Selecciona una moneda.' }),
            message: 'Selecciona una moneda.',
        }).optional().default(CurrencyType.PEN),

        start_date: z.string({
            message: 'Selecciona la fecha de inicio.',
        }).min(1, 'Selecciona la fecha de inicio.'),


        end_date: z.string().optional(),
        notes: z.string().optional(),

        frequency: z.nativeEnum(BillingFrequency).optional().default(BillingFrequency.MONTHLY),
        // has
        hasFixedAmount: z.boolean().default(false),
        amount: z.number({
            // message: 'Este campo es obligatorio.',
        }).positive({
            message: 'Ingresa un monto válido.',
        }).optional(),
        autoRenew: z.boolean().default(false),
        next_billing_date: z.string().optional(),
        billing_cycle_days: z.number().optional(),
        // renewalAlertDays: z.number().positive().optional(),

        hasWarranty: z.boolean().default(false),
        // warrantyEndDate: z.string().optional(),



    }).refine((data) => {
        if (data.period === ContractPeriod.RECURRING) {
            return data.frequency !== undefined
        }
        return true;
    }, {
        message: 'Selecciona la frecuencia de facturación.',
        path: ['frequency'],
    }).refine((data) => {
        if (data.period === ContractPeriod.RECURRING) {
            return data.hasFixedAmount === false ||
                (data.amount !== undefined && data.amount > 0);
        }
        return true;
    }, {
        message: 'Ingresa un monto válido.',
        path: ['amount'],
    }).refine((data) => {
        if (data.period === ContractPeriod.RECURRING) {
            return data.next_billing_date !== undefined && data.next_billing_date !== '';
        }
        return true;
    }, {
        message: 'Selecciona la fecha del próximo cobro.',
        path: ['next_billing_date'],
    })
        .refine((data) => {
            if (data.period === ContractPeriod.RECURRING) {
                return data.billing_cycle_days !== undefined &&
                    data.billing_cycle_days > 0;
            }
            return true;
        }, {
            message: 'Ingresa los días para la alerta.',
            path: ['billing_cycle_days'],
        })
        .refine((data) => {
            if (data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                return data.end_date !== undefined && data.end_date !== '';
            }

            return true;
        }, {
            message: 'Selecciona la fecha de fin.',
        }));

const { values, errors, handleSubmit, resetForm, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        provider: '',
        type: undefined,
        period: undefined,
        status: undefined,
        currency: CurrencyType.PEN,
        start_date: '',
        end_date: '',
        notes: '',
        frequency: BillingFrequency.MONTHLY,
        hasFixedAmount: false,
        amount: undefined,
        autoRenew: false,
        next_billing_date: '',
        billing_cycle_days: undefined,
        hasWarranty: false,
    },
});

const resetAndClose = () => {
    resetForm();
    open.value = false;
};


const handleSave = () => {
    console.log('Form values:', values);
    router.post('/admin-control', values, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                resetAndClose();
            }
        }

    });
};
</script>
