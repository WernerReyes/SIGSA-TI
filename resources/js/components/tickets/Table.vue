<template>
  <div>
    <div class="flex items-center p-4">
      <Input class="max-w-sm" placeholder="Buscar tickets..."
        :model-value="table.getState().globalFilter" @update:model-value="table.setGlobalFilter($event)" />
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline" class="ml-auto">
            Columns
            <ChevronDown class="ml-2 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <!-- @update:model-value="(value) => {
              column.toggleVisibility(!!value)
            }" -->
          <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
            :key="column.id" class="capitalize" :model-value="column.getIsVisible()">
            {{ column.id }}
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
              <!-- <ContextMenuItem>Editar</ContextMenuItem> -->
              <ContextMenuItem @click="openDetails = true">Ver detalle</ContextMenuItem>
              <ContextMenuItem @click="openDetails = true">Asignar</ContextMenuItem>
              <ContextMenuItem @click="openDetails = true">Cambiar estado</ContextMenuItem>
              <!-- <ContextMenuSeparator />
              <ContextMenuItem variant="destructive">
                Eliminar
              </ContextMenuItem> -->
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
        <!-- <div class="flex-1 text-sm text-muted-foreground">
          {{ table.getFilteredSelectedRowModel().rows.length }} of
          {{ table.getFilteredRowModel().rows.length }} row(s) selected.
        </div> -->
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
  </div>

  <!-- :ticket="activeRow" -->
  <DetailsDialog v-model:open="openDetails" :ticket="activeRow" />
</template>


<script setup lang="ts">
import type { ColumnDef } from '@tanstack/vue-table';
import {
  FlexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  useVueTable,
} from '@tanstack/vue-table';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  ContextMenu,
  ContextMenuContent,
  ContextMenuItem,
  ContextMenuSeparator,
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

import { type Ticket, type TicketPriority, ticketPriorityOptions, TicketRequestType, ticketRequestTypeOptions, type TicketStatus, ticketStatusOptions, type TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface';
import { h, ref } from 'vue';
import DetailsDialog from './DetailsDialog.vue';
import TicketColumnTable from './TicketColumnTable.vue';

const { tickets } = defineProps<{ tickets: Ticket[] }>()

const activeRow = ref<Ticket | null>(null)

const openDetails = ref(false);

const globalFilter = ref('');

const columns: ColumnDef<Ticket>[] = [
  {
    accessorFn: row => `tk-${row.id} ${row.title.toLowerCase()}`,
    filterFn: 'includesString',
    id: 'ticket',
    header: 'Ticket',
    minSize: 180,
    enableGlobalFilter: true,
    // size: 450,
    cell: info => {
      return h(TicketColumnTable, {
        ticket: info.row.original
      });
    },
  },
  {
    accessorKey: 'type',
    accessorFn: row =>
      ticketTypeOptions[row.type]?.label.toLowerCase() ?? '',
    header: 'Tipo',
    enableGlobalFilter: true,
    cell: info => {

      const op = ticketTypeOptions[(info.row.original.type) as TicketType];
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
    header: 'Prioridad',
    size: 80,
    accessorFn: row =>
      ticketPriorityOptions[row.priority]?.search ?? '',

    enableGlobalFilter: true,
    cell: info => {
      const op = ticketPriorityOptions[(info.row.original.priority) as TicketPriority];
      return h(
        Badge,
        {
          class: op.bg
        },
        () => op.label
      );
    }
  },
  {
    accessorKey: 'status',
    header: 'Estado',
    enableGlobalFilter: true,
    accessorFn: row =>
      ticketStatusOptions[row.status]?.label.toLowerCase() ?? '',

    cell: info => {
      const op = ticketStatusOptions[(info.row.original.status) as TicketStatus];
      return h(
        Badge,
        {
          class: op.bg
        },
        () => op.label
      );
    }
  },

  {
    accessorKey: 'request_type',
    header: 'Categoría',
    enableGlobalFilter: true,
    accessorFn: row =>
      ticketRequestTypeOptions[row.request_type]?.label.toLowerCase() ?? 'sin categoria',
    cell: info => {
      const requestType = info.row.original.request_type;
      if (!requestType) {
        return h(
          "small",
          { class: 'text-muted-foreground' },
          'Sin categoría'
        );
      }
      const op = ticketRequestTypeOptions[(requestType) as TicketRequestType];
      return h(
        Badge,
        {
          class: op.bg
        },
        () => op.label
      );
    }
  }
]

const setGlobalFilter = (value: string) => {
  globalFilter.value = value
}

const table = useVueTable({
  get data() { return tickets },
  get columns() { return columns },
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  // state: {
  //   globalFilter
  // },
  // onGlobalFilterChange: setGlobalFilter,
  globalFilterFn: 'includesString',
  getPaginationRowModel: getPaginationRowModel(),
})
</script>
