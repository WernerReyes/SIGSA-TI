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
                    Renovacion de contrato
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
                            <!-- <div class="flex flex-wrap gap-2">
                                <Badge class="text-[11px]" :class="getContractOp('type', form.type).bg">
                                    {{ getContractOp('type', form.type).label || 'Contrato' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('period', form.period).bg">
                                    {{ getContractOp('period', form.period).label || 'Periodo' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('status', form.status).bg">
                                    {{ getContractOp('status', form.status).label || 'Estado' }}
                                </Badge>
                            </div> -->
                        </div>
                    </div>

                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CalendarClock class="h-4 w-4 text-primary" />
                            Vigencia renovada
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4">
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








                        </div>
                    </div>


                   

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
                


                    <VeeField name="notes" v-slot="{ componentField, errors }">
                        <div class="rounded-lg border bg-muted/30 p-4">
                            <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <StickyNote class="h-4 w-4 text-primary" />
                                Notas de renovación
                            </div>


                            <div class="mt-4">

                                <Textarea v-bind="componentField" rows="3"
                                    placeholder="Notas, SLA, condiciones, etc." />
                            </div>
                        </div>
                        <FieldError :errors="errors" />
                    </VeeField>




                </form>
            </ScrollArea>

            <DialogFooter class="mt-4">
                <!-- <Button variant="outline" @click="resetAndClose">Cancelar</Button> -->
                <Button class="gap-2" type="submit" form="renew-contract-form"
                    :disabled="isLoading || !selectedContract || Object.keys(errors).length > 0">
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import { type Contract, ContractPeriod, contractPeriodOptions } from '@/interfaces/contract.interface';
import { BillingFrequency, billingFrequencyDaysMap, billingFrequencyOptions, CurrencyType } from '@/interfaces/contractBilling.interface';
import { ContractRenewal } from '@/interfaces/contractRenewal.interface';
import { router } from '@inertiajs/core';
import { toTypedSchema } from '@vee-validate/zod';
import { differenceInDays, isAfter, isSameDay } from 'date-fns';
import { BadgeDollarSign, Bell, CalendarCheck2, CalendarClock, Coins, CreditCard, Layers, RefreshCcw, Repeat, Sparkles, StickyNote } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, watch } from 'vue';
import z from 'zod';
import { FieldError } from '@/components/ui/field';

const open = defineModel<boolean>('open', { default: false });
const selectedContract = defineModel<Contract | null>('selectedContract', { default: null });

const { isLoading } = useApp();


const formSchema = toTypedSchema(
    z.object({
        period: z.nativeEnum(ContractPeriod, {
            errorMap: () => ({ message: 'Selecciona una forma de contrato.' }),
            message: 'Selecciona una forma de contrato.',
        }),
        currency: z.nativeEnum(CurrencyType, {
            errorMap: () => ({ message: 'Selecciona una moneda.' }),
            message: 'Selecciona una moneda.',
        }).default(CurrencyType.PEN),

        // start_date: z.string({
        //     message: 'Selecciona la fecha de inicio.',
        // }).min(1, 'Selecciona la fecha de inicio.'),


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
            const diff = differenceInDays(new Date(data.end_date || ''), new Date());
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
                const start = new Date();
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
                const diff = differenceInDays(new Date(data.next_billing_date || ''), new Date());
                if (diff === 0 && data.frequency === BillingFrequency.ONE_TIME) {
                    return true;
                }
                return diff >= data.alert_days_before!;
            }

            if (data.period === ContractPeriod.FIXED_TERM || (data.period === ContractPeriod.ONE_TIME && data.hasWarranty)) {
                const diff = differenceInDays(new Date(data.end_date || ''), new Date());
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
                const start = new Date();
                const end = new Date(data.end_date || '');
                return isSameDay(start, end) || isAfter(end, start);
            }
            return true;
        }, {
            message: 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            path: ['end_date'],
        }).refine((data) => {
            if (!data.end_date) return true;
            const old_end_date = new Date(selectedContract.value?.end_date || '');
            const new_end_date = new Date(data.end_date);
            if (data.period === ContractPeriod.FIXED_TERM) {
                return isSameDay(old_end_date, new_end_date) || isAfter(new_end_date, old_end_date);
            }
            if (data.period === ContractPeriod.ONE_TIME && data.hasWarranty) {
                return isSameDay(old_end_date, new_end_date) || isAfter(new_end_date, old_end_date);
            }
        }, {
            message: 'La nueva fecha de fin debe ser igual o posterior a la fecha de fin actual.',
        }
    
    )

);


const initialValues = computed(() => {
    const amount = selectedContract.value?.billing?.amount;
    return {
        period: selectedContract.value?.period,
        currency: selectedContract.value?.billing?.currency || CurrencyType.PEN,
        end_date: '',
        notes:  '',
        frequency: selectedContract.value?.billing?.frequency || BillingFrequency.MONTHLY,
        hasFixedAmount: !!selectedContract.value?.billing?.amount,
        amount: (amount !== undefined && amount !== null) ? Number(amount) : undefined,
        auto_renew: Boolean(selectedContract.value?.billing?.auto_renew) || false,
        next_billing_date: '',
        alert_days_before: selectedContract.value?.expiration?.alert_days_before,
        hasWarranty: !!selectedContract.value?.end_date,
    };



});

const { values, errors, handleSubmit, resetForm, setFieldValue, setValues } = useForm({
    validationSchema: formSchema,
    initialValues: initialValues.value,
    keepValuesOnUnmount: true

});

type FormValues = typeof values;


watch(selectedContract, (newContract) => {
    if (newContract) {
        setValues(initialValues.value);
    } else {
        resetForm();
    }
});


const resetAndClose = () => {
    resetForm();
    open.value = false;
    selectedContract.value = null;
};

const handleRenew = (values: FormValues) => {
    if (!selectedContract.value) return;

    router.post(`/admin-control/${selectedContract.value.id}/renew`, values, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            resetAndClose();

            const contract = flash.contract as Contract;
            const renewal = flash.renewal as ContractRenewal;

            router.replaceProp('contracts', (prev: Contract[]) => {
                if (!prev) return prev;
                return prev.map((c) => {
                    if (c.id === contract.id) {
                        return {
                            ...c,
                            ...contract,
                            renewals: [...(c.renewals || []), renewal],
                        }
                    }
                    return c;
                });
                
            });
        },
    });

 
};
</script>

<!-- <template>
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
                                    <component :is="getContractOp('type', selectedContract.type).icon" />
                                    {{ getContractOp('type', selectedContract.type).label || 'Contrato' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('period', selectedContract.period).bg">
                                    <component :is="getContractOp('period', selectedContract.period).icon" />
                                    {{ getContractOp('period', selectedContract.period).label || 'Periodo' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('status', selectedContract.status).bg">
                                    <component :is="getContractOp('status', selectedContract.status).icon" />
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
import FieldError from '@/components/ui/field/FieldError.vue';

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
</script> -->
