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

                <form v-else id="renew-contract-form" class="space-y-5" @submit.prevent="handleRenew">
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
                                <Badge class="text-[11px]" :class="getContractOp('type', form.type).bg">
                                    {{ getContractOp('type', form.type).label || 'Contrato' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('period', form.period).bg">
                                    {{ getContractOp('period', form.period).label || 'Periodo' }}
                                </Badge>
                                <Badge class="text-[11px]" :class="getContractOp('status', form.status).bg">
                                    {{ getContractOp('status', form.status).label || 'Estado' }}
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
                            <div class="space-y-2">
                                <Label>Fecha de inicio</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <Calendar class="h-4 w-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-model="form.start_date" type="date" />
                                </InputGroup>
                            </div>

                            <div v-if="form.period === ContractPeriod.ONE_TIME" class="space-y-2">
                                <Label>Incluye garantia</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="form.hasWarranty" />
                                    <span class="text-sm">{{ form.hasWarranty ? 'Si' : 'No' }}</span>
                                </div>
                            </div>

                            <div v-if="requiresEndDate" class="space-y-2">
                                <Label>Fecha de fin</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <CalendarCheck2 class="h-4 w-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-model="form.end_date" type="date" />
                                </InputGroup>
                            </div>

                            <div class="space-y-2">
                                <Label>Estado</Label>
                                <div class="relative">
                                    <ShieldCheck
                                        class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                    <Select v-model="form.status">
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
                            </div>

                            <div v-if="requiresAlertDays" class="space-y-2">
                                <Label>Dias antes de alerta</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <Bell class="h-4 w-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-model="form.alert_days_before" type="number" min="0"
                                        placeholder="Ej: 15" />
                                </InputGroup>
                            </div>
                        </div>
                    </div>

                    <div v-if="isRecurring" class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CreditCard class="h-4 w-4 text-primary" />
                            Facturacion y renovacion
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Frecuencia de facturacion</Label>
                                <div class="relative">
                                    <Repeat
                                        class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                    <Select v-model="form.frequency">
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
                            </div>

                            <div class="space-y-2">
                                <Label>Moneda</Label>
                                <div class="relative">
                                    <Coins
                                        class="h-4 w-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2" />
                                    <Select v-model="form.currency">
                                        <SelectTrigger class="pl-9 w-full">
                                            <SelectValue placeholder="Seleccionar moneda" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="currency in Object.values(CurrencyType)" :key="currency"
                                                :value="currency">
                                                {{ currency }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Monto fijo</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="form.hasFixedAmount" />
                                    <span class="text-sm">{{ form.hasFixedAmount ? 'Si' : 'No' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2" v-if="form.hasFixedAmount">
                                <Label>Monto</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <BadgeDollarSign class="h-4 w-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-model="form.amount" type="number" min="0" step="0.01"
                                        placeholder="0.00" />
                                </InputGroup>
                            </div>

                            <div class="space-y-2">
                                <Label>Auto-renovacion</Label>
                                <div class="flex items-center gap-3 rounded-lg border bg-background px-3 py-2">
                                    <Switch v-model:checked="form.auto_renew" />
                                    <span class="text-sm">{{ form.auto_renew ? 'Si' : 'No' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Proxima facturacion</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <CalendarCheck2 class="h-4 w-4" />
                                    </InputGroupAddon>
                                    <InputGroupInput v-model="form.next_billing_date" type="date" />
                                </InputGroup>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <StickyNote class="h-4 w-4 text-primary" />
                            Notas de renovacion
                        </div>
                        <div class="mt-4">
                            <Textarea v-model="form.notes" rows="3" placeholder="Notas, SLA, condiciones, etc." />
                        </div>
                    </div>
                </form>
            </ScrollArea>

            <DialogFooter class="mt-4">
                <Button variant="outline" @click="resetAndClose">Cancelar</Button>
                <Button class="gap-2" type="submit" form="renew-contract-form" :disabled="isLoading || !selectedContract">
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
import { type Contract, ContractPeriod, ContractStatus, ContractType, contractStatusOptions, getContractOp } from '@/interfaces/contract.interface';
import { BillingFrequency, billingFrequencyOptions, CurrencyType } from '@/interfaces/contractBilling.interface';
import { router } from '@inertiajs/core';
import { BadgeDollarSign, Bell, Calendar, CalendarCheck2, CalendarClock, Coins, CreditCard, RefreshCcw, Repeat, ShieldCheck, Sparkles, StickyNote } from 'lucide-vue-next';
import { computed, reactive, watch } from 'vue';

const open = defineModel<boolean>('open', { default: false });
const selectedContract = defineModel<Contract | null>('selectedContract', { default: null });

const { isLoading } = useApp();

const initialFormState = () => ({
    name: '',
    provider: '',
    type: ContractType.LICENSE,
    period: ContractPeriod.RECURRING,
    status: ContractStatus.ACTIVE,
    start_date: '',
    end_date: '',
    notes: '',
    frequency: BillingFrequency.MONTHLY,
    amount: undefined as number | undefined,
    currency: CurrencyType.PEN,
    auto_renew: false,
    next_billing_date: '',
    alert_days_before: undefined as number | undefined,
    hasFixedAmount: false,
    hasWarranty: false,
});

const form = reactive(initialFormState());

const syncForm = (contract: Contract) => {
    const amount = contract.billing?.amount;
    form.name = contract.name;
    form.provider = contract.provider;
    form.type = contract.type;
    form.period = contract.period;
    form.status = contract.status;
    form.start_date = contract.start_date || '';
    form.end_date = contract.end_date || '';
    form.notes = contract.notes || '';
    form.frequency = contract.billing?.frequency || BillingFrequency.MONTHLY;
    form.currency = contract.billing?.currency || CurrencyType.PEN;
    form.amount = amount !== null && amount !== undefined ? Number(amount) : undefined;
    form.auto_renew = Boolean(contract.billing?.auto_renew);
    form.next_billing_date = contract.billing?.next_billing_date || '';
    form.alert_days_before = contract.expiration?.alert_days_before ?? undefined;
    form.hasFixedAmount = amount !== null && amount !== undefined;
    form.hasWarranty = Boolean(contract.end_date);
};

const resetAndClose = () => {
    Object.assign(form, initialFormState());
    selectedContract.value = null;
    open.value = false;
};

watch(selectedContract, (contract) => {
    if (contract) {
        syncForm(contract);
    } else {
        Object.assign(form, initialFormState());
    }
});

const isRecurring = computed(() => form.period === ContractPeriod.RECURRING);
const requiresEndDate = computed(() => form.period === ContractPeriod.FIXED_TERM || (form.period === ContractPeriod.ONE_TIME && form.hasWarranty));
const requiresAlertDays = computed(() => (!form.auto_renew && form.period === ContractPeriod.RECURRING)
    || form.period === ContractPeriod.FIXED_TERM
    || (form.period === ContractPeriod.ONE_TIME && form.hasWarranty));

const handleRenew = () => {
    if (!selectedContract.value) return;

    let amount: number | null | undefined = form.amount;
    if (!form.hasFixedAmount && form.period === ContractPeriod.RECURRING) {
        amount = null;
    }

    let end_date: string | null | undefined = form.end_date;
    let alert_days_before: number | null | undefined = form.alert_days_before;

    if (!form.hasWarranty && form.period === ContractPeriod.ONE_TIME) {
        end_date = null;
        alert_days_before = null;
    }

    router.put(`/admin-control/${selectedContract.value.id}`, {
        ...form,
        amount,
        end_date,
        alert_days_before,
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
};
</script>
