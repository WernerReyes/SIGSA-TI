<template>
  <div class="flex max-md:flex-col items-center gap-4 p-6 bg-background/50 backdrop-blur-sm border-b">
    <div class="flex-1 w-full md:max-w-sm">
      <InputGroup>
        <InputGroupInput class="h-10 w-full shadow-sm" placeholder="Buscar tickets..." v-model="form.searchTerm" />
        <InputGroupAddon>
          <Search />
        </InputGroupAddon>
      </InputGroup>

    </div>
    <div class="max-sm:w-full ml-auto md:max-w-2/3 flex gap-2 flex-wrap  justify-end">

      <SelectFilters label="Solicitantes" v-model:selecteds="form.requesters" :items="users" data-key="users"
        :icon="Users" show-refresh show-selected-focus item-value="staff_id" item-label="full_name" multiple />


      <SelectFilters label="Responsables" v-model:selecteds="form.responsibles" :items="TIUsers" data-key="TIUsers"
        :icon="Users" allow-null show-refresh show-selected-focus item-value="staff_id" item-label="full_name"
        multiple />

      <SelectFilters label="Tipos" v-model:selecteds="form.types" show-refresh show-selected-focus
        :items="Object.values(ticketTypeOptions)" item-value="value" item-label="label" :icon="TicketPlus" multiple>
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>

      <SelectFilters label="Estados" show-refresh show-selected-focus v-model:selecteds="form.statuses"
        :items="Object.values(ticketStatusOptions)" item-value="value" item-label="label" :icon="ArrowUpDown" multiple>
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>

      <SelectFilters label="Prioridades" show-refresh show-selected-focus v-model:selecteds="form.priorities"
        :items="Object.values(ticketPriorityOptions)" item-value="value" item-label="label" :icon="ArrowUpDown"
        multiple>
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>

      

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


  </div>
  <div class="rounded-lg border shadow-md bg-card overflow-hidden">
    <div class="overflow-x-auto">
      <!-- <pre>{{ tickets.data }}</pre> -->
      <Table>
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
              <ContextMenuItem @click="openReassign = true" v-if="isFromTI">
                <TicketPlus />
                {{ activeRow?.responsible ? 'Reasignar' : 'Asignar' }}
              </ContextMenuItem>
              <ContextMenuItem @click="changeStatus = true" v-if="isFromTI">
                <Pencil />
                Cambiar estado
              </ContextMenuItem>
              <ContextMenuItem :disabled="disabledEdit" @click="openEdit = true">
                <Pencil />
                Editar
              </ContextMenuItem>
              <ContextMenuItem :disabled="disabledEdit">
                <Trash />
                Eliminar
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
  <DetailsDialog v-if="openDetails" v-model:open="openDetails" :ticket="activeRow" />
  <AssignResponsibleDialog v-if="openReassign" v-model:open="openReassign" v-model:ticket="activeRow" />
  <ChangeStatusDialog v-if="changeStatus" v-model:open="changeStatus" :ticket="activeRow" />
  <Dialog v-if="openEdit" v-model:open="openEdit" v-model:ticket="activeRow" />

</template>


<script setup lang="ts">
import type { Column, ColumnDef, SortingState } from '@tanstack/vue-table';
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
import {
  priorityOp, requestTypeOp, statusOp, type Ticket,
  TicketPriority,
  ticketPriorityOptions,
  TicketStatus,
  ticketStatusOptions,
  TicketType,
  ticketTypeOptions,
  typeOp
} from '@/interfaces/ticket.interface';
import { type User as IUser } from '@/interfaces/user.interface';
import type { Paginated } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { format } from 'date-fns';
import { ArrowUpDown, ChevronDown, ChevronLeftIcon, ChevronRightIcon, Columns4, Eye, Pencil, Search, TicketPlus, TicketX, Trash, User, Users, X } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';
import SelectFilters from '../SelectFilters.vue';
import AssignResponsibleDialog from './AssignResponsibleDialog.vue';
import ChangeStatusDialog from './ChangeStatusDialog.vue';
import DetailsDialog from './DetailsDialog.vue';
import Dialog from './Dialog.vue';
import TicketColumnTable from './TicketColumnTable.vue';

const { tickets } = defineProps<{ tickets: Paginated<Ticket> }>()


const page = usePage();
const { isLoading, isFromTI, userAuth, isSameUser, users, TIUsers } = useApp();

const activeRow = ref<Ticket | null>(null)

const openDetails = ref(false);
const openReassign = ref(false);
const changeStatus = ref(false);
const openEdit = ref(false);

const sorting = ref<SortingState>([])

type Filters = {
  searchTerm?: string;
  requesters?: (number | null)[];
  responsibles?: (number | null)[];
  types?: TicketType[];
  statuses?: TicketStatus[];
  priorities?: TicketPriority[];
}

const filters = computed(() => page.props.filters as Filters || {});

const disabledEdit = computed(() => {
  const ticket = activeRow.value;
  return !isSameUser(ticket?.requester_id) || isLoading.value || ticket?.status !== TicketStatus.OPEN || ticket?.responsible_id !== null;
})

const form = reactive<Filters>({
  searchTerm: filters.value?.searchTerm || '',
  requesters: filters.value?.requesters?.map(Number) || [],
  responsibles: filters.value?.responsibles?.map((v) => {
    return Number(v) || null;
  }) || [],
});



watch(
  () => form.searchTerm,
  useDebounceFn(() => {
    applyFilters()
  }, 400)
)

watch(
  () => form.requesters,
  () => {
    applyFilters()
  }
)

watch(
  () => form.responsibles,
  () => {
    applyFilters()
  }
)

watch(
  () => form.types,
  () => {
    applyFilters()
  }
)

watch(
  () => form.statuses,
  () => {
    applyFilters()
  }
)

watch(
  () => form.priorities,
  () => {
    applyFilters()
  }
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

    header: ({ column }) => header('Tipo', column),
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
    header: ({ column }) => header('Prioridad', column),
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
    header: ({ column }) => header('Estado', column),
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
    header: ({ column }) => header('Categoría', column),
    accessorFn: row =>
      requestTypeOp(row.request_type)?.label.toLowerCase() ?? 'sin categoria',
    cell: info => {
      const requestType = info.row.original.request_type;
      if (!requestType) {
        return h(
          Badge,
          {
            variant: 'outline'
          },
          [
            h(X),
            'Sin categoría'
          ]
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
    header: ({ column }) => header('Solicitante', column),
    cell: info => {
      const user = info.getValue() as IUser;
      if (user) {
        return h(Badge, { variant: 'default', class: 'flex items-center gap-2' }, () => [
          h(User, { class: '!size-5' }),
          h('div', { class: 'flex flex-col' }, [
            user.full_name,
            h('small', { class: 'text-[10px] text-gray-400 dark:text-gray-700 italic' }, user.department ? user.department.name : 'Sin departamento')
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
    accessorKey: 'responsible',
    id: 'Responsable',
    // header: 'Responsable',
    header: ({ column }) => header('Responsable', column),
    cell: info => {
      const user = info.getValue() as IUser;
      if (!user) {
        return h(
          Badge,
          {
            variant: 'outline'
          },
          [
            h(X),
            'Sin asignar'
          ]
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
    header: ({ column }) => header('Abierto el', column),
    cell: info => {
      if (!info.getValue()) return '';
      return format(new Date(info.getValue() as string), 'dd/MM/yyyy HH:mm');
    }
  }, {
    accessorKey: 'updated_at',
    id: 'Última actualización',
    header: ({ column }) => header('Última actualización', column),
    cell: info => {
      if (!info.getValue()) return '';
      return format(new Date(info.getValue() as string), 'dd/MM/yyyy HH:mm');
    }
  }

]


const header = (label: string, column: Column<Ticket, unknown>) => {
  return h(Button, {
    variant: 'ghost',
    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
  }, () => [
    h('span', {
      class: 'uppercase text-[11px] tracking-wide text-muted-foreground'
    }, label)
    , h(ArrowUpDown, { class: 'ml-2 size-4' })])
}

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
