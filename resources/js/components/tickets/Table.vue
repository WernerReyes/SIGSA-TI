<template>
  <div class="flex max-md:flex-col items-center gap-4 p-6 bg-background/50 backdrop-blur-sm border-b">
    <div class="flex-1 w-full md:max-w-sm">
      <InputGroup>
        <InputGroupInput class="w-full" placeholder="Buscar tickets..." v-model="form.searchTerm" />
        <InputGroupAddon>
          <Search />
        </InputGroupAddon>
      </InputGroup>




      <div v-if="hasFilters"
        class="flex flex-wrap gap-2 text-xs mt-5 text-muted-foreground items-center animate-in fade-in-50">
        <Badge variant="outline" class="rounded-full">{{ filterCount }} {{ filterCount === 1 ? 'filtro activo' :
          'filtros activos' }}</Badge>
        <template v-for="filter in filterstersRenders" :key="filter.label">
          <Badge v-if="filter.value" @click="() => {
            if (isLoading) return;
            filter.click();
          }" variant="secondary" class="cursor-pointer" :class="{
            'disabled': isLoading
          }">
            <Badge v-if="typeof filter.value === 'number'" class="h-4 min-w-4 rounded-full px-1 font-mono tabular-nums">
              {{ filter.value }}
            </Badge>
            {{ filter.label }}
            <XCircle class="size-4" />
          </Badge>
        </template>

      </div>
    </div>


    <div class="max-sm:w-full  md:max-w-2/3 ml-auto flex gap-2 flex-wrap justify-end">
      <SelectFilters label="Solicitantes" :items="users" data-key="users" :icon="Users" show-refresh show-selected-focus
        item-value="staff_id" item-label="full_name" :multiple="true" :default-value="form.requesters"
        @select="(selects) => form.requesters = selects" />


      <SelectFilters label="Responsables" :items="TIUsers" data-key="TIUsers" :icon="Users" :allow-null="true"
        show-refresh show-selected-focus item-value="staff_id" item-label="full_name" :multiple="true"
        :default-value="form.responsibles" @select="(selects) => form.responsibles = selects" />

      <SelectFilters label="Tipos" :items="Object.values(ticketTypeOptions)" item-value="value" item-label="label"
        :icon="TicketPlus" :multiple="true" :default-value="form.types" @select="(selects) => form.types = selects">
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>

      <SelectFilters label="Estados" show-refresh show-selected-focus :items="Object.values(ticketStatusOptions)"
        item-value="value" item-label="label" :icon="ArrowUpDown" :default-value="form.statuses"
        @select="(selects) => form.statuses = selects" :multiple="true">
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>


      <SelectFilters label="Categoria" :allowNull="true" show-refresh show-selected-focus
        :items="Object.values(ticketRequestTypeOptions)" item-value="value" item-label="label" :icon="ArrowUpDown"
        :default-value="form.categories" @select="(selects) => form.categories = selects" :multiple="true">
        <template #item="{ item }">
          <Badge :class="item.bg" variant='outline' class="flex items-center gap-2">
            <component :is="item.icon || X" />
            {{ item.label }}
          </Badge>
        </template>
      </SelectFilters>

      <SelectFilters label="Prioridades" show-refresh show-selected-focus :items="Object.values(ticketPriorityOptions)"
        item-value="value" item-label="label" :icon="ArrowUpDown" :default-value="form.priorities"
        @select="(selects) => form.priorities = selects" :multiple="true">
        <template #item="{ item }">
          <Badge :class="item.bg" class="flex items-center gap-2">
            <component :is="item.icon" />
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
          <Button variant="outline" class="shadow-sm hover:shadow-md transition-shadow">
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
                <!-- <TicketPlus /> -->
                <component :is="activeRow?.responsible_id ? UserPen : UserPlus" />
                Responsable
              </ContextMenuItem>
              <ContextMenuItem @click="changeStatus = true" v-if="isFromTI">
                <Pencil />
                Cambiar estado
              </ContextMenuItem>
             
              <ContextMenuItem :disabled="disabledEdit" @click="emit('update:ticket', activeRow!)">
                <Pencil />
                Editar
              </ContextMenuItem>
              <!-- :disabled="!isSameUser(activeRow?.requester_id) && !isFromTI" -->
              <ContextMenuItem 
                @click="handleOpenHistories">
                <History />
                Ver historial
              </ContextMenuItem>
              <ContextMenuItem :disabled="disabledEdit" @click="openDelete = true">
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

  <DetailsDialog v-if="openDetails" v-model:open="openDetails" :ticket="activeRow" />
  <AssignResponsibleDialog v-if="openReassign" v-model:open="openReassign" v-model:ticket="activeRow" />
  <ChangeStatusDialog v-if="changeStatus" v-model:open="changeStatus" :ticket="activeRow" />
 
  <HistoryDialog v-if="openHistory" v-model:open="openHistory" :ticket="activeRow" />
 

  <AlertDialog v-model:open="openDelete" title="Eliminar ticket"
    description="¿Estás seguro de que deseas eliminar este ticket? Esta acción no se puede deshacer."
    actionText="Eliminar" @confirm="handleDeleteTicket" />

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

import { formatMinutes, valueUpdater } from '@/lib/utils';

import AlertDialog from '@/components/AlertDialog.vue';
import SelectFilters from '@/components/SelectFilters.vue';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { useApp } from '@/composables/useApp';
import {
  priorityOp, requestTypeOp, SlaTimeMinutes, statusOp, type Ticket,
  TicketPriority,
  ticketPriorityOptions,
  TicketRequestType,
  ticketRequestTypeOptions,
  TicketStatus,
  ticketStatusOptions,
  TicketType,
  ticketTypeOptions,
  typeOp
} from '@/interfaces/ticket.interface';
import { type User as IUser } from '@/interfaces/user.interface';
import type { Paginated } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { getLocalTimeZone, parseDate } from '@internationalized/date';
import { useDebounceFn } from '@vueuse/core';
import { format } from 'date-fns';
import { AlertCircle, ArrowUpDown, CalendarSearch, CheckCircle2, ChevronDown, ChevronLeftIcon, ChevronRightIcon, Clock, Columns4, Eye, History, Pencil, Search, TicketPlus, TicketX, Trash, User, UserPen, UserPlus, Users, X, XCircle } from 'lucide-vue-next';
import { type DateRange } from 'reka-ui';
import { computed, h, reactive, ref, watch } from 'vue';
import AssignResponsibleDialog from './AssignResponsibleDialog.vue';
import ChangeStatusDialog from './ChangeStatusDialog.vue';
import DetailsDialog from './DetailsDialog.vue';

import HistoryDialog from './HistoryDialog.vue';
import TicketColumnTable from './TicketColumnTable.vue';

const emit = defineEmits<{
  (e: 'update:ticket', ticket: Ticket): void,
}>();

const { tickets } = defineProps<{ tickets: Paginated<Ticket> }>()


const page = usePage();
const { isLoading, isFromTI, userAuth, isSameUser, users, TIUsers } = useApp();



const activeRow = ref<Ticket | null>(null)

const openDetails = ref(false);
const openReassign = ref(false);
const changeStatus = ref(false);

const openAssignEquipment = ref(false);
const openDevolution = ref(false);
const openHistory = ref(false);
const openDelete = ref(false);

const sorting = ref<SortingState>([])

type Filters = {
  searchTerm?: string;
  requesters?: number[];
  responsibles?: (number | null)[];
  types?: TicketType[];
  statuses?: TicketStatus[];
  priorities?: TicketPriority[];
  categories?: (TicketRequestType | null)[];
  dateRange?: DateRange;
}

const filters = computed(() => page.props.filters as
  Omit<Filters, 'dateRange'> & {
    startDate?: string;
    endDate?: string;
  }
  || {});

const filterCount = computed(() => {
  const base = form.searchTerm ? 1 : 0;
  const buckets = [form.statuses?.length, form.types?.length, form.requesters?.length, form.responsibles?.length, form.priorities?.length,
  form.categories?.length,
  form.dateRange ? 1 : 0];
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
  value: form.searchTerm,
  click: (): void => { form.searchTerm = '' }
}, {
  label: 'Solicitantes',
  value: form.requesters?.length,
  click: (): void => { form.requesters = [] }
}, {
  label: 'Responsables',
  value: form.responsibles?.length,
  click: (): void => { form.responsibles = [] }
}, {
  label: 'Tipos',
  value: form.types?.length,
  click: (): void => { form.types = [] }
}, {
  label: 'Estados',
  value: form.statuses?.length,
  click: (): void => { form.statuses = [] }
}, {
  label: 'Prioridades',
  value: form.priorities?.length,
  click: (): void => { form.priorities = [] }
}, {
  label: 'Categorías',
  value: form.categories?.length,
  click: (): void => { form.categories = [] }
}, {
  label: 'Rango de fechas',
  value: form.dateRange,
  click: (): void => { form.dateRange = undefined }
}]);

const disabledEdit = computed(() => {
  const ticket = activeRow.value;
  return isLoading.value || ticket?.status !== TicketStatus.OPEN || ticket?.responsible_id !== null;
})

const form = reactive<Filters>({
  searchTerm: filters.value?.searchTerm || '',
  requesters: filters.value?.requesters?.map(Number) || [],
  responsibles: filters.value?.responsibles?.map((v) => {
    return Number(v) || null;
  }) || [],
  types: filters.value?.types || [],
  statuses: filters.value?.statuses || [],
  priorities: filters.value?.priorities || [],
  categories: filters.value?.categories || [],
  dateRange: filters.value?.startDate || filters.value?.endDate ? {
    start: filters.value?.startDate ? parseDate(filters.value.startDate) : undefined,
    end: filters.value?.endDate ? parseDate(filters.value.endDate) : undefined
  } : undefined
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

watch(
  () => form.categories,
  () => {
    applyFilters()
  }
)

watch(
  () => form.dateRange,
  () => {
    applyFilters()
  }
)




function applyFilters() {
  const startDate = form.dateRange?.start ? form.dateRange.start.toDate(getLocalTimeZone()) : null;
  const endDate = form.dateRange?.end ? form.dateRange.end.toDate(getLocalTimeZone()) : null;
  router.get(
    tickets.path,
    {
      searchTerm: form.searchTerm || undefined,
      requesters: form.requesters?.length ? form.requesters : undefined,
      responsibles: form.responsibles?.length ? form.responsibles : undefined,
      types: form.types?.length ? form.types : undefined,
      statuses: form.statuses?.length ? form.statuses : undefined,
      priorities: form.priorities?.length ? form.priorities : undefined,
      categories: form.categories?.length ? form.categories : undefined,
      startDate: startDate ? startDate.toISOString().split('T')[0] : undefined,
      endDate: endDate ? endDate.toISOString().split('T')[0] : undefined,
    },
    {
      only: ['tickets', 'filters'],
      preserveState: true,
      replace: true,
    }
  )

}
const handleOpenHistories = () => {
  router.reload({
    only: ['historiesPaginated'],
    data: { ticket_id: activeRow.value?.id },
    preserveUrl: true,
    onSuccess: () => {
     
      openHistory.value = true;
    },

  });
}


const handleDeleteTicket = () => {
  router.delete(`tickets/${activeRow.value?.id}`, {
    only: ['tickets'],
    preserveScroll: true,
    replace: true,
    preserveState: true,
    onFlash: (flash) => {
      if (flash.error) return;
      openDelete.value = false;
      activeRow.value = null;
    }
  });
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
    accessorKey: 'category',
    id: 'Categoría',
    header: ({ column }) => header('Categoría', column),
    accessorFn: row =>
      requestTypeOp(row.category)?.label.toLowerCase() ?? 'sin categoria',
    cell: info => {
      const requestType = info.row.original.category;
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
        let fullName = user.full_name;
        if (user.staff_id === userAuth.value.staff_id) {
          fullName += ' (Tú)';
        }
        return h(Badge, { variant: 'default', class: 'flex items-center gap-2' }, () => [
          h(User, { class: '!size-5' }),
          h('div', { class: 'flex flex-col' }, [
            fullName,
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
  accessorKey: "sla_response_due_at",
  id: "Vence respuesta",
  header: ({ column }) => header("Vence respuesta", column),
  cell: info => {
    return renderSlaCell(
      info.row.original.sla_response_time_minutes,
      info.row.original.sla_response_due_at,
      info.row.original.first_response_at,
      {
        dueLabel: 'Debe responder',
        actualLabel: 'Respondio'
      }
    )
  }
}
,
  {
  accessorKey: "sla_resolution_due_at",
  id: "Vence solución",
  header: ({ column }) => header("Vence solución", column),
  cell: info =>
    renderSlaCell(
      info.row.original.sla_resolution_time_minutes,
      info.row.original.sla_resolution_due_at,
      info.row.original.resolved_at,
      {
        dueLabel: 'Debe resolver',
        actualLabel: 'Resuelto'
      }
    )
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




type SlaLabels = {
  dueLabel: string;
  actualLabel: string;
}

type SlaDate = {
  text: string;
  isMissing: boolean;
}

function formatSlaDate(value?: Date | string | null): SlaDate {
  if (!value) return { text: 'Sin registrar', isMissing: true };
  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) return { text: 'Sin registrar', isMissing: true };
  return { text: format(parsed, 'dd/MM/yyyy HH:mm'), isMissing: false };
}

function renderSlaDateLine(label: string, value?: Date | string | null) {
  const formatted = formatSlaDate(value);
  const className = formatted.isMissing
    ? 'text-[11px] text-muted-foreground flex items-center gap-1'
    : 'text-[11px] text-muted-foreground';
  return h('span', { class: className }, [
    formatted.isMissing ? h(AlertCircle, { class: 'size-3 text-muted-foreground/70' }) : null,
    `${label}: ${formatted.text}`
  ]);
}

function renderSlaCell(
  slaTimeMinutes: SlaTimeMinutes | null | undefined,
  dueAt?: Date | string | null,
  actualAt?: Date | string | null,
  labels: SlaLabels = { dueLabel: 'Vence', actualLabel: 'Atendido' }
) {
  if (!slaTimeMinutes) {
    return h('div', { class: 'flex flex-col gap-1 rounded-md bg-muted/30 px-2 py-1' }, [
      h(Badge, { variant: 'outline' }, 'Sin SLA'),
      renderSlaDateLine(labels.dueLabel, dueAt),
      renderSlaDateLine(labels.actualLabel, actualAt)
    ]);
  }

  let statusBadge = h(Badge, { variant: 'secondary' }, 'Sin tiempo');
  if (slaTimeMinutes.late_minutes) {
    statusBadge = h(Badge, { class: 'bg-red-500 text-white' }, [
      h(Clock, { class: 'size-4' }),
      `${formatMinutes(slaTimeMinutes.late_minutes)} tarde`
    ]);
  } else if (slaTimeMinutes.before_late_minutes) {
    statusBadge = h(Badge, { class: 'bg-green-500 text-white' }, [
      h(Clock, { class: 'size-4' }),
      `${formatMinutes(slaTimeMinutes.before_late_minutes)} antes`
    ]);
  } else if (slaTimeMinutes.remaining_minutes) {
    statusBadge = h(Badge, { class: 'bg-yellow-500 text-white' }, [
      h(CheckCircle2, { class: 'size-4' }),
      `${formatMinutes(slaTimeMinutes.remaining_minutes)} restantes`
    ]);
  }

  return h('div', { class: 'flex flex-col gap-1 rounded-md bg-muted/30 px-2 py-1' }, [
    statusBadge,
    renderSlaDateLine(labels.dueLabel, dueAt),
    renderSlaDateLine(labels.actualLabel, actualAt)
  ]);
}








</script>
