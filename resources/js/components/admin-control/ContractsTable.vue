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
                    <div class="flex flex-wrap gap-2 text-[11px] text-muted-foreground">
                        <Badge variant="outline" class="border-primary/30 text-primary bg-primary/5">
                            {{ totals.total }} contratos
                        </Badge>
                        <Badge variant="outline" class="border-success/30 text-success bg-success/5">
                            {{ totals.active }} activos
                        </Badge>
                        <Badge variant="outline" class="border-warning/30 text-warning bg-warning/5">
                            {{ totals.expiring }} por vencer
                        </Badge>
                        <Badge variant="outline" class="border-critical/30 text-critical bg-critical/5">
                            {{ totals.expired }} vencidos
                        </Badge>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:items-end">
                    <div class="w-full lg:w-80">
                        <InputGroup>
                            <InputGroupInput v-model="search"
                                placeholder="Buscar por contrato, proveedor o servicio..." />
                            <InputGroupAddon>
                                <Search class="h-4 w-4" />
                            </InputGroupAddon>
                        </InputGroup>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button v-for="type in filters" :key="type" size="sm"
                            :variant="activeFilter === type ? 'default' : 'outline'" class="rounded-full"
                            @click="activeFilter = type">
                            {{ type }}
                        </Button>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border bg-card/70 overflow-hidden">
                <div class="overflow-x-auto">
                    <Table class="min-w-full">
                        <TableHeader class="bg-muted/70 backdrop-blur sticky top-0 z-10">
                            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                                <TableHead v-for="header in headerGroup.headers" :key="header.id"
                                    class="pl-4 uppercase text-[11px] tracking-wide text-muted-foreground">
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
                                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                                            class="pl-4 align-middle">
                                            <FlexRender :render="cell.column.columnDef.cell"
                                                :props="cell.getContext()" />
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </ContextMenuTrigger>
                            <ContextMenuContent class="w-56">
                                <ContextMenuItem>
                                    <Eye class="h-4 w-4" />
                                    Ver detalles
                                </ContextMenuItem>
                                <ContextMenuItem>
                                    <Pencil class="h-4 w-4" />
                                    Editar contrato
                                </ContextMenuItem>
                                <ContextMenuItem>
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Contract, ContractPeriod, ContractStatus, getContractOp } from '@/interfaces/contract.interface';
import { billingFrequencyDaysMap } from '@/interfaces/contractBilling.interface';
import { parseDateOnly } from '@/lib/utils';
import { createColumnHelper, FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { addDays, format } from 'date-fns';
import { Archive, Eye, FileText, Pencil, RefreshCcw, Search } from 'lucide-vue-next';
import { RendererElement, RendererNode } from 'vue';
import { computed, h, ref, VNode } from 'vue';


const { contracts } = defineProps<{
    contracts: Contract[];
}>();

type ContractRow = {
    id: number;
    code: string;
    name: string;
    vendor: string;
    type: 'Licencia' | 'Soporte' | 'Servicio' | 'Hardware';
    startDate: string;
    endDate: string;
    value: string;
    status: 'Activo' | 'Por vencer' | 'Vencido';
};

const filters: Array<'Todos' | ContractRow['type']> = ['Todos', 'Licencia', 'Soporte', 'Servicio', 'Hardware'];
const activeFilter = ref<typeof filters[number]>('Todos');
const search = ref('');
const activeRow = ref<ContractRow | null>(null);

// const rows = ref<ContractRow[]>([
//     {
//         id: 1,
//         code: 'CTR-001',
//         name: 'Microsoft 365 Business',
//         vendor: 'Microsoft',
//         type: 'Licencia',
//         startDate: '2024-01-01',
//         endDate: '2024-12-31',
//         value: '$15.000',
//         status: 'Activo',
//     },
//     {
//         id: 2,
//         code: 'CTR-002',
//         name: 'Soporte SAP ERP',
//         vendor: 'SAP',
//         type: 'Soporte',
//         startDate: '2023-06-01',
//         endDate: '2024-02-28',
//         value: '$45.000',
//         status: 'Por vencer',
//     },
//     {
//         id: 3,
//         code: 'CTR-003',
//         name: 'Antivirus Corporativo',
//         vendor: 'CrowdStrike',
//         type: 'Licencia',
//         startDate: '2023-03-01',
//         endDate: '2024-01-15',
//         value: '$8.000',
//         status: 'Vencido',
//     },
//     {
//         id: 4,
//         code: 'CTR-004',
//         name: 'Hosting Cloud AWS',
//         vendor: 'Amazon',
//         type: 'Servicio',
//         startDate: '2023-01-01',
//         endDate: '2025-01-01',
//         value: '$36.000',
//         status: 'Activo',
//     },
//     {
//         id: 5,
//         code: 'CTR-005',
//         name: 'Garantía Extendida Servidores',
//         vendor: 'Dell',
//         type: 'Hardware',
//         startDate: '2022-01-01',
//         endDate: '2025-01-01',
//         value: '$12.000',
//         status: 'Activo',
//     },
// ]);

// const filteredRows = computed(() => {
//     const query = search.value.trim().toLowerCase();
//     return contracts.filter((row) => {
//         const matchesFilter = activeFilter.value === 'Todos' || row.type === activeFilter.value;
//         const matchesSearch =
//             !query ||
//             row.code.toLowerCase().includes(query) ||
//             row.name.toLowerCase().includes(query) ||
//             row.vendor.toLowerCase().includes(query);
//         return matchesFilter && matchesSearch;
//     });
// });

const totals = computed(() => {
    return {
        total: contracts.length,
        active: contracts.filter(row => row.status === ContractStatus.ACTIVE).length,
        expiring: contracts.filter(row => row.status === ContractStatus.EXPIRED).length,
        expired: contracts.filter(row => row.status === ContractStatus.EXPIRED).length,
    };
});

const columnHelper = createColumnHelper<Contract>();

// const statusStyles: Record<ContractRow['status'], string> = {
//     Activo: 'bg-success/10 text-success',
//     'Por vencer': 'bg-warning/10 text-warning',
//     Vencido: 'bg-critical/10 text-critical',
// };

// const typeStyles: Record<ContractRow['type'], string> = {
//     Licencia: 'bg-primary/10 text-primary',
//     Soporte: 'bg-info/10 text-info',
//     Servicio: 'bg-muted text-foreground',
//     Hardware: 'bg-secondary text-secondary-foreground',
// };

const columns = [
    columnHelper.accessor('name', {
        header: 'Contrato',
        cell: ({ row }) =>
            h('div', { class: 'space-y-1' }, [
                h('p', { class: 'font-mono text-[11px] text-muted-foreground' }, `CTR-${row.original.id.toString().padStart(3, '0')}`),
                h('p', { class: 'font-medium text-sm' }, row.original.name),
            ]),
    }),
    columnHelper.accessor('provider', {
        header: 'Proveedor',
        cell: ({ row }) => h('span', { class: 'text-sm' }, row.original.provider),
    }),
    columnHelper.accessor('type', {
        header: 'Tipo',
        cell: ({ row }) => {
            const type = getContractOp('type', row.original.type);
            return h(Badge, { class: `${type.bg} text-[11px]` }, () => [
                h(type?.icon || ''),
                type.label,
            ]);
        },
    }),


    columnHelper.accessor('period', {
        header: 'Periodo',
        cell: ({ row }) => {
            const period = getContractOp('period', row.original.period);
            return h(Badge, { class: `${period.bg} text-[11px]` }, () => [
                h(period?.icon || ''),
                period.label,
            ]);
        }
    }),


    columnHelper.accessor('end_date', {
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
    }),

    // columnHelper.accessor('billing.next_billing_date', {
    //     header: 'Próximo cobro',
    //     cell: ({ row }) => {
    //         const billing = row.original.billing;
    //         let nextBillingDate = billing?.next_billing_date;
    //         if (row.original.period == ContractPeriod.RECURRING) {
    //             const today = new Date();
    //             console.log(new Date(nextBillingDate));
    //             if (nextBillingDate && new Date(nextBillingDate) < today) {
    //                return;
    //             }
    //             nextBillingDate = addDays(new Date(nextBillingDate || ''), billingFrequencyDaysMap[billing!.frequency] || 0);
    //         }
    //         return h('span', { class: 'text-sm' }, nextBillingDate ? formatDate(nextBillingDate) : 'N/A');
    //     },
    // }),

    columnHelper.accessor('status', {
        header: 'Estado',
        cell: ({ row }) => {
            const status = getContractOp('status', row.original.status);
            return h(Badge, { class: `${status.bg} text-[11px]` }, () => [
                h(status?.icon || ''),
                status.label,
            ]);
        },
    }),
];

const table = useVueTable({
    // data: computed(() => contracts),
    get data() {
        return contracts;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
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
