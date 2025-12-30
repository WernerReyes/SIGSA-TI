<template>
    <div class="flex items-center p-4">
        <Input class="max-w-sm" placeholder="Buscar activos..." :model-value="table.getState().globalFilter"
            @update:model-value="table.setGlobalFilter($event)" />
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline" class="ml-auto">
                    Columnas
                    <ChevronDown class="ml-2 h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">

                <DropdownMenuCheckboxItem
                    v-for="column in table.getAllColumns().filter((column) => column.getCanHide())" :key="column.id"
                    class="capitalize" :model-value="column.getIsVisible()"
                    @update:model-value="column.toggleVisibility()">
                    {{ column.columnDef.header }}
                </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
        </DropdownMenu>
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

                        <ContextMenuItem @click="openDetails = true">Ver detalle</ContextMenuItem>
                        <ContextMenuItem @click="openEdit = true">Editar</ContextMenuItem>
                        <ContextMenuItem @click="openAssign = true">Asignar</ContextMenuItem>
                        <ContextMenuItem @click="changeStatus = true">Cambiar estado</ContextMenuItem>

                    </ContextMenuContent>
                </ContextMenu>


            </template>
            <template v-else>
                <TableBody>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No se encontraron activos.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </template>
        </Table>

        <div class="flex items-center justify-end space-x-2 p-4">

            <div class="space-x-2">
                <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()">
                    Anterior
                </Button>
                <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    Siguiente
                </Button>
            </div>
        </div>


    </div>




    <DialogDetails v-model:open="openDetails" v-model:asset="activeRow" />
    <Dialog v-model:open-editor="openEdit" v-model:current-asset="activeRow"/>
    <AssignDialog v-model:open="openAssign" v-model:asset="activeRow" />
    <!-- :ticket="activeRow" -->
    <!-- <DetailsDialog v-model:open="openDetails" :ticket="activeRow" />
  <ReassignResponsableDialog v-model:open="openAssign" :ticket="activeRow" />
  <ChangeStatusDialog v-model:open="changeStatus" :ticket="activeRow" /> -->

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
import { valueUpdater } from '@/lib/utils';


import { ArrowUpDown, ChevronDown } from 'lucide-vue-next';
import { h, ref } from 'vue';
import { Asset, AssetStatus, statusOp } from '@/interfaces/asset.interface';
import { format, isAfter, parseISO } from 'date-fns';
import DialogDetails from './DialogDetails.vue';
import Dialog from './Dialog.vue';
import AssignDialog from './AssignDialog.vue';


const { assets } = defineProps<{ assets: Asset[] }>()

const activeRow = ref<Asset | null>(null)

const openDetails = ref(false);
const openEdit = ref(false);
const openAssign = ref(false);
const changeStatus = ref(false);


const sorting = ref<SortingState>([])

const columns: ColumnDef<Asset>[] = [
    {
        // accessorFn: row => `ast-${row.id} ${row.name.toLowerCase()}`,
        // filterFn: 'includesString',
        accessorKey: 'name',
        id: 'name',
        header: 'Nombre',
        minSize: 180,
        enableGlobalFilter: true,
        // cell: info => {
        //   return h(TicketColumnTable, {
        //     ticket: info.row.original
        //   });
        // },
    },
    {
        accessorKey: 'brand',
        id: 'brand',
        header: 'Marca',
        cell: info => info.getValue(),
    },
    {
        accessorKey: 'model',
        id: 'model',
        header: 'Modelo',
        cell: info => info.getValue(),
    },
    {
        accessorKey: 'serial_number',
        id: 'serial_number',
        header: 'Serial',
        cell: info => info.getValue(),
    },
    {
        // accessorKey: 'assigned_to.full_name',
        accessorFn: row => row?.assignment?.assigned_to ? row.assignment.assigned_to.full_name : 'Sin asignar',
        id: 'assigned_to',
        header: 'Asignado a',
        cell: info => {

            const assignedTo = info.row.original.assignment?.assigned_to;
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
                    
                    class: isValid ? 'bg-green-500' : 'bg-red-500'
                },
                () => format(parseISO(warrantyDate.split('T')[0]), 'dd/MM/yyyy')
            );

        }
    }


]


const isWarrantyValid = (warrantyEndDate: string): boolean => {
    // const warrantyEndDate = asset.warranty_expiration;

    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly)
}


const table = useVueTable({
    get data() { return assets },
    get columns() { return columns },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),

    globalFilterFn: 'includesString',
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    state: {
        get sorting() { return sorting.value },
    },
})
</script>
