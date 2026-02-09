<template>
    <Card class="border-border/60 shadow-sm bg-linear-to-br from-muted/40 via-background to-background">
        <CardContent class="p-4 space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="h-9 w-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <FileText class="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Contratos activos y próximos a vencer</p>
                            <p class="text-xs text-muted-foreground">Seguimiento de licencias, servicios y garantías.
                            </p>
                        </div>
                    </div>
                   
                </div>

                <div class="flex flex-col gap-3 lg:items-end">
                    <div class="w-full lg:w-80">
                        <InputGroup>
                            <InputGroupInput v-model="globalFilter"
                                placeholder="Buscar por contrato, proveedor o servicio..." />
                            <InputGroupAddon>
                                <Search class="h-4 w-4" />
                            </InputGroupAddon>
                        </InputGroup>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <!-- <Button v-for="type in filters" :key="type" size="sm"
                            :variant="activeFilter === type ? 'default' : 'outline'" class="rounded-full"
                            @click="activeFilter = type">
                            {{ type }}
                        </Button> -->
                    </div>
                </div>
            </div>

            <div class="rounded-lg border bg-card/70 overflow-hidden">
                <div class="overflow-x-auto">
                    <Table class="min-w-full">
                        <TableHeader class="bg-muted/70 backdrop-blur sticky top-0 z-10">
                            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                                <TableHead v-for="header in headerGroup.headers" :key="header.id" :style="{
                                    maxWidth: `${header.getSize()}px`,
                                    // width: `${header.getSize()}px`,
                                }" class="pl-4 uppercase text-[11px] tracking-wide text-muted-foreground">
                                    <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                        :props="header.getContext()" />
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <ContextMenu v-if="table.getRowModel().rows.length">
                            <ContextMenuTrigger as-child>
                                <TableBody>
                                    <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                                        @contextmenu="activeRow = row.original"
                                        class="cursor-context-menu transition hover:bg-muted/60 odd:bg-muted/30">
                                        <TableCell :style="{
                                            maxWidth: `${cell.column.getSize()}px`,
                                            // width: `${cell.column.getSize()}px`,
                                        }" v-for="cell in row.getVisibleCells()" :key="cell.id"
                                            class="pl-4 align-middle">
                                            <FlexRender :render="cell.column.columnDef.cell"
                                                :props="cell.getContext()" />
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </ContextMenuTrigger>
                            <ContextMenuContent class="w-56">
                                <ContextMenuItem @click="emit('view-details', activeRow)">
                                    <Eye class="h-4 w-4" />
                                    Ver detalles
                                </ContextMenuItem>
                                <ContextMenuItem @click="emit('edit-contract', activeRow)">
                                    <Pencil class="h-4 w-4" />
                                    Editar contrato
                                </ContextMenuItem>
                                <ContextMenuItem
                                    :disabled="activeRow?.billing?.auto_renew || activeRow?.status === ContractStatus.ACTIVE"
                                    @click="emit('renew-contract', activeRow)">
                                    <RefreshCcw class="h-4 w-4" />
                                    Renovar
                                </ContextMenuItem>
                                <ContextMenuSeparator />
                                <ContextMenuItem class="text-warning">
                                    <Archive class="h-4 w-4" />
                                    Archivar
                                </ContextMenuItem>
                            </ContextMenuContent>
                        </ContextMenu>

                        <TableBody v-else>
                            <TableRow>
                                <TableCell :colspan="columns.length"
                                    class="py-10 text-center text-sm text-muted-foreground">
                                    No hay contratos para mostrar.
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
            </div>


        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { ContextMenu, ContextMenuContent, ContextMenuItem, ContextMenuSeparator, ContextMenuTrigger } from '@/components/ui/context-menu';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Contract, ContractPeriod, ContractStatus, getContractOp } from '@/interfaces/contract.interface';
import { parseDateOnly } from '@/lib/utils';
import { ColumnDef, FlexRender, getCoreRowModel, getFilteredRowModel, getPaginationRowModel, useVueTable } from '@tanstack/vue-table';
import { format } from 'date-fns';
import { Archive, Eye, FileText, Pencil, RefreshCcw, Search } from 'lucide-vue-next';
import { computed, h, ref, RendererElement, RendererNode, VNode } from 'vue';


const emit = defineEmits<{
    (e: 'view-details', contract: Contract | null): void;
    (e: 'edit-contract', contract: Contract | null): void;
    (e: 'renew-contract', contract: Contract | null): void;
}>();

const { contracts } = defineProps<{
    contracts: Contract[];
}>();





const activeRow = ref<Contract | null>(null);

const globalFilter = ref('');



const columns: ColumnDef<Contract>[] = [
    {
        id: 'name',
        header: 'Contrato',
        accessorKey: 'name',
        size: 200,

        cell: ({ row }) =>
            h('div', { class: 'space-y-1' }, [
                h('p', { class: 'font-mono text-[11px] text-muted-foreground' }, `CTR-${row.original.id.toString().padStart(3, '0')}`),
                h('p', { class: 'font-medium text-sm text-foreground truncate' }, row.original.name),
            ]),

    }, {
        id: 'provider',
        header: 'Proveedor',
        accessorKey: 'provider',
        cell: ({ row }) => h('span', { class: 'text-sm' }, row.original.provider),

    }, {
        id: 'type',
        header: 'Tipo',
        accessorFn: row => getContractOp('type', row.type)?.label || row.type,
        cell: ({ row }) => {
            const type = getContractOp('type', row.original.type);
            return h(Badge, { class: `${type.bg} text-[11px]` }, () => [
                h(type?.icon || ''),
                type.label,
            ]);
        },
    }, {
        id: 'period',
        header: 'Periodo',
        accessorFn: row => getContractOp('period', row.period)?.label || row.period,
        cell: ({ row }) => {
            const period = getContractOp('period', row.original.period);
            return h(Badge, { class: `${period.bg} text-[11px]` }, () => [
                h(period?.icon || ''),
                period.label,
            ]);
        }
    }, {
        id: 'end_date',
        accessorFn: row => {
            const period = row.period;
            if (period === ContractPeriod.RECURRING) {
                if (row.billing?.auto_renew) {
                    return 'Renovable automáticamente';
                } else {
                    return 'Sin renovación automática';
                }
            } else if (period === ContractPeriod.ONE_TIME) {
                if (row.end_date) {
                    return `con garantía hasta el ${formatDate(row.end_date)}`;
                } else {
                    return 'sin fecha de finalización';
                }
            }
            return row.end_date ? formatDate(row.end_date) : 'N/A';
        },
        header: 'Vigencia',
        cell: ({ row }) => {
            const period = row.original.period;

            let dinamicHtml: VNode<RendererNode, RendererElement, {
                [key: string]: any;
            }> = h('span', { class: 'text-muted-foreground text-xs' }, `al ${formatDate(row.original.end_date)}`);

            if (period === ContractPeriod.RECURRING) {
                if (row.original.billing?.auto_renew) {
                    dinamicHtml = h('span', { class: 'text-emerald-600/80 text-xs' }, 'Renovable automáticamente');
                } else {
                    dinamicHtml = h('span', { class: 'text-red-600/80 text-xs' }, 'Sin renovación automática');
                }
            } else if (period === ContractPeriod.ONE_TIME) {
                if (row.original.end_date) {
                    dinamicHtml = h('span', { class: 'text-muted-foreground text-xs' }, `con garantía hasta el ${formatDate(row.original.end_date)}`);
                } else {
                    dinamicHtml = h('span', { class: 'text-muted-foreground text-xs' }, 'sin fecha de finalización');
                }
            }
            return h('div', { class: 'text-xs space-y-1' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h('span', { class: 'font-medium text-foreground' }, formatDate(row.original.start_date)),

                ]),
                dinamicHtml

            ]);
        },
    }, {
        id: 'status',
        header: 'Estado',
        accessorFn: row => getContractOp('status', row.status)?.label || row.status,
        cell: ({ row }) => {
            const status = getContractOp('status', row.original.status);
            return h(Badge, { class: `${status.bg} text-[11px]` }, () => [
                h(status?.icon || ''),
                status.label,
            ]);
        },
    }, 
    {
        id: 'created_at',
        header: 'Creado',
        accessorKey: 'created_at',
        cell: ({ row }) => h('span', { class: 'text-sm text-muted-foreground' }, format(row.getValue('created_at'), 'dd/MM/yyyy HH:mm') ),

    }, {
        id: 'updated_at',
        header: 'Actualizado',
        accessorKey: 'updated_at',
        cell: ({ row }) => h('span', { class: 'text-sm text-muted-foreground' }, format(row.getValue('updated_at'), 'dd/MM/yyyy HH:mm') ),
    }

];

const table = useVueTable({
    get data() {
        return contracts;
    },

    columns,

    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
    },
});

const formatDate = (dateStr: Date | string | null = '') => {
    if (!dateStr) return 'N/A';
    if (dateStr instanceof Date) {
        return format(dateStr, 'dd/MM/yyyy');
    }
    return format(parseDateOnly(dateStr || ''), 'dd/MM/yyyy');
};


//  billingFrequencyDaysMap
</script>
