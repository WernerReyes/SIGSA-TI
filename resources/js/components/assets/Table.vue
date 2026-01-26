<template>

    <div class="space-y-4">
        <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
            <div class="flex max-md:flex-col gap-4 items-start p-4">
                <div class="flex flex-col gap-2 w-full max-w-xl">

                    <div class="flex items-center gap-2">
                        <!-- <Input class="w-full" placeholder="Buscar activos..." v-model="form.search" /> -->
                        <InputGroup>
                            <InputGroupInput class="w-full" placeholder="Buscar activos..." v-model="form.search" />
                            <InputGroupAddon>
                                <Search />
                            </InputGroupAddon>
                        </InputGroup>

                        <Button variant="ghost" size="icon" :disabled="isLoading || !hasFilters" class="rounded-full"
                            @click="resetFilters">
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />
                        </Button>
                    </div>
                    <div v-if="hasFilters"
                        class="flex flex-wrap gap-2 text-xs text-muted-foreground items-center animate-in fade-in-50">
                        <Badge variant="outline" class="rounded-full">{{ filterCount }} {{
                            filterCount === 1 ? 'filtro aplicado' : 'filtros aplicados'
                            }}</Badge>

                        <template v-for="filter in filterstersRenders" :key="filter.label">
                            <Badge v-if="filter.value" variant="secondary" class="cursor-pointer" :class="{
                                'disabled': isLoading
                            }" @click="filter.click">
                                <Badge v-if="typeof filter.value === 'number' && filter.label !== 'Fechas'"
                                    class="h-4 min-w-4 rounded-full px-1 font-mono tabular-nums">
                                    {{ filter.value }}
                                </Badge>
                                {{ filter.label }}
                                <XCircle class="size-4 ml-1" />
                            </Badge>
                        </template>
                    </div>
                </div>

                <!-- max-sm:w-full ml-auto md:max-w-2/3 flex gap-2 flex-wrap justify-end items-end -->

                <div class="max-sm:w-full ml-auto flex gap-2 flex-wrap justify-end items-end">


                    <SelectFilters label="Asignados" :items="users" data-key="users" :icon="Users" :allow-null="true"
                        show-refresh show-selected-focus item-value="staff_id" item-label="full_name" :multiple="true"
                        :default-value="form.assigned_to" @select="(selects) => form.assigned_to = selects" />


                    <SelectFilters label="Departamentos" :items="departments" data-key="departments" :icon="Building"
                        show-refresh show-selected-focus item-value="id" item-label="name" :multiple="true"
                        :default-value="form.department_id" @select="(selects) => form.department_id = selects" />

                    <!-- {{ Compu }} -->

                    <SelectFilters label="Tipos" :items="assetTypes" data-key="types" :icon="MonitorSmartphone"
                        show-refresh show-selected-focus item-value="id" item-label="name" :multiple="true"
                        :default-value="form.types" @select="(selects) => form.types = selects">

                        <template #item="{ item }">
                            <div class="flex items-center gap-2">
                                <component :is="assetTypeOp(item.name)?.icon" class="size-4" />
                                {{ item.name }}
                            </div>
                        </template>

                    </SelectFilters>


                    <SelectFilters label="Estados" :items="Object.values(assetStatusOptions)" data-key="status"
                        :icon="ChartArea" show-refresh show-selected-focus item-value="value" item-label="label"
                        :multiple="true" :default-value="form.status" @select="(selects) => form.status = selects">

                        <template #item="{ item }">
                            <Badge :class="item.bg">
                                <component :is="item.icon" class="size-4" />
                                {{ item.label }}
                            </Badge>
                        </template>
                    </SelectFilters>

                    <Popover>
                        <PopoverTrigger as-child>
                            <Button id="date" variant="outline" class="w-48 justify-between font-normal">
                                <CalendarSearch />

                                {{ formattedDate }}
                                <ChevronDownIcon />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                            <RangeCalendar locale="es" v-model="form.dateRange as any" layout="month-and-year" />
                        </PopoverContent>
                    </Popover>


                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline">
                                <Columns4 class="size-4" />
                                Columnas
                                <ChevronDown class="ml-2 size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">

                            <DropdownMenuCheckboxItem :disabled="isLoading"
                                v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                                :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
                                @update:model-value="column.toggleVisibility()">
                                {{ column.columnDef.header }}
                            </DropdownMenuCheckboxItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>


        <div class="rounded-xl border bg-card/70 shadow-sm backdrop-blur overflow-hidden">

            <div class="overflow-x-auto">
                <Table class="min-w-full">
                    <TableHeader class="bg-muted/70 backdrop-blur sticky top-0 z-10">
                        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <TableHead class="pl-5 uppercase text-[11px] tracking-wide text-muted-foreground"
                                v-for="header in headerGroup.headers" :key="header.id" :style="{
                                    width: header.getSize() + 'px'
                                }">
                                <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                    :props="header.getContext()" />
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <template v-if="table.getRowModel().rows?.length">

                        <ContextMenu>
                            <ContextMenuTrigger as-child>
                                <TableBody>
                                    <TableRow @contextmenu="() => {
                                        activeRow = row.original
                                    }" :key="row.id" v-for="row in table.getRowModel().rows"
                                        :data-state="row.getIsSelected() ? 'selected' : undefined"
                                        class="cursor-context-menu transition hover:bg-muted/60 odd:bg-muted/30">
                                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                                            class="pl-5 align-middle" :style="{ width: cell.column.getSize() + 'px' }">
                                            <FlexRender :render="cell.column.columnDef.cell"
                                                :props="cell.getContext()" />
                                        </TableCell>
                                    </TableRow>
                                </TableBody>

                            </ContextMenuTrigger>

                            <ContextMenuContent class="w-52">

                                <ContextMenuItem @click="handleOpenDetails()">
                                    <Eye />
                                    Ver detalle
                                </ContextMenuItem>
                                <ContextMenuItem @click="openEdit = true">
                                    <Pencil />
                                    Editar
                                </ContextMenuItem>
                                <ContextMenuItem @click="changeStatus = true">
                                    <Pencil />
                                    Cambiar estado
                                </ContextMenuItem>

                                <ContextMenuItem @click="openInvoice = true">
                                    <UploadCloud />
                                    Cargar factura
                                </ContextMenuItem>


                                <ContextMenuItem
                                    :disabled="[AssetStatus.DECOMMISSIONED, AssetStatus.IN_REPAIR].includes(activeRow!.status)"
                                    @click="openAssign = true">
                                    <MonitorSmartphone />
                                    Asignar
                                </ContextMenuItem>
                                <ContextMenuItem :disabled="!activeRow?.current_assignment"
                                    @click="openDevolution = true">
                                    <MonitorSmartphone />
                                    Devolver
                                </ContextMenuItem>
                                <ContextMenuItem @click="handleOpenHistories()">
                                    <History />
                                    Ver historial
                                </ContextMenuItem>
                                <ContextMenuItem @click="openDelete = true"
                                    :disabled="AssetStatus.AVAILABLE !== activeRow?.status">
                                    <X />
                                    Eliminar
                                </ContextMenuItem>
                            </ContextMenuContent>
                        </ContextMenu>
                    </template>

                    <template v-else>
                        <TableBody>
                            <TableRow>
                                <TableCell :colspan="columns.length" class="text-center">

                                    <Empty class="p-6">
                                        <EmptyHeader>
                                            <EmptyMedia variant="icon">
                                                <MonitorSmartphone />
                                            </EmptyMedia>
                                            <EmptyTitle>
                                                Sin resultados
                                            </EmptyTitle>
                                            <EmptyDescription>
                                                No se encontraron equipos que coincidan con los filtros aplicados.
                                            </EmptyDescription>
                                        </EmptyHeader>


                                    </Empty>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </template>
                </Table>
            </div>

            <div class="flex space-x-2 p-4">
                <Pagination class="mx-0  w-fit ml-auto!" :items-per-page="assets.per_page" :total="assets.total"
                    :default-page="assets.current_page">
                    <PaginationContent class="flex-wrap">
                        <PaginationPrevious :disabled="isLoading || assets.current_page === 1" @click="!isLoading && router.visit(assets.prev_page_url || '', {
                            preserveScroll: true,
                            replace: true,
                        })">

                            <ChevronLeftIcon />
                            Anterior
                        </PaginationPrevious>
                        <template v-for="(item, index) in assets.links.filter(link => +link.label)" :key="index">
                            <PaginationItem :value="+item.label" :is-active="item.active"
                                :disabled="isLoading || item.active" @click="!isLoading && router.visit(item.url, {
                                    preserveScroll: true,
                                    replace: true,
                                })">
                                {{ item.label }}
                            </PaginationItem>
                        </template>

                        <PaginationNext :disabled="isLoading || assets.current_page === assets.last_page" @click="!isLoading && router.visit(assets.next_page_url || '', {
                            preserveScroll: true,
                        })">
                            Siguiente
                            <ChevronRightIcon />
                        </PaginationNext>

                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>

    <DialogDetails v-if="openDetails" v-model:open="openDetails" v-model:asset="activeRow" />
    <Dialog v-model:open-editor="openEdit" v-model:current-asset="activeRow" />
    <InvoiceDialog v-if="openInvoice" v-model:open="openInvoice" :asset="activeRow" />
    <StatusDialog v-if="changeStatus" v-model:open="changeStatus" :asset="activeRow" />
    <AssignDialog v-if="openAssign" v-model:open="openAssign" v-model:asset="activeRow" />
    <DevolutionDialog v-if="openDevolution" v-model:open="openDevolution" v-model:asset="activeRow" />
    <HistoryDialog v-if="openHistory" v-model:open="openHistory" v-model:asset="activeRow" />

    <AlertDialog v-model:open="openDelete" title="¿Estás seguro de que deseas eliminar este equipo?"
        description="Esta acción no se puede deshacer. El equipo será eliminado permanentemente del sistema, incluyendo todos sus registros asociados ( asignaciones, historial de cambios de estado, etc. )."
        actionText="Eliminar" @confirm="handleDeleteAsset" />

</template>


<script setup lang="ts">

import type { ColumnDef, ExpandedState, SortingState } from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    ContextMenu,
    ContextMenuContent,
    ContextMenuItem,
    ContextMenuTrigger
} from '@/components/ui/context-menu';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious
} from '@/components/ui/pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { valueUpdater } from '@/lib/utils';


import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { type Asset, AssetStatus, assetStatusOptions, statusOp } from '@/interfaces/asset.interface';
import { assetTypeOp, TypeName } from '@/interfaces/assetType.interface';

import { router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { format, isAfter, isSameDay, startOfDay } from 'date-fns';
import { Building, ChartArea, Check, ChevronDown, ChevronLeftIcon, ChevronRight, ChevronRightIcon, Columns4, Eye, History, MonitorSmartphone, Pencil, RefreshCcw, UploadCloud, UserIcon, Users, X, XCircle, CalendarSearch, Search } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

import { useApp } from '@/composables/useApp';
import type { Paginated } from '@/types';
import SelectFilters from '../SelectFilters.vue';
import AssignDialog from './AssignDialog.vue';
import DevolutionDialog from './DevolutionDialog.vue';
import Dialog from './Dialog.vue';
import DialogDetails from './DialogDetails.vue';
import HistoryDialog from './HistoryDialog.vue';
import InvoiceDialog from './InvoiceDialog.vue';
import StatusDialog from './StatusDialog.vue';
import AlertDialog from '../AlertDialog.vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { CalendarDate, getLocalTimeZone, parseDate } from '@internationalized/date';
import { type DateRange } from 'reka-ui';
import { InputGroup, InputGroupInput, InputGroupAddon } from '@/components/ui/input-group';


const { assets } = defineProps<{ assets: Paginated<Asset> }>()

const page = usePage();
const { isLoading, users, departments, assetTypes, assetAccessories } = useApp();


const activeRow = ref<Asset | null>(null)

const openDetails = ref(false);
const openEdit = ref(false);
const changeStatus = ref(false);
const openAssign = ref(false);
const openDevolution = ref(false);
const openHistory = ref(false);
const openInvoice = ref(false);
const openDelete = ref(false);

const sorting = ref<SortingState>([])



const filters = computed(() => page.props.filters as Record<string, any>);

const assetId = computed(() => activeRow.value?.id || null);

const form = reactive<{
    search: string;
    status: AssetStatus[];
    types: number[];
    assigned_to: (number | null)[];
    department_id: number[];
    dateRange?: DateRange | null;
}>({
    search: filters.value.search || '',
    status: filters.value.status || [],
    types: filters.value.types?.map((id: string) => +id) || [],
    assigned_to: filters.value.assigned_to?.map((id: string | null) => id ? +id : null) || [],
    department_id: filters.value.department_id || [],
    dateRange: filters.value?.startDate || filters.value?.endDate ? {
        start: filters.value?.startDate ? parseDate(filters.value.startDate) : undefined,
        end: filters.value?.endDate ? parseDate(filters.value.endDate) : undefined
    } : undefined
})

const filterCount = computed(() => {
    const base = form.search ? 1 : 0;
    const buckets = [form.status.length, form.types.length, form.assigned_to.length, form.department_id.length, form.dateRange ? 1 : 0];
    return base + buckets.filter(Boolean).length;
});

const hasFilters = computed(() => filterCount.value > 0);

const formattedDate = computed(() => {
    if (!form.dateRange || (!form.dateRange?.start && !form.dateRange?.end)) {
        return 'Fecha creado';
    }
    const start = form.dateRange.start?.toDate(getLocalTimeZone()).toLocaleDateString();
    if (!form.dateRange.end) {
        return start;
    }
    const end = form.dateRange.end?.toDate(getLocalTimeZone()).toLocaleDateString();
    return `${start} - ${end}`;
});


const filterstersRenders = computed(() => [{
    label: 'Búsqueda',
    value: form.search,
    click: (): void => { form.search = '' }

}, {
    label: 'Estados',
    value: form.status.length,
    click: (): void => { form.status = [] }

}, {
    label: 'Tipos',
    value: form.types.length,
    click: (): void => { form.types = [] }

}, {
    label: 'Asignados',
    value: form.assigned_to.length,
    click: (): void => { form.assigned_to = [] }

}, {
    label: 'Departamentos',
    value: form.department_id.length,
    click: (): void => { form.department_id = [] }
}, {
    label: 'Fechas',
    value: form.dateRange ? 1 : 0,
    click: (): void => { form.dateRange = undefined }


}]
);

watch(
    () => form.search,
    useDebounceFn(() => {
        applyFilters()
    }, 400)
)

watch(
    () => form.status,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.types,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.assigned_to,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.department_id,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.dateRange,
    () => {
        applyFilters()
    },
    { deep: true }
)


const handleOpenDetails = () => {
    router.reload({
        only: ['details'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: (page) => {
            activeRow.value = {
                ...activeRow.value!,
                ...page.props.details as Asset,
            }
            openDetails.value = true;
        }
    });

}

const handleOpenHistories = () => {
    router.reload({
        only: ['historiesPaginated'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: (page) => {
            // console.log(page.props.historiesPaginated)
            // activeRow.value = {
            //     ...activeRow.value!,
            //     histories: page.props.historiesPaginated as Asset['histories'],
            // }
            openHistory.value = true;
        }
    });
}


const applyFilters = () => {
    const startDate = form.dateRange?.start ? format(form.dateRange.start.toDate(getLocalTimeZone()), 'yyyy-MM-dd') : null;
    const endDate = form.dateRange?.end ? format(form.dateRange.end.toDate(getLocalTimeZone()), 'yyyy-MM-dd') : null;
    router.get(
        assets.path,
        {
            search: form.search || undefined,
            status: form.status.length ? form.status : undefined,
            types: form.types.length ? form.types : undefined,
            assigned_to: form.assigned_to.length ? form.assigned_to : undefined,
            department_id: form.department_id.length ? form.department_id : undefined,
            startDate: startDate || undefined,
            endDate: endDate || undefined,
        },
        {
            only: ['assetsPaginated', 'filters'],
            preserveState: true,
            replace: true,
        }
    )
}

const resetFilters = () => {
    form.search = '';
    form.status = [];
    form.types = [];
    form.assigned_to = [];
    form.department_id = [];
    applyFilters();
};

const handleDeleteAsset = () => {

    if (!assetId.value) return;

    const only = ['assetsPaginated', 'stats'];
    if (assetAccessories.value.some(acc => acc.id === assetId.value)) {
        only.push('accessories');
    }


    router.delete(`
                     /assets/${assetId.value}
                    `, {
        only,
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onSuccess: () => {
            activeRow.value = null;
            openDelete.value = false;
        }
    });
}

const columns: ColumnDef<Asset>[] = [
    {

        accessorKey: 'name',
        id: 'name',
        header: 'Nombre',
        minSize: 180,
        enableGlobalFilter: true,

        cell: ({ row }) => {
            return row.getCanExpand() ? (
                h('div', { class: 'flex items-center gap-2' }, [
                    h(
                        Button,
                        {
                            variant: 'ghost',
                            size: 'icon',
                            onClick: row.getToggleExpandedHandler()
                        },
                        () =>
                            row.getIsExpanded()
                                ? h(ChevronDown)
                                : h(ChevronRight)
                    ),
                    row.original.name || h(Badge, { variant: 'secondary' }, () => 'N/A')
                ])
            ) : (
                row.original.name || h(Badge, { variant: 'secondary' }, () => 'N/A')
            );
        }


    },
    {
        accessorKey: 'brand',
        id: 'brand',
        header: 'Marca',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {
        accessorKey: 'model',
        id: 'model',
        header: 'Modelo',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {
        accessorKey: 'serial_number',
        id: 'serial_number',
        header: 'Serial',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {

        accessorFn: row => row?.current_assignment?.assigned_to ? row.current_assignment.assigned_to.full_name : 'Sin asignar',
        id: 'assigned_to',
        header: 'Asignado a',
        cell: info => {
            const assignedTo = info.row.original.current_assignment?.assigned_to;
            if (assignedTo) {
                return h(Badge, { variant: 'default', class: 'flex items-center gap-2' }, () => [
                    h(UserIcon, { class: '!size-5' }),
                    h('div', { class: 'flex flex-col' }, [
                        assignedTo.full_name,
                        h('small', { class: 'text-[10px] text-gray-400 dark:text-gray-700 italic' }, assignedTo.department ? assignedTo.department.name : 'Sin departamento')
                    ])
                ]);
            } else {
                return h(Badge, { variant: 'secondary' }, () => [
                    h(X),
                    'Sin asignar'
                ]);
            }
        },
    },
    {
        accessorKey: 'type.name',
        id: 'type',
        header: 'Tipo',
        cell: info => h(
            'div',
            { class: 'flex items-center gap-2' },
            [
                h(
                    assetTypeOp(info.getValue() as TypeName)?.icon || 'span',
                    { class: 'size-4' }
                ),
                info.getValue() as string
            ]
        )
    },
    {
        accessorFn: row => row.is_new ? 'si' : 'no',
        id: 'is_new',
        header: 'Nuevo',
        cell: info => {
            const isNew = info.row.original.is_new;
            return isNew ? h(Badge, { class: 'bg-green-500' }, () => 'Sí') : h(Badge, { class: 'bg-gray-500' }, () => 'No');
        }
    },


    {
        accessorFn: row => statusOp(row.status)?.label.toLowerCase() ?? '',
        id: 'status',
        header: 'Estado',
        cell: info => {
            const op = statusOp(info.row.original.status as AssetStatus);
            return h(
                Badge,
                {
                    class: op?.bg
                },
                () => [op?.icon ? h(op.icon) : null, op?.label]
            );
        }
    }, {
        accessorKey: 'purchase_date',
        id: 'purchase_date',
        header: 'Fecha de compra',
        cell: info => format(info.getValue() as string, 'dd/MM/yyyy'),
    }, {
        accessorKey: 'warranty_expiration',
        id: 'warranty_expiration',
        header: 'Garantía hasta',
        cell: info => {
            const warrantyDate = info.getValue() as string;
            const isValid = isWarrantyValid(warrantyDate);
            return h(
                Badge,
                {
                    class: isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                },
                () => [
                    h(isValid ? Check : X),
                    format(warrantyDate, 'dd/MM/yyyy')
                ]
            );

        }
    }, {
        accessorKey: 'created_at',
        id: 'created_at',
        header: 'Creado el',
        cell: info => format(info.getValue() as string, 'dd/MM/yyyy HH:mm:ss')

    }, {
        accessorKey: 'updated_at',
        id: 'updated_at',
        header: 'Actualizado el',
        cell: info => format(info.getValue() as string, 'dd/MM/yyyy HH:mm:ss'),
    }


]

const isWarrantyValid = (warrantyEndDate: string): boolean => {
    const endDate = startOfDay(new Date(warrantyEndDate));
    const today = startOfDay(new Date());
    return isAfter(endDate, today) || isSameDay(endDate, today);
};

const expanded = ref<ExpandedState>({})

const table = useVueTable({
    get data() { return assets.data },
    get columns() { return columns },
    getSubRows: row => row.current_assignment?.children_assignments?.map(child => child.asset!) || [],
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    globalFilterFn: 'includesString',
    paginateExpandedRows: false,

    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() { return sorting.value },
        get expanded() { return expanded.value },
    },
})

</script>
