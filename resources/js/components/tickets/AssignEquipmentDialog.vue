<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            ticket = null;
            open = false;
        }
    }">
        <DialogContent class="max-w-6xl bg-muted/30">
            <!-- Header -->
            <div class="rounded-2xl border bg-card p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <Badge variant="outline" class="text-xs">TK-{{ ticket?.id }}</Badge>
                            <Badge :class="priorityBadgeClass" class="text-xs">{{ priorityLabel }}</Badge>
                            <Badge :class="statusBadgeClass" class="text-xs">{{ statusLabel }}</Badge>
                        </div>
                        <DialogTitle class="text-xl font-semibold">
                            {{ ticket?.title || 'Solicitud de equipo' }}
                        </DialogTitle>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                            <span><strong>Solicitante:</strong> {{ ticket?.requester?.full_name || '—' }}</span>
                            <span>•</span>
                            <span><strong>Técnico:</strong> {{ ticket?.responsible?.full_name || 'Sin asignar' }}</span>
                            <span>•</span>
                            <span><strong>SLA:</strong> {{ slaLabel }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button variant="outline" size="sm" :disabled="disableActions"
                            @click="handleStatusAction('RESOLVED')">
                            Resolver
                        </Button>
                        <Button variant="outline" size="sm" :disabled="disableActions"
                            @click="handleStatusAction('IN_PROGRESS')">
                            Escalar
                        </Button>
                        <Button size="sm" :disabled="disableActions"
                            @click="handleStatusAction('CLOSED')">
                            Cerrar
                        </Button>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-[1.4fr_1fr]">
                <!-- Left column -->
                <div class="space-y-4">
                    <!-- Current user assets panel -->
                    <div class="rounded-2xl border bg-card p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold">Activos actuales del usuario</h3>
                            <Badge variant="secondary" class="text-xs">{{ activeUserAssets.length }} activos</Badge>
                        </div>

                        <div v-if="activeUserAssets.length" class="mt-3 overflow-hidden rounded-xl border">
                            <div class="grid grid-cols-[2fr_1fr_1fr_1fr_auto] bg-muted/50 text-xs font-semibold text-muted-foreground">
                                <div class="px-3 py-2">Activo</div>
                                <div class="px-3 py-2">Tipo</div>
                                <div class="px-3 py-2">Serie</div>
                                <div class="px-3 py-2">Estado</div>
                                <div class="px-3 py-2 text-right">Acción</div>
                            </div>
                            <div class="divide-y">
                                <div v-for="assignment in activeUserAssets" :key="assignment.id"
                                    class="grid grid-cols-[2fr_1fr_1fr_1fr_auto] items-center text-xs">
                                    <div class="px-3 py-2 font-medium">{{ assignment.asset?.full_name || assignment.asset?.name || '—' }}</div>
                                    <div class="px-3 py-2 text-muted-foreground">{{ assignment.asset?.type?.name || '—' }}</div>
                                    <div class="px-3 py-2 text-muted-foreground">{{ assignment.asset?.serial_number || '—' }}</div>
                                    <div class="px-3 py-2">
                                        <Badge variant="outline" class="text-[11px]">Activo</Badge>
                                    </div>
                                    <div class="px-3 py-2 text-right">
                                        <Button variant="outline" size="sm">Solicitar devolución</Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-3 rounded-xl border border-dashed p-4 text-center text-xs text-muted-foreground">
                            El usuario no tiene activos asignados actualmente.
                        </div>

                        <div v-if="activeUserAssets.length" class="mt-3 flex items-center gap-2 text-xs text-amber-700 dark:text-amber-400">
                            <Badge variant="outline" class="text-[11px]">Advertencia</Badge>
                            El usuario ya tiene una asignación activa.
                        </div>
                    </div>

                    <!-- Assets assigned in this ticket -->
                    <div class="rounded-2xl border bg-card p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold">Activos asignados en este ticket</h3>
                            <Button size="sm" @click="openAssignModal = true">+ Asignar nuevo activo</Button>
                        </div>

                        <div v-if="ticketAssignedAssets.length" class="mt-3 overflow-hidden rounded-xl border">
                            <div class="grid grid-cols-[2fr_1fr_1fr_auto] bg-muted/50 text-xs font-semibold text-muted-foreground">
                                <div class="px-3 py-2">Activo</div>
                                <div class="px-3 py-2">Asignado por</div>
                                <div class="px-3 py-2">Fecha</div>
                                <div class="px-3 py-2 text-right">Acción</div>
                            </div>
                            <div class="divide-y">
                                <div v-for="item in ticketAssignedAssets" :key="item.key"
                                    class="grid grid-cols-[2fr_1fr_1fr_auto] items-center text-xs">
                                    <div class="px-3 py-2">
                                        <div class="font-medium">{{ item.name }}</div>
                                        <div class="text-[11px] text-muted-foreground">{{ item.type }}</div>
                                        <Badge v-if="item.isChild" variant="secondary" class="mt-1 text-[10px]">Accesorio</Badge>
                                    </div>
                                    <div class="px-3 py-2 text-muted-foreground">{{ item.assignedBy }}</div>
                                    <div class="px-3 py-2 text-muted-foreground">{{ item.assignedAt }}</div>
                                    <div class="px-3 py-2 text-right">
                                        <Button variant="outline" size="sm">Cambiar</Button>
                                        <Button variant="ghost" size="sm">Devolver</Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-3 rounded-xl border border-dashed p-4 text-center text-xs text-muted-foreground">
                            Aún no hay activos asignados en este ticket.
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="rounded-2xl border bg-card p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold">Actividad del ticket</h3>
                            <Badge variant="outline" class="text-xs">{{ ticket?.histories?.length || 0 }} eventos</Badge>
                        </div>

                        <div v-if="ticket?.histories?.length" class="mt-4 space-y-4">
                            <div v-for="history in ticket?.histories" :key="history.id" class="flex gap-3">
                                <div class="size-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center">
                                    <MonitorSmartphone class="size-4" />
                                </div>
                                <div class="flex-1">
                                    <div class="text-xs font-semibold">{{ history.action }}</div>
                                    <div class="text-xs text-muted-foreground">{{ history.description }}</div>
                                    <div class="text-[11px] text-muted-foreground">{{ history.performed_at }}</div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-3 rounded-xl border border-dashed p-4 text-center text-xs text-muted-foreground">
                            Sin actividad registrada.
                        </div>
                    </div>
                </div>

                <!-- Right column: assignment modal preview -->
                <div class="space-y-4">
                    <div class="rounded-2xl border bg-card p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold">Asignación de activo</h3>
                                <p class="text-xs text-muted-foreground">Editable dentro de 15 minutos</p>
                            </div>
                            <Badge v-if="canEdit && currentAssetAssignment" variant="secondary" class="text-xs">Editable</Badge>
                            <Badge v-else-if="currentAssetAssignment" variant="outline" class="text-xs">Bloqueado</Badge>
                        </div>
                        <div class="mt-3 text-xs text-muted-foreground">
                            Usa el botón “Asignar nuevo activo” para abrir el formulario.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment modal -->
            <Dialog v-model:open="openAssignModal">
                <DialogContent class="sm:max-w-3xl space-y-4">
                    <DialogHeader class="space-y-1 pb-2 border-b">
                        <DialogTitle class="text-lg">Asignar nuevo activo</DialogTitle>
                        <p class="text-xs text-muted-foreground">Selecciona el activo y los accesorios.</p>
                    </DialogHeader>

                    <Countdown title="Tiempo para editar la asignación (15 minutos)" @timeout="() => {
                        toast.error('El tiempo para editar esta asignación ha expirado.');
                        canEdit = false;
                    }" v-if="canEdit && currentAssetAssignment" :target-date="currentAssetAssignment?.created_at"
                        target-label="Tiempo restante para poder editar la información" :duration="15" />

                    <Alert v-else-if="currentAssetAssignment">
                        <LockKeyhole class="size-4" />
                        <AlertTitle>No es posible editar esta asignación</AlertTitle>
                        <AlertDescription>
                            Han pasado más de 15 minutos desde que se asignó este equipo.
                        </AlertDescription>
                    </Alert>

                    <form @submit.prevent="handleSubmit(onSubmit)()" id="dialogForm" class="space-y-4">
                        <FieldGroup>
                            <VeeField name="asset_id" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="asset_id">Activo</FieldLabel>
                                    <SelectFilters data-key="availableAssets" :items="availableAssets"
                                        :show-selected-focus="false" :show-refresh="false"
                                        :disabled="!!currentAssetAssignment?.parent_assignment_id || !canEdit"
                                        :label="currentAssetAssignment?.asset?.name || 'Seleccionar equipo'"
                                        item-label="name" item-value="id" selected-as-label
                                        :default-value="componentField.modelValue"
                                        @select="(value) => setFieldValue('asset_id', +value)"
                                        filter-placeholder="Buscar activo..." empty-text="No se encontraron equipos">
                                        <template #item="{ item: asset }">
                                            <div class="flex items-center gap-3 py-1">
                                                <component :is="assetTypeOp(asset.type?.name)?.icon"
                                                    class="size-4 shrink-0 text-muted-foreground" />
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold">{{ asset.full_name }}</span>
                                                        <Badge
                                                            class="bg-green-500/20 text-green-700 dark:text-green-400 border-green-300 dark:border-green-800 text-xs">
                                                            Disponible
                                                        </Badge>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </SelectFilters>

                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <FieldGroup>
                            <VeeField name="assign_date" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="assign_date">Fecha de Entrega</FieldLabel>
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>
                                            <Button :disabled="currentAssetAssignment?.parent_assignment_id || !canEdit"
                                                variant="outline" class="w-48 justify-between font-normal">
                                                {{ componentField.modelValue
                                                    ? format(componentField.modelValue.toDate(getLocalTimeZone()),
                                                        'dd/MM/yyyy')
                                                    : 'Seleccionar fecha' }}

                                                <ChevronDownIcon />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                                            <Calendar v-bind="componentField" locale="es" layout="month-and-year"
                                                selection-mode="single" @update:model-value="(value) => {
                                                    if (value) {
                                                        componentField.onChange(value)
                                                        close()
                                                    }
                                                }" />
                                        </PopoverContent>
                                    </Popover>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <FieldGroup v-if="selectedAsset && selectedAsset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="accessories" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="accessories">Accesorios</FieldLabel>

                                    <SelectFilters :disabled="!canEdit" :items="assetAccessories" data-key="accessories"
                                        :show-selected-focus="false" :show-refresh="false"
                                        :label="selectLabels(componentField.modelValue)" item-label="name"
                                        item-value="id" :multiple="true" selected-as-label
                                        :default-value="componentField.modelValue"
                                        @select="(values) => setFieldValue('accessories', values)"
                                        filter-placeholder="Buscar accesorio..."
                                        empty-text="No se encontraron accesorios">
                                    </SelectFilters>

                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <FieldGroup v-if="ticket?.current_asset_assignment?.asset?.type?.name !== TypeName.ACCESSORY">
                            <VeeField name="comment" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel for="comment">Notas (ITIL)</FieldLabel>
                                    <Textarea :disabled="!canEdit" id="comment"
                                        placeholder="Condiciones del equipo, accesorios incluidos..." rows="3"
                                        v-bind="componentField" />
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </form>

                    <DialogFooter class="gap-2 pt-2 border-t">
                        <Button variant="outline" @click="openAssignModal = false" :disabled="isLoading">Cancelar</Button>
                        <Button :disabled="disableForm" type="submit" form="dialogForm" class="min-w-36">
                            <Spinner v-if="isLoading" />
                            {{ isLoading ? 'Asignando...' : 'Asignar activo' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import z from 'zod';

import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel
} from '@/components/ui/field';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import Countdown from '@/components/Countdown.vue';
import { Alert } from '@/components/ui/alert';

import { Textarea } from '@/components/ui/textarea';

import { useAsset } from '@/composables/useAsset';
import { type Asset } from '@/interfaces/asset.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/zod';
import { ChevronDownIcon, LockKeyhole, MonitorSmartphone } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { useApp } from '@/composables/useApp';
import type { Alert as IAlert } from '@/interfaces/alert.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { TicketStatus, type Ticket, priorityOp, statusOp } from '@/interfaces/ticket.interface';
import { addMinutes, format } from 'date-fns';
import { toast } from 'vue-sonner';
import SelectFilters from '../SelectFilters.vue';

import { AlertTitle, AlertDescription } from '@/components/ui/alert';
import { isEqual } from '@/lib/utils';

const ticket = defineModel<Ticket | null>('ticket');
const open = defineModel<boolean>('open');

const page = usePage();
const { availableAssets: availables, assetAccessories: accessories, isLoading } = useApp();
const { downloadAssignmentDocument } = useAsset();

const openAssignModal = ref(false);

const requesterAssignments = computed<AssetAssignment[]>(() => {
    return (page.props as unknown as { requesterAssignments?: AssetAssignment[] })?.requesterAssignments || [];
});

const currentAssetAssignment = computed<AssetAssignment | null>(() => {
    return page.props?.currentAssignment as AssetAssignment | null;
});

const currentValues = computed<Record<string, any>>(() => {
    if (currentAssetAssignment.value) {
        return {
            asset_id: currentAssetAssignment.value.asset_id,
            assign_date: parseDate(currentAssetAssignment.value.assigned_at.split('T')[0]),
            comment: currentAssetAssignment.value.comment || '',
            accessories: currentAssetAssignment.value.children_assignments?.map(ca => ca.asset_id) || [],
        };
    }
    return {};
});

const activeUserAssets = computed<AssetAssignment[]>(() => {
    return requesterAssignments.value.filter(assignment => !assignment.returned_at);
});

const ticketAssignedAssets = computed(() => {
    const assignment = ticket.value?.current_asset_assignment;
    if (!assignment) return [] as Array<{ key: string; name: string; type: string; assignedBy: string; assignedAt: string; isChild: boolean }>; 
    const assignedBy = ticket.value?.responsible?.full_name || '—';
    const assignedAt = assignment.assigned_at || '—';

    const main = {
        key: `main-${assignment.id}`,
        name: assignment.asset?.full_name || assignment.asset?.name || 'Equipo',
        type: assignment.asset?.type?.name || 'Tipo no especificado',
        assignedBy,
        assignedAt,
        isChild: false
    };

    const children = (assignment.children_assignments || []).map(child => ({
        key: `child-${child.id}`,
        name: child.asset?.name || 'Accesorio',
        type: child.asset?.type?.name || 'Accesorio',
        assignedBy,
        assignedAt,
        isChild: true
    }));

    return [main, ...children];
});

const disableForm = computed<boolean>(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || !canEdit.value || isEqual(values, currentValues.value);
});


const canEdit = computed({
    get: () => {
        if (!currentAssetAssignment.value) return true;
        const targetDate = currentAssetAssignment.value.created_at;
        return new Date() <= addMinutes(targetDate, 15)
    },
    set: (val: boolean) => {
        return val;
    }
});


const availableAssets = computed<Asset[]>(() => {
    if (currentAssetAssignment.value) {
        return [
            currentAssetAssignment.value.asset!,
            ...availables.value,
        ];
    }
    return availables.value;
});

const assetAccessories = computed<Asset[]>(() => {
    if (!accessories.value.length) {
        return [...childrenAssets.value];
    }
    return [...childrenAssets.value, ...accessories.value];
});

const childrenAssets = computed<Asset[]>(() => {
    if (!currentAssetAssignment.value) return [];
    return currentAssetAssignment.value?.children_assignments?.flatMap(ca => ca.asset).filter((asset): asset is Asset => !!asset) || [];
});

const accesoriesOutOfStockAlertsExists = computed<boolean>(() => {
    const alerts = (page.props?.accessoriesOutOfStockAlert || []) as IAlert[];
    return alerts.length > 0;
});

const selectedAsset = computed<Asset | null>(() => {
    if (!values.asset_id) return null;
    return availableAssets.value.find(a => a.id === values.asset_id) || null;
});

const formSchema = toTypedSchema(
    z.object({
        asset_id: z.number({
            message: 'Seleccione un equipo',
        }),
        assign_date: z.instanceof(CalendarDate, {
            message: 'Seleccione una fecha de entrega',
        }).transform((date: CalendarDate) => date.toDate(getLocalTimeZone())),
        comment: z.string().optional(),
        accessories: z.array(z.number()).optional(),
    })
);

const { handleSubmit, errors, setValues, setFieldValue, values, resetForm } = useForm({
    initialValues: {
        asset_id: undefined,
        assign_date: today(getLocalTimeZone()),
        comment: '',
        accessories: [],
    },
    validationSchema: formSchema,
    validateOnMount: false,
});

watch(currentAssetAssignment, (assignment) => {
    if (assignment) {
        setValues(currentValues.value);
    } else {
        resetForm();
    }
}, { immediate: true });

const onSubmit = async (values: Record<string, any>) => {
    const accessoriesIds = values.accessories || [];
    const type = selectedAsset.value?.type?.name || null;

    const accessories = assetAccessories.value.filter(acc => accessoriesIds.includes(acc.id)) as Asset[];

    const only: string[] = [];
    if ([TypeName.LAPTOP, TypeName.CELL_PHONE].includes(type as TypeName)) {
        const includeCharger = accessories.some(acc => acc.name.toLowerCase().trim().includes('cargador'));
        if (!includeCharger) {
            toast.error('Debe incluir el cargador en los accesorios del equipo.');
            return;
        }
    }

    router.post('/assets/assign', {
        asset_id: values.asset_id,
        ticket_id: ticket.value?.id,
        responsible_id: ticket.value?.responsible_id,
        assigned_to_id: ticket.value?.requester_id,
        assign_date: values.assign_date.toDateString(),
        comment: type === TypeName.ACCESSORY ? null : values.comment,
        accessories: values.accessories,
    }, {
        only: ['availableAssets', 'accessories'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,

        onFlash: (flash) => {
            if (flash.success) {
                if (flash.assignment_id) {
                    const type = currentAssetAssignment.value?.asset?.type?.name || null;
                    if (type) {
                        downloadAssignmentDocument(flash.assignment_id as number, type);
                    }
                }

                open.value = false;
                ticket.value = null;
                if (!only.length) {
                    router.replaceProp('availableAssets', (availables: Asset[]) => {
                        return availables.filter(a => a.id !== values.asset_id);
                    });
                }
            }

            if (flash.alert_triggered && !accesoriesOutOfStockAlertsExists.value) {
                toast.success('Se ha enviado una alerta de stock bajo para los accesorios seleccionados.');
            }
        },
    });
};


const selectLabels = (modelValue: Array<number>) => {
    if (!modelValue?.length) return 'Seleccionar accesorio';

    let accesories = assetAccessories.value;
    if (!accesories.length) {
        accesories = childrenAssets.value;
    }

    if (accesories.length > 3 && modelValue.length > 3) {
        return `${modelValue.length} accesorios seleccionados`;
    }

    return accesories
        .filter(acc => modelValue.includes(acc.id))
        .map(acc => acc.name)
        .join(', ');
}

const statusLabel = computed(() => statusOp(ticket.value?.status)?.label || '—');
const priorityLabel = computed(() => priorityOp(ticket.value?.priority)?.label || '—');

const statusBadgeClass = computed(() => statusOp(ticket.value?.status)?.bg || 'bg-gray-500');
const priorityBadgeClass = computed(() => priorityOp(ticket.value?.priority)?.bg || 'bg-gray-500');

const slaLabel = computed(() => '—');

const disableActions = computed(() => {
    return isLoading.value || !ticket.value?.id;
});

const handleStatusAction = (status: TicketStatus | 'RESOLVED' | 'IN_PROGRESS' | 'CLOSED') => {
    if (!ticket.value?.id) return;

    router.post(`/tickets/${ticket.value.id}/change-status`, {
        status,
    }, {
        only: ['tickets'],
        preserveState: true,
        preserveScroll: true,
        onFlash: (flash) => {
            if (flash.error) {
                toast.error(flash.error as string);
            } else if (flash.success) {
                toast.success(flash.success as string);
            }
        }
    });
};
</script>
