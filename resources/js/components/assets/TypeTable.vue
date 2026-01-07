<template>

    <div class="w-full">
        <div class="flex items-center py-4">
            <Input class="max-w-sm" placeholder="Buscar nombres..."
                :model-value="table.getColumn('name')?.getFilterValue() as string"
                @update:model-value=" table.getColumn('name')?.setFilterValue($event)" />

        </div>
        <div class="rounded-md border lg:w-96">
            <Table
            
            >
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <template v-if="table.getRowModel().rows?.length">
                    <ContextMenu>
                        <ContextMenuTrigger as-child>

                            <TableBody>
                                <TableRow @contextmenu="currentType = row.original"
                                    v-for="row in table.getRowModel().rows" :key="row.id"
                                    :data-state="row.getIsSelected() && 'selected'">
                                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                        <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>


                        </ContextMenuTrigger>

                        <ContextMenuContent class="w-52">
                            <ContextMenuItem @click="() => emit('update:currentType', currentType)">
                                <Pencil />
                                Editar
                            </ContextMenuItem>
                            <ContextMenuItem @click="openAlert = true">
                                <Trash />
                                Eliminar

                            </ContextMenuItem>

                        </ContextMenuContent>
                    </ContextMenu>

                </template>



                <TableBody v-else>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No se encontraron tipos.
                        </TableCell>
                    </TableRow>
                </TableBody>

            </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">

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

    <AlertDialog v-model:open="openAlert">
        <!-- <AlertDialogTrigger>Open</AlertDialogTrigger> -->
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>

                    Estás seguro de que deseas eliminar este tipo?
                </AlertDialogTitle>
                <AlertDialogDescription>
                    Esta acción no se puede deshacer.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                <AlertDialogAction @click="handleDelete">Continuar</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>


</template>

<script setup lang="ts">
import type {
    ColumnDef,
    ColumnFiltersState,
    ExpandedState,
    SortingState,
    VisibilityState,
} from '@tanstack/vue-table'
import {
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table'

import { ArrowUpDown, Pencil, Trash } from 'lucide-vue-next'
import { h, ref } from 'vue'

import { Button } from '@/components/ui/button'
import { valueUpdater } from '@/lib/utils'


import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog'
import { ContextMenu, ContextMenuContent, ContextMenuItem, ContextMenuTrigger } from '@/components/ui/context-menu'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { type AssetType } from '@/interfaces/asset.interface'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { format } from 'date-fns'

const { assetTypes } = defineProps<{
    assetTypes: Array<AssetType>,

}>()

const localAssetTypes = ref<Array<AssetType>>([]);

const localAssetTypesC = computed({
    get: () => assetTypes,
    set: (val: Array<AssetType>) => { 
        localAssetTypes.value = val;
     }
})

const emit = defineEmits<{
    (e: 'update:currentType', value: AssetType | null): void
}>()



const currentType = ref<AssetType | null>(null)
const openAlert = ref(false)

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})

const handleDelete = () => {
    router.delete('/assets/types', {
        data: {
            id: currentType.value?.id,
        },
        onSuccess: () => {
            openAlert.value = false;
            emit('update:currentType', null);
            localAssetTypesC.value = localAssetTypesC.value.filter(type => type.id !== currentType.value?.id);
            
        },
    });
};

const columns: ColumnDef<AssetType>[] = [

    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) => row.getValue('id'),
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Nombre', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => row.getValue('name'),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Creado', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => format(row.getValue('created_at') as string, 'dd/MM/yyyy HH:mm:ss'),
    }, {
        accessorKey: 'updated_at',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Actualizado', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => format(row.getValue('updated_at') as string, 'dd/MM/yyyy HH:mm:ss'),
    }

]

const table = useVueTable({
    data: localAssetTypesC,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
        get rowSelection() { return rowSelection.value },
        get expanded() { return expanded.value },
    },
})

function copy(id: string) {
    navigator.clipboard.writeText(id)
}
</script>