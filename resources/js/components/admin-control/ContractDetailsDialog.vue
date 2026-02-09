<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            selectedContract = null;
        }
    }">
        <DialogContent class="sm:max-w-4xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <FileText class="h-5 w-5 text-primary" />
                    Detalle de contrato
                </DialogTitle>
                <DialogDescription>
                    Informacion completa del contrato y su historial de notificaciones.
                </DialogDescription>
            </DialogHeader>

            <ScrollArea class="max-h-[calc(100vh-14rem)] pr-2">
                <div v-if="!selectedContract"
                    class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                    Selecciona un contrato para ver los detalles.
                </div>

                <div v-else class="space-y-4">
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-[11px] font-mono text-muted-foreground">
                                    CTR-{{ selectedContract.id.toString().padStart(3, '0') }}
                                </p>
                                <p class="text-base font-semibold text-foreground">
                                    {{ selectedContract.name }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ selectedContract.provider || 'Proveedor no definido' }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Badge class="text-[11px]" :class="getContractOp('type', selectedContract.type).bg">
                                    {{ getContractOp('type', selectedContract.type).label || 'Contrato' }}
                                </Badge>
                                <Badge class="text-[11px]"
                                    :class="getContractOp('period', selectedContract.period).bg">
                                    {{ getContractOp('period', selectedContract.period).label || 'Periodo' }}
                                </Badge>
                                <Badge class="text-[11px]"
                                    :class="getContractOp('status', selectedContract.status).bg">
                                    {{ getContractOp('status', selectedContract.status).label || 'Estado' }}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-lg border bg-muted/30 p-4 space-y-3">
                            <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                                Fechas y estado
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Inicio:</span>
                                    {{ formatDate(selectedContract.start_date) }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Fin:</span>
                                    {{ formatDate(selectedContract.end_date) }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Creado:</span>
                                    {{ formatDateTime(selectedContract.created_at) }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Actualizado:</span>
                                    {{ formatDateTime(selectedContract.updated_at) }}
                                </div>
                                <div v-if="selectedContract.expiration" class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Vencimiento:</span>
                                    {{ formatDate(selectedContract.expiration.expiration_date) }}
                                </div>
                                <div v-if="selectedContract.expiration" class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Alerta (dias):</span>
                                    {{ selectedContract.expiration.alert_days_before }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border bg-muted/30 p-4 space-y-3">
                            <div class="flex items-center gap-2 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                                <CreditCard class="h-4 w-4 text-primary" />
                                Facturacion
                            </div>
                            <div v-if="selectedContract.billing" class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Frecuencia:</span>
                                    {{ billingLabel(selectedContract.billing.frequency) }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Monto:</span>
                                    {{ formatAmount(selectedContract.billing.amount, selectedContract.billing.currency) }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Auto-renovacion:</span>
                                    {{ selectedContract.billing.auto_renew ? 'Si' : 'No' }}
                                </div>
                                <div class="rounded-md border px-2 py-1">
                                    <span class="text-muted-foreground">Proxima facturacion:</span>
                                    {{ formatDate(selectedContract.billing.next_billing_date) }}
                                </div>
                            </div>
                            <div v-else class="rounded-md border border-dashed px-2 py-2 text-[11px] text-muted-foreground">
                                Sin informacion de facturacion.
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-muted/30 p-4">
                        <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                            Notas
                        </p>
                        <p class="text-sm mt-2">
                            {{ selectedContract.notes || 'Sin notas registradas.' }}
                        </p>
                    </div>

                    <div class="rounded-lg border bg-card/70 p-4">
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                            <RotateCcw class="h-4 w-4 text-primary" />
                            Historial de renovaciones
                        </div>

                        <div v-if="contractRenewals.length" class="mt-3 space-y-3">
                            <div v-for="renewal in contractRenewals" :key="renewal.id"
                                class="rounded-lg border bg-muted/30 p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold">
                                            Renovacion {{ formatDate(renewal.new_end_date) }}
                                        </p>
                                        <p class="text-[11px] text-muted-foreground">
                                            Antes: {{ formatDate(renewal.old_end_date) }} · Nuevo: {{ formatDate(renewal.new_end_date) }}
                                        </p>
                                    </div>
                                    <Badge variant="outline" class="text-[10px]">
                                        {{ renewal.renewed_by?.full_name || 'Automatica' }}
                                    </Badge>
                                </div>
                                <p class="text-[11px] opacity-80 mt-2">
                                    {{ renewal.notes || 'Sin notas.' }}
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2 text-[10px]">
                                    <Badge variant="outline">
                                        {{ formatDateTime(renewal.created_at) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mt-3 rounded-md border border-dashed px-3 py-4 text-center text-[11px] text-muted-foreground">
                            No hay renovaciones registradas.
                        </div>
                    </div>

                    <div class="rounded-lg border bg-card/70 p-4">
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                            <BellRing class="h-4 w-4 text-warning" />
                            Historial de notificaciones
                        </div>

                        <div v-if="contractNotifications.length" class="mt-3 space-y-3">
                            <div v-for="notification in contractNotifications" :key="notification.id"
                                class="rounded-lg border bg-muted/30 p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold">
                                            {{ alertTitle(notification) }}
                                        </p>
                                        <p class="text-[11px] text-muted-foreground">
                                            {{ alertSubtitle(notification) }}
                                        </p>
                                    </div>
                                    <Badge variant="outline" class="text-[10px]">
                                        {{ notification.read_at ? 'Leida' : 'No leida' }}
                                    </Badge>
                                </div>
                                <p class="text-[11px] opacity-80 mt-2">
                                    {{ notificationMessage(notification) || 'Sin mensaje adicional.' }}
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2 text-[10px]">
                                    <Badge variant="outline">
                                        {{ formatDateTime(notification.created_at) }}
                                    </Badge>
                                    <Badge variant="outline" v-if="notification.contract">
                                        {{ periodLabel(notification.contract.period) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mt-3 rounded-md border border-dashed px-3 py-4 text-center text-[11px] text-muted-foreground">
                            No hay notificaciones asociadas a este contrato.
                        </div>
                    </div>
                </div>
            </ScrollArea>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { type Contract, ContractPeriod, getContractOp, type NotificationContract, contractPeriodOptions } from '@/interfaces/contract.interface';
import { getContractBillOp, type BillingFrequency, type CurrencyType } from '@/interfaces/contractBilling.interface';
import { parseDateOnly } from '@/lib/utils';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { BellRing, CreditCard, FileText, RotateCcw } from 'lucide-vue-next';
import { computed } from 'vue';

const open = defineModel<boolean>('open', { default: false });
const selectedContract = defineModel<Contract | null>('selectedContract', { default: null });

const props = defineProps<{
    notifications: NotificationContract[];
}>();

const contractNotifications = computed(() => {
    if (!selectedContract.value) return [];
    const contractId = selectedContract.value.id;
    return props.notifications
        .filter((notification) => (notification.contract?.id ?? notification.entity_id) === contractId)
        .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
});

const contractRenewals = computed(() => {
    if (!selectedContract.value?.renewals?.length) return [];
    return [...selectedContract.value.renewals]
        .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
});

const formatDate = (value?: string | Date | null) => {
    if (!value) return 'N/A';
    if (value instanceof Date) {
        return format(value, 'dd/MM/yyyy');
    }
    return format(parseDateOnly(value), 'dd/MM/yyyy');
};

const formatDateTime = (value?: string | Date | null) => {
    if (!value) return 'N/A';
    const date = value instanceof Date ? value : new Date(value);
    if (Number.isNaN(date.getTime())) return 'N/A';
    return format(date, 'dd MMM yyyy, HH:mm', { locale: es });
};

const formatAmount = (amount: number | null, currency?: CurrencyType) => {
    if (amount === null || amount === undefined || !currency) return 'Monto variable';
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency }).format(amount);
};

const billingLabel = (frequency?: BillingFrequency | null) => {
    return getContractBillOp('frequency', frequency).label || 'Sin frecuencia';
};

const notificationMessage = (notification?: NotificationContract | null) => {
    if (!notification?.data) return '';
    try {
        const payload = JSON.parse(notification.data) as { message?: string };
        return payload.message || '';
    } catch (error) {
        return '';
    }
};

const periodLabel = (period?: ContractPeriod) => {
    if (!period) return 'Contrato';
    return contractPeriodOptions[period]?.label || 'Contrato';
};

const alertTitle = (notification: NotificationContract) => {
    const message = JSON.parse(notification.data || '{}') as { message?: string };
    const name = notification.contract?.name || selectedContract.value?.name || 'Contrato sin nombre';
    const typeLabel = getContractOp('type', notification.contract?.type || selectedContract.value?.type).label || 'Contrato';
    return `${typeLabel}: ${name} ${message.message || ''}`.trim();
};

const alertSubtitle = (notification: NotificationContract) => {
    const provider = notification.contract?.provider || selectedContract.value?.provider || 'Proveedor no definido';
    const period = periodLabel(notification.contract?.period || selectedContract.value?.period);
    return `${provider} · ${period}`;
};
</script>
