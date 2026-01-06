<template>
    <div class="flex max-md:flex-col gap-4 items-center p-4 ">
        <Input class="max-w-sm" placeholder="Buscar activos..." v-model="form.search" />


        <div class="max-sm:w-full ml-auto flex gap-2 flex-wrap">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Tipos
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">

                    <DropdownMenuCheckboxItem :disabled="isLoading" v-for="type in types" :key="type.id"
                        class="capitalize" :model-value="form.types.includes(type.id)
                            " @update:model-value="(val) => {
                            if (val) {
                                form.types.push(type.id);
                            } else {
                                const index = form.types.indexOf(type.id);
                                if (index > -1) {
                                    form.types.splice(index, 1);
                                }
                            }
                        }">
                        {{ type.name }}

                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Todos los estados
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">

                    <DropdownMenuCheckboxItem :disabled="isLoading" v-for="status in Object.values(assetStatusOptions)"
                        :key="status.value" :model-value="form.status.includes(status.value)" @update:model-value="(val) => {
                            if (val) {
                                form.status.push(status.value);
                            } else {
                                const index = form.status.indexOf(status.value);
                                if (index > -1) {
                                    form.status.splice(index, 1);
                                }
                            }
                        }">
                        <Badge :class="status.bg">{{ status.label }}</Badge>
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>

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
                            <TableRow @contextmenu="activeRow = row.original" :key="row.id"
                                v-for="row in table.getRowModel().rows"
                                :data-state="row.getIsSelected() ? 'selected' : undefined" class="cursor-context-menu">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="pl-5"
                                    :style="{ width: cell.column.getSize() + 'px' }">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </ContextMenuTrigger>

                    <ContextMenuContent class="w-52">

                        <ContextMenuItem @click="openDetails = true">
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
                        <ContextMenuItem @click="openAssign = true">
                            <MonitorSmartphone />
                            Asignar
                        </ContextMenuItem>
                        <ContextMenuItem v-if="activeRow?.current_assignment" @click="openDevolution = true">
                            <MonitorSmartphone />
                            Devolver
                        </ContextMenuItem>
                        <ContextMenuItem @click="openHistory = true">
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
            <Pagination class="flex mx-0 w-fit ml-auto!" :items-per-page="assets.per_page" :total="assets.total"
                :default-page="assets.current_page">
                <PaginationContent>
                    <PaginationPrevious :disabled="isLoading || assets.current_page === 1" @click="!isLoading && router.visit(assets.prev_page_url || '', {
                        preserveScroll: true,
                        replace: true,
                    })">

                        <ChevronLeftIcon />
                        Anterior
                    </PaginationPrevious>
                    <template v-for="(item, index) in assets.links.filter(link => +link.label)" :key="index">
                        <PaginationItem :value="+item.label" :is-active="item.active" :disabled="isLoading" @click="!isLoading && router.visit(item.url, {
                            preserveScroll: true,
                            replace: true,
                        })">



                            {{ item.label }}

                        </PaginationItem>

                    </template>

                    <PaginationNext :disabled="isLoading || assets.current_page === assets.total" @click="!isLoading && router.visit(assets.next_page_url || '', {
                        preserveScroll: true,
                    })">

                        Siguiente
                        <ChevronRightIcon />
                    </PaginationNext>

                </PaginationContent>
            </Pagination>
        </div>
    </div>



    <DialogDetails v-model:open="openDetails" v-model:asset="activeRow" />
    <Dialog v-model:open-editor="openEdit" v-model:current-asset="activeRow" />
    <StatusDialog v-model:open="changeStatus" :asset="activeRow" />
    <AssignDialog v-model:open="openAssign" v-model:asset="activeRow" />
    <DevolutionDialog v-model:open="openDevolution" v-model:asset="activeRow" />
    <HistoryDialog v-model:open="openHistory" v-model:asset="activeRow" />

</template>


<script setup lang="ts">
import type { ColumnDef, SortingState } from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    //   PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination'
import { valueUpdater } from '@/lib/utils';


import { type Asset, AssetStatus, assetStatusOptions, AssetType, statusOp } from '@/interfaces/asset.interface';
import { Link, router, usePage } from '@inertiajs/vue3';
import { format, isAfter, parseISO } from 'date-fns';
import { ChevronDown, ChevronLeftIcon, Eye, MonitorSmartphone, Pencil, ChevronRightIcon, History } from 'lucide-vue-next';
import { computed, h, onMounted, reactive, ref } from 'vue';
import AssignDialog from './AssignDialog.vue';
import DevolutionDialog from './DevolutionDialog.vue';
import Dialog from './Dialog.vue';
import DialogDetails from './DialogDetails.vue';
import StatusDialog from './StatusDialog.vue';
import HistoryDialog from './HistoryDialog.vue';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { AssetsPaginated } from '../../interfaces/asset.interface';
import { watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { onUnmounted } from 'vue';


const { assets } = defineProps<{ assets: AssetsPaginated }>()

const activeRow = ref<Asset | null>(null)

const openDetails = ref(false);
const openEdit = ref(false);
const changeStatus = ref(false);
const openAssign = ref(false);
const openDevolution = ref(false);
const openHistory = ref(false);

const pagination = ref({
    pageIndex: 0,
    pageSize: 10,
})

const statusList = ref<AssetStatus[]>(Object.values(AssetStatus));

const sorting = ref<SortingState>([])


const assetsFiltered = computed(() => {
    let filteredAssets = assets.data;
    if (statusList.value.length > 0) {
        filteredAssets = filteredAssets.filter(asset => statusList.value.includes(asset.status));
    }

    if (selectedTypes.value.length > 0) {
        filteredAssets = filteredAssets.filter(asset => selectedTypes.value.includes(asset.type_id));
    }


    return filteredAssets;
});

const filters = computed(() => usePage().props.filters as Record<string, any>);

const form = reactive({
    search: filters.value.search || '',
    status: filters.value.status || [],
    types: filters.value.types || [],
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


const applyFilters = () => {

    router.get(
        assets.path,

        form,
        {
            preserveState: true,
            replace: true,
        }
    )
}


const types = computed(() => usePage().props.types as AssetType[]);

const selectedTypes = ref<number[]>(types.value.map(type => type.id));


const isLoading = ref(false)

const start = () => (isLoading.value = true)
const finish = () => (isLoading.value = false)

let unsubscribeStart: (() => void) | null = null
let unsubscribeFinish: (() => void) | null = null

onMounted(() => {
    unsubscribeStart = router.on('start', start)
    unsubscribeFinish = router.on('finish', finish)
})

onUnmounted(() => {
    unsubscribeStart?.()
    unsubscribeFinish?.()
})


const columns: ColumnDef<Asset>[] = [
    {

        accessorKey: 'name',
        id: 'name',
        header: 'Nombre',
        minSize: 180,
        enableGlobalFilter: true,

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
        accessorKey: 'processor',
        id: 'processor',
        header: 'Procesador',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),
    },
    {
        accessorKey: 'ram',
        id: 'ram',
        header: 'RAM',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),
    },
    {
        accessorKey: 'storage',
        id: 'storage',
        header: 'Almacenamiento',
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),
    },
    {

        accessorFn: row => row?.current_assignment?.assigned_to ? row.current_assignment.assigned_to.full_name : 'Sin asignar',
        id: 'assigned_to',
        header: 'Asignado a',
        cell: info => {

            const assignedTo = info.row.original.current_assignment?.assigned_to;
            if (assignedTo) {
                return info.getValue();
            } else {
                return h(Badge, { variant: 'secondary' }, () => 'Sin asignar');
            }
        },
    },
    {
        accessorKey: 'type.name',
        id: 'type',
        header: 'Tipo',
        cell: info => info.getValue(),
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
                () => op?.label
            );
        }
    }, {
        accessorKey: 'purchase_date',
        id: 'purchase_date',
        header: 'Fecha de compra',
        cell: info => format(parseISO((info.getValue() as string).split('T')[0]), 'dd/MM/yyyy'),
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
                () => format(parseISO(warrantyDate.split('T')[0]), 'dd/MM/yyyy')
            );

        }
    }


]

const isWarrantyValid = (warrantyEndDate: string): boolean => {
    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly)
}


const table = useVueTable({
    get data() { return assetsFiltered.value },
    get columns() { return columns },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    globalFilterFn: 'includesString',
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    state: {
        get pagination() { return pagination.value },
        get sorting() { return sorting.value },
    },
    onPaginationChange: updaterOrValue => valueUpdater(updaterOrValue, pagination),
})
</script>
