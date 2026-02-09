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
                        <Button variant="outline" size="sm" class="h-7 px-2" :disabled="!unreadNotifications.length || isLoading"
                            @click="markAllAsRead">
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
                    <Accordion type="single" default-value="active" collapsible class="w-full">
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
                                <div v-if="!activeVisible.length"
                                    class="rounded-lg border border-dashed p-3 text-center text-xs text-muted-foreground">
                                    Sin alertas activas.
                                </div>
                                <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                    <div v-for="notification in activeVisible" :key="notification.id"
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
                                                
                                                <Badge  class="text-[10px]"
                                                    :class="getContractOp('type', notification?.contract.type).bg">
                                                    {{ getContractOp('type', notification?.contract.type).label}}
                                                </Badge>
                                                <Badge class="text-[10px]" :class="getContractOp('period', notification?.contract.period).bg">
                                                     <component :is="getContractOp('period', notification?.contract.period).icon"  />
                                                    {{ getContractOp('period', notification?.contract.period).label }}
                                                </Badge>
                                            </div>
                                            <p class="text-[11px] opacity-80 mt-1">
                                                {{ alertMeta(notification) }}
                                            </p>
                                            <small class="text-[10px] block text-end opacity-60 mt-2">
                                                {{ format(new Date(notification.created_at), 'dd MMM yyyy, HH:mm', {
                                                    locale: es
                                                }) }}
                                            </small>
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
            <Card
                class="border-warning/30 bg-linear-to-br from-warning/10 via-background to-background shadow-sm h-full rounded-none sm:rounded-l-xl">
                <CardHeader class="flex-row items-center justify-between border-b border-warning/20 bg-warning/5 py-2">
                    <div class="space-y-1">
                        <CardTitle class="text-sm flex items-center gap-2">
                            <span
                                class="h-7 w-7 rounded-full bg-warning/15 text-warning flex items-center justify-center">
                                <BellRing class="h-4 w-4" />
                            </span>
                            Centro de notificaciones
                        </CardTitle>
                        <CardDescription class="text-xs">
                            Contratos y alertas clave.
                        </CardDescription>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" class="h-7 px-2" :disabled="!unreadNotifications.length || isLoading"
                            @click="markAllAsRead">
                            Marcar todo
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="py-3">
                    <Accordion type="multiple" :default-value="['unread']"
                        class="grid gap-2 max-h-130 overflow-y-auto pr-1">
                        <AccordionItem value="unread" class="border rounded-lg px-2">
                            <AccordionTrigger class="hover:no-underline py-2">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-[11px] font-semibold">No leídas</span>
                                    <Badge variant="secondary" class="text-[10px]">{{ unreadNotifications.length }}
                                    </Badge>
                                </div>
                            </AccordionTrigger>
                            <AccordionContent class="pt-2">
                                <div v-if="!unreadNotifications.length"
                                    class="rounded-xl border border-dashed p-3 text-center text-[10px] text-muted-foreground">
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
                                                :class="[getContractOp('period', notification.contract?.period).card]">
                                                <component :is="getContractOp('type', notification.contract?.type).icon"
                                                    class="w-4 h-4 mt-0.5 shrink-0" />
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-4 justify-between">

                                                        <p class="text-xs font-semibold wrap-break-word">
                                                            {{ alertTitle(notification) }}
                                                        </p>
                                                        <div class="flex items-center gap-1">
                                                            <Button
                                                                :disabled="isLoading"
                                                                variant="outline"
                                                                size="sm"
                                                                class="h-6 px-2 text-[10px]"
                                                                @click="openDetails(notification)"
                                                            >
                                                                Detalles
                                                            </Button>
                                                            <Button
                                                                :disabled="isLoading"
                                                                variant="outline"
                                                                size="sm"
                                                                class="h-6 px-2 text-[10px]"
                                                                @click="markAsRead(notification)"
                                                            >
                                                                Leído
                                                            </Button>
                                                        </div>
                                                    </div>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertSubtitle(notification) }}
                                                    </p>
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="typeBadgeClass(notification.contract?.type)">
                                                            {{ getContractOp('type', notification.contract?.type).label }}
                                                        </span>
                                                        <!-- <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="periodBadgeClass(notification.contract?.period)">
                                                            {{ periodLabel(notification.contract?.period) }}
                                                        </span> -->
                                                    </div>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertMeta(notification) }}
                                                    </p>
                                                    <small class="text-[10px] block text-end opacity-60 mt-2">
                                                        {{ format(new Date(notification.created_at),
                                                            'dd MMM yyyy, HH:mm',
                                                            {
                                                                locale: es
                                                            }) }}
                                                    </small>
                                                </div>

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
                                <div v-if="!readNotifications.length"
                                    class="rounded-xl border border-dashed p-3 text-center text-[10px] text-muted-foreground">
                                    Aún no hay notificaciones leídas.
                                </div>

                                <div v-else class="space-y-3">
                                    <div class="flex items-center justify-end">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="h-6 px-2 text-[10px] border-red-300/60 text-red-700 hover:bg-red-50"
                                            :disabled="!readNotifications.length || isLoading"
                                            @click="deleteReadHistory"
                                        >
                                            Eliminar todo
                                        </Button>
                                    </div>
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
                                                :class="[getContractOp('period', notification.contract?.period).card, getContractOp('type', notification.contract?.type).bg]">
                                                <component :is="getContractOp('type', notification.contract?.type).icon"
                                                    class="w-4 h-4 mt-0.5 shrink-0" />
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-4 justify-between">
                                                        <p class="text-xs font-semibold wrap-break-word">
                                                            {{ alertTitle(notification) }}
                                                        </p>
                                                        <div class="flex items-center gap-1">
                                                            <Button
                                                                :disabled="isLoading"
                                                                variant="outline"
                                                                size="sm"
                                                                class="h-6 px-2 text-[10px]"
                                                                @click="openDetails(notification)"
                                                            >
                                                                Detalles
                                                            </Button>
                                                            <Button
                                                                :disabled="isLoading"
                                                                variant="outline"
                                                                size="sm"
                                                                class="h-6 px-2 text-[10px] border-red-300/60 text-red-700 hover:bg-red-50"
                                                                @click="deleteNotification(notification)"
                                                            >
                                                                Eliminar
                                                            </Button>
                                                        </div>
                                                    </div>
                                                    <p class="text-[11px] opacity-80 mt-1">
                                                        {{ alertSubtitle(notification) }}
                                                    </p>
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="typeBadgeClass(notification.contract?.type)">
                                                            {{ getContractOp('type', notification.contract?.type).label
                                                                || 'Contrato' }}
                                                        </span>
                                                        <!-- <span class="text-[10px] px-2 py-0.5 rounded-full border"
                                                            :class="periodBadgeClass(notification.contract?.period)">
                                                            {{ periodLabel(notification.contract?.period) }}
                                                        </span> -->
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

    <Dialog v-model:open="detailOpen" @update:open="(val) => {
        if (!val) {
            selectedNotification = null;
        }
    }">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <BellRing class="h-4 w-4 text-warning" />
                    Detalle de notificación
                </DialogTitle>
                <DialogDescription class="text-xs">
                    Revisa la información de la alerta y el contrato asociado.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <div class="rounded-lg border bg-muted/30 p-3">
                    <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                        Notificación
                    </p>
                    <p class="text-sm font-semibold mt-1">
                        {{ selectedNotification ? alertTitle(selectedNotification) : 'Sin título' }}
                    </p>
                    <p class="text-[11px] opacity-80 mt-1">
                        {{ selectedNotification ? alertSubtitle(selectedNotification) : '' }}
                    </p>
                    <p class="text-[11px] opacity-80 mt-2">
                        {{ notificationMessage(selectedNotification) || 'Sin mensaje adicional.' }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <Badge variant="outline" class="text-[10px]">
                            {{ selectedNotification?.read_at ? 'Leída' : 'No leída' }}
                        </Badge>
                        <Badge variant="outline" class="text-[10px]">
                            {{ selectedNotification ? format(new Date(selectedNotification.created_at), 'dd MMM yyyy, HH:mm', { locale: es }) : '' }}
                        </Badge>
                    </div>
                </div>

                <div class="rounded-lg border p-3" v-if="selectedNotification?.contract">
                    <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                        Contrato
                    </p>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold">{{ selectedNotification.contract.name }}</p>
                                <p class="text-[11px] opacity-80">{{ selectedNotification.contract.provider }}</p>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <Badge  class="text-[10px]"
                                    :class="getContractOp('type', selectedNotification?.contract.type).bg">
                                    <component :is="getContractOp('type', selectedNotification?.contract.type).icon"  />
                                    {{ getContractOp('type', selectedNotification?.contract.type).label}}
                                </Badge>
                                <Badge  class="text-[10px]"
                                    :class="getContractOp('period', selectedNotification?.contract.period).bg">
                                    <component :is="getContractOp('period', selectedNotification?.contract.period).icon"  />
                                    {{ getContractOp('period', selectedNotification?.contract.period).label }}
                                </Badge>
                                <Badge class="text-[10px]" :class="getContractOp('status', selectedNotification?.contract.status).bg">
                                     <component :is="getContractOp('status', selectedNotification?.contract.status).icon"  />
                                    {{ getContractOp('status', selectedNotification?.contract.status).label }}
                                </Badge>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                            <div class="rounded-md border px-2 py-1">
                                <span class="text-muted-foreground">Inicio:</span>
                                {{ selectedNotification.contract.start_date || 'Sin fecha' }}
                            </div>
                            <div class="rounded-md border px-2 py-1">
                                <span class="text-muted-foreground">Fin:</span>
                                {{ selectedNotification.contract.end_date || 'Sin fecha' }}
                            </div>
                            <div class="rounded-md border px-2 py-1" v-if="selectedNotification.contract.billing?.next_billing_date">
                                <span class="text-muted-foreground">Próxima facturación:</span>
                                {{ selectedNotification.contract.billing.next_billing_date }}
                            </div>
                            <div class="rounded-md border px-2 py-1" v-if="selectedNotification.contract.notes">
                                <span class="text-muted-foreground">Notas:</span>
                                {{ selectedNotification.contract.notes }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-dashed p-3 text-center text-[11px] text-muted-foreground"
                    v-else>
                    Esta notificación no tiene contrato asociado.
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { BellRing } from 'lucide-vue-next';
import { NotificationContract, getContractOp, contractPeriodOptions, ContractPeriod, ContractType, contractTypeOptions } from '@/interfaces/contract.interface';
import { computed, ref } from 'vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { router } from '@inertiajs/vue3';
import { useApp } from '@/composables/useApp';
import { NotificationEntity } from '@/interfaces/notification.interface';

import { parseDateOnly } from '@/lib/utils';


const notifications = defineModel<NotificationContract[]>('notifications', {
    default: [],
    required: true
})

const {  isLoading } = useApp();

const detailOpen = ref(false);
const selectedNotification = ref<NotificationContract | null>(null);

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
    router.patch(`/notifications/${notification.id}/mark-as-read`, {}, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            notification.read_at = new Date().toISOString();
        }
    });
};

const markAllAsRead = () => {
    router.patch(`/notifications/mark-all-as-read`, {
        type: NotificationEntity.CONTRACT
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            notifications.value = notifications.value.map(n => ({ ...n, read_at: new Date().toISOString() }));
        }
    });
};

const openDetails = (notification: NotificationContract) => {
    selectedNotification.value = notification;
    detailOpen.value = true;
};

const deleteNotification = (notification: NotificationContract) => {
    router.delete(`/notifications/${notification.id}`, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            notifications.value = notifications.value.filter(n => n.id !== notification.id);
        }
    });
};

const deleteReadHistory = () => {
    router.delete(`/notifications/read`, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            notifications.value = notifications.value.filter(n => !n.read_at);
        }
    });
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
    }  else if (notification.contract?.billing?.next_billing_date) {
        return `Vigencia hasta: ${format(parseDateOnly(nextBilling), 'dd MMM yyyy', { locale: es })}`;
    }
    if (notification.contract?.end_date) {
        return `Vence el ${format(parseDateOnly(notification.contract.end_date), 'dd MMM yyyy', { locale: es })}`;
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



</script>
