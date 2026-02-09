<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            resetAndClose();
        }
    }">
        <DialogContent class="sm:max-w-3xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <FileText class="h-5 w-5 text-primary" />
                    {{ isEditMode ? 'Editar contrato' : 'Nuevo contrato' }}
                </DialogTitle>
                <DialogDescription>
                    Define la forma de contrato para ver únicamente los campos necesarios.
                </DialogDescription>
            </DialogHeader>


            <ScrollArea class="max-h-96 sm:max-h-[calc(100vh-15rem)] pr-2">

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
                                        <Select :disabled="isEditMode" v-bind="componentField">
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

                            <!-- <VeeField name="status" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Estado</Label>
                                    <div class="relative">
                                        <ShieldCheck
                                            class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                        <Select :disabled="isEditMode" v-bind="componentField">
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
                            </VeeField> -->

                            <VeeField name="start_date" v-slot="{ errors, componentField }">
                                <div class="space-y-2">
                                    <Label>Fecha de inicio</Label>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Calendar class="size-4" />
                                        </InputGroupAddon>
                                        <InputGroupInput :disabled="isEditMode" v-bind="componentField" type="date" />
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

                    <template v-if="!isEditMode">

                        <div v-if="values.period === ContractPeriod.RECURRING"
                            class="rounded-lg border bg-muted/30 p-4">
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
                                                    <SelectItem
                                                        v-for="frequency in Object.values(billingFrequencyOptions).filter(f => f.value !== BillingFrequency.ONE_TIME)"
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
                                            :default-value="values.hasFixedAmount"
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

                                            <InputGroupInput :model-value="componentField.modelValue"
                                                @update:model-value="(val: string | number) => {
                                                    if (val === '') {
                                                        setFieldValue('amount', undefined);
                                                    } else {
                                                        setFieldValue('amount', Number(val));
                                                    }
                                                }" type="number" min="0" step="0.01" placeholder="$ 0" />
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
                                        <Switch v-model:checked="values.auto_renew" :default-value="values.auto_renew"
                                            @update:model-value="(val) => setFieldValue('auto_renew', val)" />
                                        <span class="text-sm">{{ values.auto_renew ? 'Sí' : 'No' }}</span>
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



                                <VeeField v-if="values.auto_renew === false" name="alert_days_before"
                                    v-slot="{ errors, componentField }">
                                    <div class="space-y-2">
                                        <Label>Días antes para alerta de renovación</Label>
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

                        <div v-if="values.period === ContractPeriod.FIXED_TERM"
                            class="rounded-lg border bg-muted/30 p-4">
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
                                        <FieldError :errors="errors" />

                                    </div>
                                </VeeField>

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
                                                    <SelectItem
                                                        v-for="frequency in Object.values(billingFrequencyOptions)"
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


                                <VeeField name="alert_days_before" v-slot="{ errors, componentField }">
                                    <div class="space-y-2">
                                        <Label>Días antes para alerta de vencimiento</Label>
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
                                                }" type="number" min="0" placeholder="Ej: 30" />
                                        </InputGroup>
                                        <FieldError :errors="errors" />
                                    </div>
                                </VeeField>

                                <VeeField name="amount" v-slot="{ errors, componentField }">
                                    <div class="space-y-2">
                                        <Label>Monto</Label>
                                        <InputGroup>
                                            <InputGroupAddon>
                                                <BadgeDollarSign class="h-4 w-4" />
                                            </InputGroupAddon>
                                            <InputGroupInput :model-value="componentField.modelValue"
                                                :default-value="componentField.modelValue" @update:model-value="(val: string | number) => {
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
                                    <Label>¿Tiene garantía?</Label>
                                    <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                        <Switch v-model:checked="values.hasWarranty" :default-value="values.hasWarranty"
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
                                            <FieldError :errors="errors" />
                                        </div>
                                    </VeeField>


                                    <VeeField name="alert_days_before" v-slot="{ errors, componentField }">

                                        <div class="space-y-2">
                                            <Label>Días antes para alerta de garantía</Label>
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
                                                    }" type="number" min="0" placeholder="Ej: 20" />
                                            </InputGroup>
                                            <FieldError :errors="errors" />
                                        </div>
                                    </VeeField>
                                </template>
                            </div>
                        </div>
                    </template>

                </form>
            </ScrollArea>
            {{ errors }}
            <DialogFooter class="mt-4">
                <Button variant="outline" @click="resetAndClose()">Cancelar</Button>
                <Button class="gap-2" type="submit" form="contract-form" :disabled="isFormInvalid">
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
import { type Contract, ContractPeriod, contractPeriodOptions, ContractStatus, contractStatusOptions, ContractType, contractTypeOptions } from '@/interfaces/contract.interface';
import { BillingFrequency, billingFrequencyDaysMap, billingFrequencyOptions, CurrencyType } from '@/interfaces/contractBilling.interface';
import { isEqual } from '@/lib/utils';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { differenceInDays, isAfter, isSameDay, set } from 'date-fns';
import { BadgeDollarSign, Bell, Building2, Calendar, CalendarCheck2, Coins, CreditCard, FileText, Info, Layers, Package, Repeat, ShieldCheck, Sparkles, StickyNote, Tags, Wallet } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, onMounted, watch } from 'vue';
import z from 'zod';

const open = defineModel<boolean>('open', { default: false });
const selectedContract = defineModel<Contract | null>('selectedContract', { default: null });

const { isLoading, echo } = useApp();

const isFormInvalid = computed(() => {
    return isLoading.value || Object.keys(errors.value).length || isEqual(initialValues.value, values);
});

const isEditMode = computed(() => !!selectedContract.value);


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
        // status: z.nativeEnum(ContractStatus, {
        //     errorMap: () => ({ message: 'Selecciona un estado.' }),
        //     message: 'Selecciona un estado.',
        // }),
        currency: z.nativeEnum(CurrencyType, {
            errorMap: () => ({ message: 'Selecciona una moneda.' }),
            message: 'Selecciona una moneda.',
        }).default(CurrencyType.PEN),

        start_date: z.string({
            message: 'Selecciona la fecha de inicio.',
        }).min(1, 'Selecciona la fecha de inicio.'),


        end_date: z.string().optional(),
        notes: z.string().optional(),

        frequency: z.nativeEnum(BillingFrequency).optional().default(BillingFrequency.MONTHLY),
        // has
        hasFixedAmount: z.boolean().default(false),
        amount: z.number({
        }).positive({
            message: 'Ingresa un monto válido.',
        }).optional(),
        auto_renew: z.boolean().default(false),
        next_billing_date: z.string().optional(),
        alert_days_before: z.number().optional(),

        hasWarranty: z.boolean().default(false),




    }).refine((data) => {
        if (data.period === ContractPeriod.RECURRING || data.period === ContractPeriod.FIXED_TERM) {
            return data.frequency !== undefined
        }

        return true;
    }, {
        message: 'Selecciona la frecuencia de facturación.',
        path: ['frequency'],
    }).refine((data) => {


        if (data.period === ContractPeriod.FIXED_TERM) {
            const diff = differenceInDays(new Date(data.end_date || ''), new Date(data.start_date || ''));
            if (diff === 0 && data.frequency === BillingFrequency.ONE_TIME) {
                return true;
            }

            const days = billingFrequencyDaysMap[data.frequency];
            if (!days) return true;

            if (diff < days) {
                return false;
            }

        }
        return true;
    }, {
        message: 'La frecuencia no es compatible con el periodo seleccionado.',
        path: ['frequency'],
    })


        .refine((data) => {
            if (data.period === ContractPeriod.RECURRING) {
                return data.hasFixedAmount === false ||
                    (data.amount !== undefined && data.amount > 0);
            }

            if (data.period === ContractPeriod.FIXED_TERM || data.period === ContractPeriod.ONE_TIME) {
                return data.amount !== undefined && data.amount > 0;
            }
            return true;
        }
            , {
                message: 'Ingresa un monto válido.',
                path: ['amount'],
            })

        // .refine


        .refine((data) => {
            if (data.period === ContractPeriod.RECURRING) {
                return data.next_billing_date !== undefined && data.next_billing_date !== '';
            }
            return true;
        }, {
            message: 'Selecciona la fecha del próximo cobro.',
            path: ['next_billing_date'],
        }).refine((data) => {
            if (data.period === ContractPeriod.RECURRING) {
                const start = new Date(data.start_date);
                const end = new Date(data.next_billing_date || '');

                return isSameDay(start, end) || isAfter(end, start);
            }
            return true;
        }, {
            message: 'La próxima fecha de facturación debe ser igual o posterior a la fecha de inicio.',
            path: ['next_billing_date'],
        })
        .refine((data) => {
            if ((data.period === ContractPeriod.RECURRING && data.auto_renew === false) || data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                return data.alert_days_before !== undefined;
            }
            return true;
        }, {
            message: 'Ingresa los días para la alerta.',
            path: ['alert_days_before'],
        })
        .refine((data) => {
            if (!data.alert_days_before) return true;
            if (data.period === ContractPeriod.RECURRING) {
                const diff = differenceInDays(new Date(data.next_billing_date || ''), new Date(data.start_date || ''));
                if (diff === 0 && data.frequency === BillingFrequency.ONE_TIME) {
                    return true;
                }
                return diff >= data.alert_days_before!;
            }

            if (data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                const diff = differenceInDays(new Date(data.end_date || ''), new Date(data.start_date || ''));
                if (diff === 0 && data.frequency === BillingFrequency.ONE_TIME) {
                    return true;
                }
                return diff >= data.alert_days_before!;
            }

            return true;
        }, {
            message: 'Los días para la alerta deben ser menores al tiempo restante del contrato.',
            path: ['alert_days_before'],
        })
        .refine((data) => {
            if (data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                return data.end_date !== undefined && data.end_date !== '';
            }

            return true;
        }, {
            message: 'Selecciona la fecha de fin.',
            path: ['end_date'],
        }).refine((data) => {
            if (data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                const start = new Date(data.start_date);
                const end = new Date(data.end_date || '');
                return isSameDay(start, end) || isAfter(end, start);
            }
            return true;
        }, {
            message: 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            path: ['end_date'],
        })

);


const initialValues = computed(() => {
    const amount = selectedContract.value?.billing?.amount;
    return {
        name: selectedContract.value?.name,
        provider: selectedContract.value?.provider,
        type: selectedContract.value?.type,
        period: selectedContract.value?.period,
        status: selectedContract.value?.status,
        currency: selectedContract.value?.billing?.currency || CurrencyType.PEN,
        start_date: selectedContract.value?.start_date,
        end_date: selectedContract.value?.end_date || '',
        notes: selectedContract.value?.notes || '',
        frequency: selectedContract.value?.billing?.frequency || BillingFrequency.MONTHLY,
        hasFixedAmount: !!selectedContract.value?.billing?.amount,
        amount: (amount !== undefined && amount !== null) ? Number(amount) : undefined,
        auto_renew: Boolean(selectedContract.value?.billing?.auto_renew) || false,
        next_billing_date: selectedContract.value?.billing?.next_billing_date || '',
        alert_days_before: selectedContract.value?.expiration?.alert_days_before,
        hasWarranty: !!selectedContract.value?.end_date,
    };



});

const { values, errors, handleSubmit, resetForm, setFieldValue, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: initialValues.value,
    keepValuesOnUnmount: true

});

onMounted(() => {
    echo.channel().notification((notification: {
        message: string,
        short: string,
        contract: Contract
    }) => {
        if (selectedContract.value && notification.contract.id === selectedContract.value.id) {
            if (selectedContract.value.billing?.next_billing_date) {
                selectedContract.value.billing.next_billing_date = notification.contract.billing?.next_billing_date || null;
                setValues({
                    ...values,
                    next_billing_date: notification.contract.billing?.next_billing_date || '',
                });
            }

        };
    });
});


watch(selectedContract, (contract) => {
    if (contract) {
        setValues(initialValues.value);
    } else {
        resetForm();
    }
});


const resetAndClose = () => {
    resetForm();
    selectedContract.value = null;
    open.value = false;
};

const handleSave = () => {
    let amount: number | null | undefined = values.amount;
    if (!values.hasFixedAmount && values.period === ContractPeriod.RECURRING) {
        amount = null;
    }

    let end_date = values.end_date;
    let alert_days_before = values.alert_days_before;
    if (!values.hasWarranty && values.period === ContractPeriod.ONE_TIME) {
        end_date = null;
        alert_days_before = null;
    }


    if (selectedContract.value) {
        router.put(`/admin-control/${selectedContract.value.id}`, {
            name: values.name,
            provider: values.provider,
            type: values.type,
            notes: values.notes,
            period: selectedContract.value.period, // El periodo no se puede cambiar al editar
            start_date: selectedContract.value.start_date,
            frequency: selectedContract.value.billing?.frequency,
            next_billing_date: selectedContract.value.billing?.next_billing_date,
            currency: selectedContract.value.billing?.currency,
            auto_renew: selectedContract.value.billing?.auto_renew,



        }, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                const contract = flash.contract as Contract;
                router.replaceProp('contracts', (contracts: Contract[]) => {
                    return contracts.map(c => c.id === contract.id ? contract : c);
                });
                resetAndClose();

            }
        });

        return;
    }

    router.post('/admin-control', values, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.success) {
                router.prependToProp('contracts', flash.contract);
                resetAndClose();
            }
        }

    });


};
</script>
