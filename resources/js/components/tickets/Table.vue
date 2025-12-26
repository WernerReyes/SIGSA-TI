<template>
  <div class="flex items-center p-4">
    <Input class="max-w-sm" placeholder="Buscar tickets..." :model-value="table.getState().globalFilter"
      @update:model-value="table.setGlobalFilter($event)" />
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="outline" class="ml-auto">
          Columnas
          <ChevronDown class="ml-2 h-4 w-4" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">

        <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
          :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
          @update:model-value="column.toggleVisibility()">
          {{ column.columnDef.id }}
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
              <TableRow @contextmenu="activeRow = row.original" :key="row.id" v-for="row in table.getRowModel().rows"
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
            <ContextMenuItem @click="openReassign = true">Asignar</ContextMenuItem>
            <ContextMenuItem @click="changeStatus = true">Cambiar estado</ContextMenuItem>

          </ContextMenuContent>
        </ContextMenu>


      </template>
      <template v-else>
        <TableBody>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              No se encontraron tickets.
            </TableCell>
          </TableRow>
        </TableBody>
      </template>
    </Table>

    <div class="flex items-center justify-end space-x-2 p-4">

      <div class="space-x-2">
        <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
          Anterior
        </Button>
        <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
          Siguiente
        </Button>
      </div>
    </div>


  </div>




  <!-- :ticket="activeRow" -->
  <DetailsDialog v-model:open="openDetails" :ticket="activeRow" />
  <ReassignResponsableDialog v-model:open="openReassign" :ticket="activeRow" />
  <ChangeStatusDialog v-model:open="changeStatus" :ticket="activeRow" />

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

import { priorityOp, requestTypeOp, statusOp, type Ticket, typeOp } from '@/interfaces/ticket.interface';
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next';
import { h, ref } from 'vue';
import ChangeStatusDialog from './ChangeStatusDialog.vue';
import DetailsDialog from './DetailsDialog.vue';
import ReassignResponsableDialog from './ReassignResponsableDialog.vue';
import TicketColumnTable from './TicketColumnTable.vue';

const { tickets } = defineProps<{ tickets: Ticket[] }>()

const activeRow = ref<Ticket | null>(null)

const openDetails = ref(false);
const openReassign = ref(false);
const changeStatus = ref(false);


const sorting = ref<SortingState>([])

const columns: ColumnDef<Ticket>[] = [
  {
    accessorFn: row => `tk-${row.id} ${row.title.toLowerCase()}`,
    filterFn: 'includesString',
    id: 'ticket',
    header: 'Ticket',
    minSize: 180,
    enableGlobalFilter: true,
    cell: info => {
      return h(TicketColumnTable, {
        ticket: info.row.original
      });
    },
  },
  {
    accessorKey: 'type',
    id: 'Tipo',
    accessorFn: row =>
      typeOp(row.type)?.label.toLowerCase() ?? '',
    // header: 'Tipo',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Tipo', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    // cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    enableGlobalFilter: true,
    cell: info => {
      const op = typeOp(info.row.original.type);
      return h(
        Badge,
        {
          class: op?.bg
        },
        () => op?.label
      );
    }
  },
  {
    accessorKey: 'priority',
    id: 'Prioridad',
    // header: 'Prioridad',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Prioridad', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    size: 80,
    accessorFn: row =>
      priorityOp(row.priority)?.label.toLowerCase() ?? '',

    enableGlobalFilter: true,
    cell: info => {
      const op = priorityOp(info.row.original.priority);
      return h(
        Badge,
        {
          class: op?.bg
        },
        () => op?.label
      );
    }
  },
  {
    accessorKey: 'status',
    id: 'Estado',
    // header: 'Estado',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Estado', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    enableGlobalFilter: true,
    accessorFn: row =>
      statusOp(row.status)?.label.toLowerCase() ?? '',

    cell: info => {
      const op = statusOp(info.row.original.status);
      return h(
        Badge,
        {
          class: op?.bg
        },
        () => op?.label
      );
    }
  },

  {
    accessorKey: 'request_type',
    id: 'Categoría',
    // header: 'Categoría',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Categoría', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    enableGlobalFilter: true,
    accessorFn: row =>
      requestTypeOp(row.request_type)?.label.toLowerCase() ?? 'sin categoria',
    cell: info => {
      const requestType = info.row.original.request_type;
      if (!requestType) {
        return h(
          "small",
          { class: 'text-muted-foreground' },
          'Sin categoría'
        );
      }
      const op = requestTypeOp(requestType);
      return h(
        Badge,
        {
          class: op?.bg
        },
        () => op?.label
      );
    }
  }
]



const table = useVueTable({
  get data() { return tickets },
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
