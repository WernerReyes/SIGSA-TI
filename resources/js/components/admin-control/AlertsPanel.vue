<template>
    <Sheet>
        <div class="space-y-2">
            <Card class="border-warning/30 bg-warning/5 shadow-sm">
                <CardHeader class="flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm flex items-center gap-2">
                        <BellRing class="w-5 h-5 text-warning" />
                        Alertas activas ({{ unreadNotifications.length }})
                    </CardTitle>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" class="h-7 px-2" :disabled="!unreadNotifications.length" @click="markAllAsRead">
                            Marcar todo
                        </Button>
                        <SheetTrigger :as-child="true">
                            <Button variant="outline" size="sm" class="h-7 px-2">
                                Ver todas
                            </Button>
                        </SheetTrigger>
                    </div>
                </CardHeader>
                <CardContent class="pt-0 pb-2">
                    <Accordion type="single" default-value="active" collapsible class="w-full" >
                        <AccordionItem value="active" class="border-0">
                            <AccordionTrigger class="py-2 hover:no-underline">
                                <div class="flex w-full items-center justify-between">
                                    <span class="text-xs font-semibold">Ver alertas activas</span>
                                    <Badge variant="secondary" class="text-[10px]">
                                        {{ unreadNotifications.length }}
                                    </Badge>
                                </div>
                            </AccordionTrigger>
                            <AccordionContent class="pt-2">
                                <div v-if="!activeVisible.length" class="rounded-lg border border-dashed p-3 text-center text-xs text-muted-foreground">
                                    Sin alertas activas.
                                </div>
                                <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                    <div v-for="notification in activeVisible" :key="notification.id"
                                        class="p-2 rounded-lg border flex items-start gap-2"
                                        :class="[periodCardClass(notification.contract?.period), typeAccentClass(notification.contract?.type)]">
                                        <component :is="getContractOp('type', notification.contract?.type).icon" class="w-4 h-4 mt-0.5 shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold wrap-break-word">
                                                {{ alertTitle(notification) }}
                                            </p>
                                            <p class="text-[11px] opacity-80 mt-1">
                                                {{ alertSubtitle(notification) }}
                                            </p>
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                    :class="typeBadgeClass(notification.contract?.type)">
                                                    {{ getContractOp('type', notification.contract?.type).label || 'Contrato' }}
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                    :class="periodBadgeClass(notification.contract?.period)">
                                                    {{ periodLabel(notification.contract?.period) }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] opacity-80 mt-1">
                                                {{ alertMeta(notification) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="hasMoreActive" class="text-[11px] text-muted-foreground mt-2">
                                    +{{ unreadNotifications.length - activeVisible.length }} más en el panel completo
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>
        </div>

        <SheetContent side="right" class="w-full sm:w-105 p-0">
            <Card class="border-warning/30 bg-linear-to-br from-warning/10 via-background to-background shadow-sm h-full rounded-none sm:rounded-l-xl">
                <CardHeader class="flex-row items-center justify-between border-b border-warning/20 bg-warning/5 py-2">
                    <div class="space-y-1">
                        <CardTitle class="text-sm flex items-center gap-2">
                            <span class="h-7 w-7 rounded-full bg-warning/15 text-warning flex items-center justify-center">
                                <BellRing class="h-4 w-4" />
                            </span>
                            Centro de notificaciones
                        </CardTitle>
                        <CardDescription class="text-xs">
                            Contratos y alertas clave.
                        </CardDescription>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" class="h-7 px-2" :disabled="!unreadNotifications.length" @click="markAllAsRead">
                            Marcar todo
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="py-3">
                    <Accordion type="multiple" :default-value="['unread']" class="grid gap-2 max-h-130 overflow-y-auto pr-1">
                        <AccordionItem value="unread" class="border rounded-lg px-2">
                            <AccordionTrigger class="hover:no-underline py-2">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-[11px] font-semibold">No leídas</span>
                                    <Badge variant="secondary" class="text-[10px]">{{ unreadNotifications.length }}</Badge>
                                </div>
                            </AccordionTrigger>
                            <AccordionContent class="pt-2">
                                <div v-if="!unreadNotifications.length" class="rounded-xl border border-dashed p-3 text-center text-[10px] text-muted-foreground">
                                    No hay notificaciones nuevas.
                                </div>

                                <div v-else class="space-y-3">
                                    <div v-for="group in unreadGroups" :key="group.key" class="space-y-2 ">
                                        <div class="flex items-center gap-2 text-[10px] font-semibold"
                                            :class="typeHeaderClass(group.key)">
                                            <component :is="group.icon" class="w-3.5 h-3.5" />
                                            {{ group.label }}
                                            <Badge variant="outline" class="text-[10px]">
                                                {{ group.items.length }}
                                            </Badge>
                                        </div>
                                        <div class="grid gap-2">
                                            <div v-for="notification in group.items" :key="notification.id"
                                                class="p-2 rounded-lg border flex items-start gap-2"
                                                :class="[periodCardClass(notification.contract?.period), typeAccentClass(notification.contract?.type)]">
                                                <component :is="getContractOp('type', notification.contract?.type).icon" class="w-4 h-4 mt-0.5 shrink-0" />
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-semibold wrap-break-word">
                                                        {{ alertTitle(notification) }}
                                                    </p>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertSubtitle(notification) }}
                                                    </p>
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="typeBadgeClass(notification.contract?.type)">
                                                            {{ getContractOp('type', notification.contract?.type).label || 'Contrato' }}
                                                        </span>
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="periodBadgeClass(notification.contract?.period)">
                                                            {{ periodLabel(notification.contract?.period) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertMeta(notification) }}
                                                    </p>
                                                </div>
                                                <Button variant="outline" size="sm" class="h-6 px-2 text-[10px]" @click="markAsRead(notification)">
                                                    Leído
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="history" class="border rounded-lg px-2">
                            <AccordionTrigger class="hover:no-underline py-2">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-[11px] font-semibold">Historial</span>
                                    <Badge variant="outline" class="text-[10px]">{{ readNotifications.length }}</Badge>
                                </div>
                            </AccordionTrigger>
                            <AccordionContent class="pt-2">
                                <div v-if="!readNotifications.length" class="rounded-xl border border-dashed p-3 text-center text-[10px] text-muted-foreground">
                                    Aún no hay notificaciones leídas.
                                </div>

                                <div v-else class="space-y-3">
                                    <div v-for="group in readGroups" :key="group.key" class="space-y-2">
                                        <div class="flex items-center gap-2 text-[10px] font-semibold"
                                            :class="typeHeaderClass(group.key)">
                                            <component :is="group.icon" class="w-3.5 h-3.5" />
                                            {{ group.label }}
                                            <Badge variant="outline" class="text-[10px]">
                                                {{ group.items.length }}
                                            </Badge>
                                        </div>
                                        <div class="grid gap-2">
                                            <div v-for="notification in group.items" :key="notification.id"
                                                class="p-2 rounded-lg border flex items-start gap-2"
                                                :class="[periodCardClass(notification.contract?.period), typeAccentClass(notification.contract?.type)]">
                                                <component :is="getContractOp('type', notification.contract?.type).icon" class="w-4 h-4 mt-0.5 shrink-0" />
                                                <div class="min-w-0">
                                                    <p class="text-xs font-semibold wrap-break-word">
                                                        {{ alertTitle(notification) }}
                                                    </p>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertSubtitle(notification) }}
                                                    </p>
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="typeBadgeClass(notification.contract?.type)">
                                                            {{ getContractOp('type', notification.contract?.type).label || 'Contrato' }}
                                                        </span>
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="periodBadgeClass(notification.contract?.period)">
                                                            {{ periodLabel(notification.contract?.period) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertMeta(notification) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </CardContent>
            </Card>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { BellRing } from 'lucide-vue-next';
import { NotificationContract, getContractOp, contractPeriodOptions, ContractPeriod, ContractType, contractTypeOptions } from '@/interfaces/contract.interface';
import { computed } from 'vue';


const notifications = defineModel<NotificationContract[]>('notifications', {
    default: [],
    required: true
})



const unreadNotifications = computed(() => notifications.value.filter(n => !n.read_at));
const readNotifications = computed(() => notifications.value.filter(n => !!n.read_at));

const activeVisible = computed(() => unreadNotifications.value.slice(0, 4));
const hasMoreActive = computed(() => unreadNotifications.value.length > activeVisible.value.length);

const groupByType = (items: NotificationContract[]) => {
    const map = new Map<string, { key: string; label: string; icon: any; items: NotificationContract[] }>();
    items.forEach((notification) => {
        const typeKey = notification.contract?.type ?? 'OTHER';
        const option = contractTypeOptions[typeKey as ContractType];
        const label = option?.label ?? 'Otros';
        const icon = option?.icon ?? BellRing;
        if (!map.has(typeKey)) {
            map.set(typeKey, { key: typeKey, label, icon, items: [] });
        }
        map.get(typeKey)?.items.push(notification);
    });
    return Array.from(map.values());
};

const unreadGroups = computed(() => groupByType(unreadNotifications.value));
const readGroups = computed(() => groupByType(readNotifications.value));

const markAsRead = (notification: NotificationContract) => {
    if (!notification.read_at) {
        notification.read_at = new Date().toISOString();
    }
};

const markAllAsRead = () => {
    unreadNotifications.value.forEach(markAsRead);
};

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
    if (notification.contract?.billing?.next_billing_date) {
        return `Próxima facturación: ${notification.contract.billing.next_billing_date}`;
    }
    if (notification.contract?.end_date) {
        return `Vence el ${notification.contract.end_date}`;
    }
    return 'Revisar vigencia del contrato';
};

const periodCardClass = (period?: ContractPeriod) => {
    switch (period) {
        case ContractPeriod.RECURRING:
            return 'bg-indigo-500/10 text-indigo-700 border-indigo-300/40';
        case ContractPeriod.FIXED_TERM:
            return 'bg-amber-500/10 text-amber-700 border-amber-300/40';
        case ContractPeriod.ONE_TIME:
            return 'bg-emerald-500/10 text-emerald-700 border-emerald-300/40';
        default:
            return 'bg-muted/40 text-foreground border-muted';
    }
};

const typeAccentClass = (type?: ContractType) => {
    switch (type) {
        case ContractType.LICENSE:
            return 'border-l-4 border-l-blue-500';
        case ContractType.SERVICE:
            return 'border-l-4 border-l-green-500';
        case ContractType.SUPPORT:
            return 'border-l-4 border-l-yellow-500';
        case ContractType.HARDWARE:
            return 'border-l-4 border-l-purple-500';
        case ContractType.OTHER:
        default:
            return 'border-l-4 border-l-slate-400';
    }
};

const typeHeaderClass = (type?: string) => {
    switch (type) {
        case ContractType.LICENSE:
            return 'text-blue-600';
        case ContractType.SERVICE:
            return 'text-green-600';
        case ContractType.SUPPORT:
            return 'text-yellow-600';
        case ContractType.HARDWARE:
            return 'text-purple-600';
        default:
            return 'text-muted-foreground';
    }
};

const typeBadgeClass = (type?: ContractType) => {
    switch (type) {
        case ContractType.LICENSE:
            return 'border-blue-300/60 bg-blue-500/10 text-blue-700';
        case ContractType.SERVICE:
            return 'border-green-300/60 bg-green-500/10 text-green-700';
        case ContractType.SUPPORT:
            return 'border-yellow-300/60 bg-yellow-500/10 text-yellow-700';
        case ContractType.HARDWARE:
            return 'border-purple-300/60 bg-purple-500/10 text-purple-700';
        default:
            return 'border-slate-300/60 bg-slate-500/10 text-slate-700';
    }
};

const periodBadgeClass = (period?: ContractPeriod) => {
    switch (period) {
        case ContractPeriod.RECURRING:
            return 'border-indigo-300/60 bg-indigo-500/10 text-indigo-700';
        case ContractPeriod.FIXED_TERM:
            return 'border-amber-300/60 bg-amber-500/10 text-amber-700';
        case ContractPeriod.ONE_TIME:
            return 'border-emerald-300/60 bg-emerald-500/10 text-emerald-700';
        default:
            return 'border-slate-300/60 bg-slate-500/10 text-slate-700';
    }
};



// defineProps<{
//     notifications: NotificationContract[];
// }>()
</script>
