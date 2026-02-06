<template>
    <Card class="border-border/60 shadow-sm">
        <CardContent class="p-x-4">
            <div class="overflow-x-auto">

                <div class="flex items-center gap-2 mb-5">
                    <InputGroup>
                        <InputGroupInput class="w-full" placeholder="Buscar Servicios..." v-model="globalFilter" />
                        <InputGroupAddon>
                            <Search />
                        </InputGroupAddon>
                    </InputGroup>


                    <Popover>
                        <PopoverTrigger as-child>
                            <Button id="date" variant="outline" class="w-52 justify-between font-normal">
                                <CalendarSearch />

                                {{ formattedDate }}
                                <ChevronDownIcon />
                            </Button>
                            <RefreshCcw @click="() => {
                                dateRange = undefined
                            }" class="cursor-pointer text-muted-foreground hover:text-foreground" />

                        </PopoverTrigger>
                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                            <RangeCalendar locale="es" v-model="dateRange as any" layout="month-and-year" />
                        </PopoverContent>
                    </Popover>



                </div>

                <Table class="text-sm">
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

                    <ContextMenu v-if="table.getRowModel().rows.length">
                        <ContextMenuTrigger as-child>
                            <TableBody>
                                <TableRow @contextmenu="() => {
                                    activeRow = row.original
                                }" :key="row.id" v-for="row in table.getRowModel().rows"
                                    :data-state="row.getIsSelected() ? 'selected' : undefined"
                                    class="cursor-context-menu transition hover:bg-muted/60 odd:bg-muted/30">
                                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                                        class="pl-5 align-middle" :style="{ width: cell.column.getSize() + 'px' }">
                                        <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>

                        </ContextMenuTrigger>

                        <ContextMenuContent class="w-52">

                            <ContextMenuItem @click="emit('update:service', activeRow!)">
                                <Pencil />
                                Editar
                            </ContextMenuItem>



                            <ContextMenuItem @click="deleteDialogOpen = true">
                                <X />
                                Eliminar
                            </ContextMenuItem>
                        </ContextMenuContent>
                    </ContextMenu>

                    <TableBody v-else>
                        <TableRow>
                            <TableCell :colspan="columns.length" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <p class="text-sm text-muted-foreground">No hay servicios registrados</p>
                                    <p class="text-xs text-muted-foreground">Crea uno nuevo con el botón "Registrar
                                        servicio"</p>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>


            <div class="flex space-x-2 p-4 w-fit ml-auto">
                <Pagination v-slot="{ page }" :items-per-page="10" :total="table.getFilteredRowModel().rows.length">
                    <PaginationContent v-slot="{ items }">
                        <PaginationPrevious @click="table.previousPage()" />
                        <template v-for="(item, index) in items" :key="index">
                            <PaginationItem v-if="item.type === 'page'" :value="item.value"
                                :is-active="item.value === page" @click="table.setPageIndex(item.value - 1)">

                                {{ item.value }}
                            </PaginationItem>
                        </template>

                        <PaginationNext @click="table.nextPage()" />
                    </PaginationContent>
                </Pagination>
            </div>

        </CardContent>
    </Card>

    <AlertDialog v-model:open="deleteDialogOpen" :title="`Eliminar servicio: ${activeRow?.name || ''}`"
        description="¿Estás seguro de que deseas eliminar este servicio? Esta acción no se podra deshacer."
        @confirm="handleDelete" />
</template>


<script setup lang="ts">

import { Card, CardContent } from '@/components/ui/card';
import { ContextMenu, ContextMenuContent, ContextMenuItem, ContextMenuTrigger } from '@/components/ui/context-menu';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Service } from '@/interfaces/service.interface';
import { router } from '@inertiajs/vue3';
import { ColumnFiltersState, createColumnHelper, FlexRender, getCoreRowModel, getFilteredRowModel, getPaginationRowModel, useVueTable } from '@tanstack/vue-table';
import { endOfDay, format, startOfDay } from 'date-fns';
import { Pencil, Search, X, CalendarSearch, ChevronDownIcon, RefreshCcw } from 'lucide-vue-next';
import { computed, h, ref, watch } from 'vue';
import AlertDialog from '../AlertDialog.vue';
import CopyToChiptory from '../CopyToChiptory.vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { type DateRange } from 'reka-ui';
import { Button } from '@/components/ui/button';
import { getLocalTimeZone } from '@internationalized/date';
import { applyColumnFilter } from '@/lib/utils';

const props = defineProps<{
    rows: Service[];

}>();

const emit = defineEmits<{
    (e: 'update:service', service: Service): void,
}>();


const deleteDialogOpen = ref(false);
const pageSize = ref(10);
const globalFilter = ref('');
const dateRange = ref<DateRange | undefined>(undefined);
const activeRow = ref<Service | null>(null);

const columnFilters = ref<ColumnFiltersState>([]);

const columnHelper = createColumnHelper<Service>();

const formattedDate = computed(() => {
    if (!dateRange.value || (!dateRange.value?.start && !dateRange.value?.end)) {
        return 'Fecha creado';
    }
    const start = dateRange.value.start?.toDate(getLocalTimeZone()).toLocaleDateString();
    if (!dateRange.value.end) {
        return start;
    }
    const end = dateRange.value.end?.toDate(getLocalTimeZone()).toLocaleDateString();
    return `${start} - ${end}`;
});


watch(dateRange, (range) => applyColumnFilter(table, 'created_at', range));


const columns = [
    columnHelper.accessor('name', {
        header: 'Servicio',
        cell: ({ row }) => h('div', { class: 'text-foreground' }, row.original.name),
    }),
    columnHelper.accessor('url', {
        header: 'URL',
        cell: ({ row }) => h('a', { href: row.original.url, target: '_blank', class: 'text-primary hover:underline truncate' }, row.original.url),
    }),
    columnHelper.accessor('description', {
        header: 'Descripción',
        cell: ({ row }) => h('span', { class: 'text-muted-foreground line-clamp-1' }, row.original?.description || '—'),
    }),
    columnHelper.accessor('username', {
        header: 'Usuario',
        cell: ({ row }) => {

            return h(CopyToChiptory, { label: row.original.username });

        },
    }),
    columnHelper.accessor('password', {
        header: 'Contraseña',
        cell: ({ row }) => h(CopyToChiptory, { label: row.original.password, isSecret: true }),
    }),
    columnHelper.accessor('is_active', {
        header: 'Estado',
        cell: ({ row }) => h('div', { class: 'flex gap-1' }, [
            h('span', { class: row.original.is_active ? 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-900/40 dark:text-slate-200' }, row.original.is_active ? 'Activo' : 'Inactivo'),
        ]),
    }),
    columnHelper.accessor('created_at', {
        header: 'Creado el',
        filterFn: 'dateRange',
        cell: ({ row }) => h('span', { class: 'text-xs text-muted-foreground' }, format(new Date(row.original.created_at), 'dd/MM/yyyy HH:mm'))
    }),
    columnHelper.accessor('updated_at', {
        header: 'Actualizado el',
        cell: ({ row }) => h('span', { class: 'text-xs text-muted-foreground' }, format(new Date(row.original.updated_at), 'dd/MM/yyyy HH:mm'))
    }),

];


const filterFns = {
  dateRange: (row: any, columnId: string, range: DateRange | undefined) => {
    if (!range?.start && !range?.end) return true;

    const cellDate = new Date(row.getValue(columnId));

    if (range.start) {
      const start = startOfDay(range.start.toDate(getLocalTimeZone()));
      if (cellDate < start) return false;
    }

    if (range.end) {
      const end = endOfDay(range.end.toDate(getLocalTimeZone()));
      if (cellDate > end) return false;
    }

    return true;
  },
};



const handleDelete = () => {
    if (!activeRow.value) return;
    router.delete(`/access/${activeRow.value.id}`, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onSuccess: () => {
            router.replaceProp('services', (services: Service[]) => {
                return services.filter(s => s.id !== activeRow.value?.id);
            })
        }
    });
};

const table = useVueTable({
    // data: computed(() => props.rows),
    get data() {
        return props.rows;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnFiltersChange: (updater) => {
        columnFilters.value =
            updater instanceof Function ? updater(columnFilters.value) : updater;
    },
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
    },
    filterFns



});


table.setPageSize(pageSize.value);

</script>