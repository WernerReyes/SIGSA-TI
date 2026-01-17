<template>

    <div class="flex max-md:flex-col gap-4 items-center p-4 ">
        <Input class="max-w-sm" placeholder="Buscar activos..." v-model="form.search" />

        <div class="max-sm:w-full ml-auto flex gap-2 flex-wrap">
            <Popover v-model:open="openUserSelect">
                <PopoverTrigger as-child>
                    <Button variant="outline" role="combobox" :aria-expanded="openUserSelect"
                        class="w-fit justify-between ">

                        <span class="relative flex size-2" v-if="form.assigned_to.length > 0">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                        </span>

                        Empleados
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-full p-0">
                    <Command>
                        <!-- <div class="flex"> -->

                        <CommandInput placeholder="Buscar empleado..." class="w-full" />

                        <CommandShortcut :disable="isLoading || !form.assigned_to?.length" @click="() => {
                            if (!form.assigned_to?.length) return;
                            form.assigned_to = []

                        }" class="cursor-pointer  w-full justify-center gap-2  flex items-center p-2">
                            Refrescar lista
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />

                        </CommandShortcut>
                        <!-- </div> -->
                        <CommandList>
                            <WhenVisible data="users">
                                <template #fallback>
                                    <CommandGroup>
                                        <CommandItem v-for="n in 5" :key="n" value="loading">
                                            <Skeleton class="h-4 w-full" />
                                        </CommandItem>
                                    </CommandGroup>
                                </template>

                                <CommandEmpty>Empleado no encontrado</CommandEmpty>

                                <CommandGroup>
                                    <CommandItem v-for="user in users" :key="user.staff_id || 'user-none'"
                                        :value="user.staff_id" @select="() => {
                                            if (form.assigned_to.includes(user.staff_id)) {
                                                form.assigned_to = form.assigned_to.filter(id => id !== user.staff_id)
                                            } else {
                                                form.assigned_to = [...form.assigned_to, user.staff_id]
                                            }
                                        }">
                                        {{ user.full_name }}
                                        <Check v-if="form.assigned_to.includes(user.staff_id)" class="ml-auto size-4" />
                                    </CommandItem>
                                </CommandGroup>
                            </WhenVisible>
                        </CommandList>

                    </Command>
                </PopoverContent>
            </Popover>

            <Popover v-model:open="openDepartmentSelect">
                <PopoverTrigger as-child>
                    <Button variant="outline" role="combobox" :aria-expanded="openDepartmentSelect"
                        class="w-fit justify-between">

                        <span class="relative flex size-2" v-if="form.department_id.length > 0">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                        </span>

                        Departamentos
                        <ChevronsUpDown class="ml-2 size shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-full p-0">
                    <Command>
                        <!-- <div class="flex"> -->

                        <CommandInput placeholder="Buscar departamento..." class="w-full" />



                        <CommandShortcut :disable="isLoading || !form.department_id?.length" @click="() => {
                            if (isLoading || !form.department_id?.length) return;
                            form.department_id = []
                            // selectDepartmentId = null;
                        }" class="cursor-pointer  w-full justify-center gap-2  flex items-center p-2">
                            Refrescar lista
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />

                        </CommandShortcut>
                        <!-- </div> -->
                        <CommandList>
                            <WhenVisible data="departments">

                                <template #fallback>
                                    <CommandGroup>
                                        <CommandItem v-for="n in 5" :key="n" value="loading">
                                            <Skeleton class="h-4 w-full" />
                                        </CommandItem>
                                    </CommandGroup>
                                </template>



                                <CommandEmpty>Departamento no encontrado</CommandEmpty>
                                <CommandGroup>

                                    <CommandItem v-for="department in departments" :key="department.id"
                                        :value="department.id" @select="() => {
                                            // selectDepartmentId = department.id;
                                            if (form.department_id.includes(department.id)) {
                                                form.department_id = [...form.department_id.filter(id => id !== department.id)];
                                            } else {
                                                form.department_id = [...form.department_id, department.id];
                                            }
                                        }">

                                        {{ department.name }}
                                        <Check v-if="form.department_id.includes(department.id)" class="ml-auto size" />
                                    </CommandItem>
                                </CommandGroup>


                            </WhenVisible>
                        </CommandList>

                    </Command>
                </PopoverContent>
            </Popover>

            <Popover v-model:open="openAssetTypeSelect">
                <PopoverTrigger as-child>
                    <Button variant="outline" role="combobox" :aria-expanded="openAssetTypeSelect"
                        class="w-fit justify-between">

                        <span class="relative flex size-2" v-if="form.types.length > 0">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                        </span>

                        Tipos
                        <ChevronsUpDown class="ml-2 size-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-full p-0">
                    <Command>

                        <CommandShortcut :disable="isLoading || !form.types?.length" @click="() => {
                            if (isLoading || !form.types?.length) return;
                            form.types = []

                        }" class="cursor-pointer  w-full justify-center gap-2  flex items-center p-2">
                            Refrescar lista
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />

                        </CommandShortcut>

                        <CommandList>

                            <WhenVisible data="types">
                                <template #fallback>
                                    <CommandGroup>
                                        <CommandItem v-for="n in 5" :key="n" value="loading">
                                            <Skeleton class="h-4 w-full" />
                                        </CommandItem>
                                    </CommandGroup>
                                </template>

                                <CommandEmpty>Tipo no encontrado</CommandEmpty>
                                <CommandGroup>
                                    <CommandItem v-for="assetType in types" :key="assetType.id" :value="assetType.id"
                                        @select="() => {

                                            if (form.types.includes(assetType.id)) {
                                                form.types = [...form.types.filter(id => id !== assetType.id)];
                                            } else {
                                                form.types = [...form.types, assetType.id];
                                            }
                                        }">
                                        <component :is="assetTypeOp(assetType.name)?.icon" class="size-4" />
                                        {{ assetType.name }}
                                        <Check v-if="form.types.includes(assetType.id)" class="ml-auto h-4 w-4" />
                                    </CommandItem>
                                </CommandGroup>
                            </WhenVisible>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>

            <Popover v-model:open="openStatusSelect">
                <PopoverTrigger as-child>
                    <Button variant="outline" role="combobox" :aria-expanded="openStatusSelect"
                        class="w-fit justify-between">

                        <span class="relative flex size-2" v-if="form.status.length > 0">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                        </span>

                        Estados
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-full p-0">
                    <Command>
                        <!-- <CommandInput placeholder="Buscar departamento..." class="w-full" /> -->
                        <CommandShortcut :disable="isLoading || !form.status?.length" @click="() => {
                            if (isLoading || !form.status?.length) return;
                            form.status = []
                            // selectDepartmentId = null;
                        }" class="cursor-pointer  w-full justify-center gap-2  flex items-center p-2">
                            Refrescar lista
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />

                        </CommandShortcut>
                        <!-- </div> -->
                        <CommandList>
                            <CommandEmpty>Estado no encontrado</CommandEmpty>
                            <CommandGroup>

                                <CommandItem v-for="status in Object.values(assetStatusOptions)" :key="status.value"
                                    :value="status.value" @select="() => {
                                        // selectAssetTypeId = assetType.id;
                                        if (form.status.includes(status.value)) {
                                            form.status = [...form.status.filter(id => id !== status.value)];
                                        } else {
                                            form.status = [...form.status, status.value];
                                        }
                                    }">
                                    <Badge :class="status.bg">
                                        <component :is="status.icon" class="size-4" />
                                        {{ status.label }}
                                    </Badge>
                                    <Check v-if="form.status.includes(status.value)" class="ml-auto h-4 w-4" />

                                </CommandItem>
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columnas
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">

                    <DropdownMenuCheckboxItem :disabled="isLoading"
                        v-for="column in table.getAllColumns().filter((column) => column.getCanHide())" :key="column.id"
                        class="capitalize" :model-value="column.getIsVisible()"
                        @update:model-value="column.toggleVisibility()">
                        {{ column.columnDef.header }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>


    <div class="border rounded-md">

        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead class="pl-5" v-for="header in headerGroup.headers" :key="header.id" :style="{
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
                                :data-state="row.getIsSelected() ? 'selected' : undefined" class="cursor-context-menu">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="pl-5"
                                    :style="{ width: cell.column.getSize() + 'px' }">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
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
                            :disabled="[AssetStatus.DECOMMISSIONED, AssetStatus.IN_REPAIR].includes(activeRow?.status)"
                            @click="openAssign = true">
                            <MonitorSmartphone />
                            Asignar
                        </ContextMenuItem>
                        <ContextMenuItem :disabled="!activeRow?.current_assignment" @click="openDevolution = true">
                            <MonitorSmartphone />
                            Devolver
                        </ContextMenuItem>
                        <ContextMenuItem @click="handleOpenHistories()">
                            <History />
                            Ver historial
                        </ContextMenuItem>
                    </ContextMenuContent>
                </ContextMenu>
            </template>

            <template v-else>
                <TableBody>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="text-center">

                            <Empty class="p-1!">
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

        <div class="flex space-x-2 p-4">
            <!-- {{assets}} -->
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

    <DialogDetails v-if="openDetails" v-model:open="openDetails" v-model:asset="activeRow" />
    <Dialog v-model:open-editor="openEdit" v-model:current-asset="activeRow" />
    <InvoiceDialog v-if="openInvoice" v-model:open="openInvoice" :asset="activeRow" />
    <StatusDialog v-if="changeStatus" v-model:open="changeStatus" :asset="activeRow" />
    <AssignDialog v-if="openAssign" v-model:open="openAssign" v-model:asset="activeRow" />
    <DevolutionDialog v-if="openDevolution" v-model:open="openDevolution" v-model:asset="activeRow" />
    <HistoryDialog v-if="openHistory" v-model:open="openHistory" v-model:asset="activeRow" />


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

import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandShortcut } from '@/components/ui/command';

import { valueUpdater } from '@/lib/utils';


import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { type Asset, AssetStatus, assetStatusOptions, statusOp } from '@/interfaces/asset.interface';
import { AssetType, assetTypeOp, TypeName } from '@/interfaces/assetType.interface';

import { Skeleton } from '@/components/ui/skeleton';
import type { Department } from '@/interfaces/department.interace';
import type { BasicUserInfo } from '@/interfaces/user.interface';
import { router, usePage, WhenVisible } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { format, isAfter, isEqual, parseISO } from 'date-fns';
import { Check, ChevronDown, ChevronLeftIcon, ChevronRight, ChevronRightIcon, ChevronsUpDown, Eye, History, MonitorSmartphone, Pencil, RefreshCcw, UploadCloud, UserIcon, X } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

import { useApp } from '@/composables/useApp';
import type { Paginated } from '@/types';
import AssignDialog from './AssignDialog.vue';
import DevolutionDialog from './DevolutionDialog.vue';
import Dialog from './Dialog.vue';
import DialogDetails from './DialogDetails.vue';
import HistoryDialog from './HistoryDialog.vue';
import InvoiceDialog from './InvoiceDialog.vue';
import StatusDialog from './StatusDialog.vue';


const { assets } = defineProps<{ assets: Paginated<Asset> }>()


const { isLoading } = useApp();


const activeRow = ref<Asset | null>(null)

const openDetails = ref(false);
const openEdit = ref(false);
const changeStatus = ref(false);
const openAssign = ref(false);
const openDevolution = ref(false);
const openHistory = ref(false);
const openInvoice = ref(false);

const sorting = ref<SortingState>([])


const openUserSelect = ref(false);
const openDepartmentSelect = ref(false);
const openAssetTypeSelect = ref(false);
const openStatusSelect = ref(false);


// const canAssignEdit = computed(() => {
//     // if (!activeRow.value) return false;
//     // if ([AssetStatus.DECOMMISSIONED, AssetStatus.IN_REPAIR].includes(activeRow.value.status)) return false;
//     if (!activeRow.value?.current_assignment) return true;
//     const targetDate = activeRow.value.current_assignment.created_at;
//     return new Date() <= addMinutes(targetDate, 15)
// },

// );


const users = computed<(Omit<BasicUserInfo, 'staff_id'> & {
    staff_id: number | null
})[]>(() => {
    const users = usePage().props.users as BasicUserInfo[];
    return [{
        staff_id: null,
        full_name: 'Sin asignar',
        firstname: '',
        lastname: ''
    }, ...users];
});

const departments = computed(() => {
    return (usePage().props?.departments || []) as Department[];
});

const types = computed(() => {
    return (usePage().props?.types || []) as AssetType[];
});

const filters = computed(() => usePage().props.filters as Record<string, any>);

const assetId = computed(() => activeRow.value?.id || null);

const form = reactive<{
    search: string;
    status: AssetStatus[];
    types: number[];
    assigned_to: (number | null)[];
    department_id: number[];
}>({
    search: filters.value.search || '',
    status: filters.value.status || [],
    types: filters.value.types?.map((id: string) => +id) || [],
    assigned_to: filters.value.assigned_to?.map((id: string | null) => id ? +id : null) || [],
    department_id: filters.value.department_id || [],
})


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


// const isLoading = ref(false)

// const start = () => (isLoading.value = true)
// const finish = () => (isLoading.value = false)

// let unsubscribeStart: (() => void) | null = null
// let unsubscribeFinish: (() => void) | null = null

// onMounted(() => {
//     unsubscribeStart = router.on('start', start)
//     unsubscribeFinish = router.on('finish', finish)
// })

// onUnmounted(() => {
//     unsubscribeStart?.()
//     unsubscribeFinish?.()
// })

const handleOpenDetails = () => {
    router.reload({
        only: ['details'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: (page) => {
            activeRow.value = page.props.details as Asset;
            openDetails.value = true;
        }
    });

}

const handleOpenHistories = () => {
    router.reload({
        only: ['histories', 'historiesPaginated'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: (page) => {
            activeRow.value = page.props.histories as Asset;
            openHistory.value = true;
        }
    });
}


const applyFilters = () => {
    router.get(
        assets.path,
        form,
        {
            only: ['assetsPaginated', 'filters'],
            preserveState: true,
            replace: true,
        }
    )
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
    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly) || isEqual(endDate, todayDateOnly)
}

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
