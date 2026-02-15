<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { ContractPeriod, contractPeriodOptions, getContractOp, NotificationContract } from '@/interfaces/contract.interface';
import { parseDateOnly } from '@/lib/utils';
import { format, formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';
import { ShieldAlert } from 'lucide-vue-next';

const { notifications } = defineProps<{
    notifications: NotificationContract[];
}>();


const periodLabel = (period?: ContractPeriod) => {
    if (!period) return 'Contrato';
    return contractPeriodOptions[period]?.label || 'Contrato';
};

const alertTitle = (notification: NotificationContract) => {
    const message = JSON.parse(notification.data || '{}');
    const name = notification.contract?.name || 'Contrato sin nombre';
    const typeLabel = getContractOp('type', notification.contract?.type).label || 'Contrato';
    return `${typeLabel}: ${name} ${message.message || ''}`;
};

const alertSubtitle = (notification: NotificationContract) => {
    const provider = notification.contract?.provider ? notification.contract.provider : 'Proveedor no definido';
    const period = periodLabel(notification.contract?.period);
    return `${provider} · ${period}`;
};


const alertMeta = (notification: NotificationContract) => {
    const nextBilling = notification.contract?.billing?.next_billing_date;
    if (notification.contract?.billing?.next_billing_date && notification.contract.billing.auto_renew) {
        return `Próxima facturación: ${format(parseDateOnly(nextBilling), 'dd MMM yyyy', { locale: es })}`;
    } else if (notification.contract?.billing?.next_billing_date) {
        return `Vigencia hasta: ${format(parseDateOnly(nextBilling), 'dd MMM yyyy', { locale: es })}`;
    }
    if (notification.contract?.end_date) {
        return `Vence el ${format(parseDateOnly(notification.contract.end_date), 'dd MMM yyyy', { locale: es })}`;
    }
    return 'Revisar vigencia del contrato';
};


</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>Alertas activas</CardTitle>
            <CardDescription>Eventos que requieren seguimiento inmediato.</CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div v-if="notifications.length === 0" class="text-center text-sm text-muted-foreground">
                <Empty>
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <ShieldAlert />
                        </EmptyMedia>
                        <EmptyTitle>No hay alertas activas</EmptyTitle>
                        <EmptyDescription>
                            No se han reportado nuevas alertas esta semana.
                        </EmptyDescription>
                    </EmptyHeader>


                </Empty>
            </div>
            <div v-for="notification in notifications" :key="notification.id"
                class="p-2 rounded-lg border flex items-start gap-2"
                :class="[getContractOp('period', notification.contract?.period).card]">
                <component :is="getContractOp('type', notification.contract?.type).icon"
                    class="w-4 h-4 mt-0.5 shrink-0" />
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold wrap-break-word">
                        {{ alertTitle(notification) }}
                    </p>
                    <p class="text-[11px] opacity-80 mt-1">
                        {{ alertSubtitle(notification) }}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-1">

                        <Badge class="text-[10px]" :class="getContractOp('type', notification?.contract?.type).bg">
                            {{ getContractOp('type', notification?.contract?.type).label }}
                        </Badge>
                        <Badge class="text-[10px]" :class="getContractOp('period', notification?.contract?.period).bg">
                            <component :is="getContractOp('period', notification?.contract?.period).icon" />
                            {{ getContractOp('period', notification?.contract?.period).label }}
                        </Badge>
                    </div>
                    <p class="text-[11px] opacity-80 mt-1">
                        {{ alertMeta(notification) }}
                    </p>
                    <small class="text-[10px] block text-end opacity-60 mt-2">

                        {{ formatDistanceToNow(notification.created_at, { addSuffix: true, locale: es }) }}
                    </small>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
