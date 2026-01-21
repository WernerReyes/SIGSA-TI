<template>
  <div class="flex items-center gap-4 p-6 bg-background/50 backdrop-blur-sm border-b">
    <div class="flex-1 max-w-sm">
      <InputGroup>
        <InputGroupInput class="h-10 shadow-sm" placeholder="Buscar tickets..." v-model="form.searchTerm" />
        <InputGroupAddon>
          <Search />
        </InputGroupAddon>
      </InputGroup>

    </div>
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="outline" class="ml-auto shadow-sm hover:shadow-md transition-shadow">
          <Columns4 />
          Columnas
          <ChevronDown class="ml-2 size-4" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" class="w-48">
        <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
          :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
          @update:model-value="column.toggleVisibility()">
          {{ column.columnDef.id }}
        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
  <div class="rounded-lg border shadow-md bg-card overflow-hidden">
    <div class="overflow-x-auto">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id" class="bg-muted/50">
            <TableHead class="px-6 py-4 font-semibold text-sm" v-for="header in headerGroup.headers" :key="header.id"
              :style="{
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
                  :data-state="row.getIsSelected() ? 'selected' : undefined"
                  class="cursor-context-menu hover:bg-muted/50 transition-colors duration-150 border-b border-border/50">
                  <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-6 py-4"
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
              <ContextMenuItem @click="openReassign = true">
                <TicketPlus />
                Asignar
              </ContextMenuItem>
              <ContextMenuItem @click="changeStatus = true">
                <Pencil />
                Cambiar estado
              </ContextMenuItem>

            </ContextMenuContent>
          </ContextMenu>


        </template>
        <template v-else>
          <TableBody>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-40 text-center">
                <Empty class="p-6">
                  <EmptyHeader>
                    <EmptyMedia variant="icon">
                      <TicketX class="w-12 h-12 text-muted-foreground" />
                    </EmptyMedia>
                    <EmptyTitle>
                      Sin resultados
                    </EmptyTitle>
                    <EmptyDescription>
                      No se encontraron tickets que coincidan con su búsqueda.
                    </EmptyDescription>
                  </EmptyHeader>
                </Empty>
              </TableCell>
            </TableRow>
          </TableBody>
        </template>
      </Table>
    </div>

    <div class="flex items-center justify-between px-6 py-4 border-t bg-muted/20">
      <div class="text-sm text-muted-foreground">
        Mostrando <span class="font-medium">{{ tickets.from }}</span> a <span class="font-medium">{{ tickets.to ||
          0
        }}</span> de <span class="font-medium">{{ tickets.total }}</span> tickets
      </div>
      <Pagination class="mx-0 w-fit" :items-per-page="tickets.per_page" :total="tickets.total"
        :default-page="tickets.current_page">
        <PaginationContent class="flex-wrap gap-1">
          <PaginationPrevious :disabled="isLoading || tickets.current_page === 1"
            class="shadow-sm hover:shadow transition-shadow" @click="!isLoading && router.visit(tickets.prev_page_url || '', {
              preserveScroll: true,
              replace: true,
              only: ['tickets'],
            })">
            <ChevronLeftIcon class="h-4 w-4" />
            Anterior
          </PaginationPrevious>
          <template v-for="(item, index) in tickets.links.filter(link => +link.label)" :key="index">
            <PaginationItem :value="+item.label" :is-active="item.active" :disabled="isLoading || item.active"
              class="shadow-sm hover:shadow transition-shadow" @click="!isLoading && router.visit(item.url, {
                preserveScroll: true,
                replace: true,
                only: ['tickets'],
              })">
              {{ item.label }}
            </PaginationItem>
          </template>
          <PaginationNext :disabled="isLoading || tickets.current_page === tickets.last_page"
            class="shadow-sm hover:shadow transition-shadow" @click="!isLoading && router.visit(tickets.next_page_url || '', {
              preserveScroll: true,
              replace: true,
              only: ['tickets'],
            })">
            Siguiente
            <ChevronRightIcon class="h-4 w-4" />
          </PaginationNext>
        </PaginationContent>
      </Pagination>
    </div>
  </div>


  <!-- :ticket="activeRow" -->
  <DetailsDialog v-model:open="openDetails" :ticket="activeRow" />
  <AssignResponsibleDialog v-model:open="openReassign" :ticket="activeRow" />
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

import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { useApp } from '@/composables/useApp';
import { priorityOp, requestTypeOp, statusOp, type Ticket, typeOp } from '@/interfaces/ticket.interface';
import type { Paginated } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, ChevronDown, ChevronLeftIcon, ChevronRightIcon, Columns4, Eye, Pencil, Search, TicketPlus, TicketX, User } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';
import ChangeStatusDialog from './ChangeStatusDialog.vue';
import DetailsDialog from './DetailsDialog.vue';
import AssignResponsibleDialog from './AssignResponsibleDialog.vue';
import TicketColumnTable from './TicketColumnTable.vue';
import { useDebounceFn } from '@vueuse/core';
import { type User as IUser } from '@/interfaces/user.interface';
import { format } from 'date-fns';

const { tickets } = defineProps<{ tickets: Paginated<Ticket> }>()


const page = usePage();
const { isLoading } = useApp();

const activeRow = ref<Ticket | null>(null)

const openDetails = ref(false);
const openReassign = ref(false);
const changeStatus = ref(false);

const sorting = ref<SortingState>([])

type Filters = {
  searchTerm?: string;
}
const userAuth = computed(() => page.props.auth.user as IUser);

const filters = computed(() => page.props.filters as Filters || {});

const form = reactive<Filters>({
  searchTerm: filters.value.searchTerm || '',
});

watch(
  () => form.searchTerm,
  useDebounceFn(() => {
    applyFilters()
  }, 400)
)
function applyFilters() {

  router.get(
    tickets.path,
    form,
    {
      only: ['tickets', 'filters'],
      preserveState: true,
      replace: true,
    }
  )

}
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
        [
          op?.icon ? h(op.icon) : null,
          op?.label
        ]
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
        [
          op?.icon ? h(op.icon) : null,
          op?.label
        ]
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
        [
          op?.icon ? h(op.icon) : null,
          op?.label
        ]
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
        [
          op?.icon ? h(op.icon) : null,
          op?.label
        ]
      );
    }
  }, {
    accessorKey: 'requester',
    id: 'Solicitante',
    header: 'Solicitante',
    enableGlobalFilter: true,
    cell: info => {
      const user = info.getValue() as IUser;
      let fullName = user.full_name;
      if (user.staff_id === userAuth.value.staff_id) {
        fullName += ' (Tú)';
      }
      return h(Badge, {}, [
        h(User),
        fullName
      ])
    }
  },
  {
    accessorKey: 'responsible',
    id: 'Responsable',
    header: 'Responsable',
    enableGlobalFilter: true,
    cell: info => {
      const user = info.getValue() as IUser;
      if (!user) {
        return h(
          "small",
          { class: 'text-muted-foreground' },
          'No asignado'
        );
      }
      let fullName = user.full_name;
      if (user.staff_id === userAuth.value.staff_id) {
        fullName += ' (Tú)';
      }
      return h(Badge, {
        variant: 'secondary'
      }, [
        h(User),
        fullName
      ])
    }
  },
  {
    accessorKey: 'created_at',
    id: 'Abierto el',
    header: 'Abierto el',
    enableGlobalFilter: false,
    cell: info => {
      return format(new Date(info.getValue() as string), 'dd/MM/yyyy HH:mm');
    }
  }, {
    accessorKey: 'updated_at',
    id: 'Última actualización',
    header: 'Última actualización',
    enableGlobalFilter: false,
    cell: info => {
      return format(new Date(info.getValue() as string), 'dd/MM/yyyy HH:mm');
    }
  }

]



const table = useVueTable({
  get data() { return tickets.data },
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
