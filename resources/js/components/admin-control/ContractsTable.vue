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
                            <p class="text-xs text-muted-foreground">Seguimiento de licencias, servicios y garantías.</p>
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
                            <InputGroupInput v-model="search" placeholder="Buscar por contrato, proveedor o servicio..." />
                            <InputGroupAddon>
                                <Search class="h-4 w-4" />
                            </InputGroupAddon>
                        </InputGroup>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="type in filters"
                            :key="type"
                            size="sm"
                            :variant="activeFilter === type ? 'default' : 'outline'"
                            class="rounded-full"
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
                                <TableHead
                                    v-for="header in headerGroup.headers"
                                    :key="header.id"
                                    class="pl-4 uppercase text-[11px] tracking-wide text-muted-foreground">
                                    <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                        :props="header.getContext()" />
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <ContextMenu v-if="table.getRowModel().rows.length">
                            <ContextMenuTrigger as-child>
                                <TableBody>
                                    <TableRow
                                        v-for="row in table.getRowModel().rows"
                                        :key="row.id"
                                        @contextmenu="activeRow = row.original"
                                        class="cursor-context-menu transition hover:bg-muted/60 odd:bg-muted/30">
                                        <TableCell
                                            v-for="cell in row.getVisibleCells()"
                                            :key="cell.id"
                                            class="pl-4 align-middle">
                                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
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
                                <TableCell :colspan="columns.length" class="py-10 text-center text-sm text-muted-foreground">
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
import { createColumnHelper, FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { Archive, Eye, FileText, Pencil, RefreshCcw, Search } from 'lucide-vue-next';
import { computed, h, ref } from 'vue';

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

const rows = ref<ContractRow[]>([
    {
        id: 1,
        code: 'CTR-001',
        name: 'Microsoft 365 Business',
        vendor: 'Microsoft',
        type: 'Licencia',
        startDate: '2024-01-01',
        endDate: '2024-12-31',
        value: '$15.000',
        status: 'Activo',
    },
    {
        id: 2,
        code: 'CTR-002',
        name: 'Soporte SAP ERP',
        vendor: 'SAP',
        type: 'Soporte',
        startDate: '2023-06-01',
        endDate: '2024-02-28',
        value: '$45.000',
        status: 'Por vencer',
    },
    {
        id: 3,
        code: 'CTR-003',
        name: 'Antivirus Corporativo',
        vendor: 'CrowdStrike',
        type: 'Licencia',
        startDate: '2023-03-01',
        endDate: '2024-01-15',
        value: '$8.000',
        status: 'Vencido',
    },
    {
        id: 4,
        code: 'CTR-004',
        name: 'Hosting Cloud AWS',
        vendor: 'Amazon',
        type: 'Servicio',
        startDate: '2023-01-01',
        endDate: '2025-01-01',
        value: '$36.000',
        status: 'Activo',
    },
    {
        id: 5,
        code: 'CTR-005',
        name: 'Garantía Extendida Servidores',
        vendor: 'Dell',
        type: 'Hardware',
        startDate: '2022-01-01',
        endDate: '2025-01-01',
        value: '$12.000',
        status: 'Activo',
    },
]);

const filteredRows = computed(() => {
    const query = search.value.trim().toLowerCase();
    return rows.value.filter((row) => {
        const matchesFilter = activeFilter.value === 'Todos' || row.type === activeFilter.value;
        const matchesSearch =
            !query ||
            row.code.toLowerCase().includes(query) ||
            row.name.toLowerCase().includes(query) ||
            row.vendor.toLowerCase().includes(query);
        return matchesFilter && matchesSearch;
    });
});

const totals = computed(() => {
    return {
        total: rows.value.length,
        active: rows.value.filter(row => row.status === 'Activo').length,
        expiring: rows.value.filter(row => row.status === 'Por vencer').length,
        expired: rows.value.filter(row => row.status === 'Vencido').length,
    };
});

const columnHelper = createColumnHelper<ContractRow>();

const statusStyles: Record<ContractRow['status'], string> = {
    Activo: 'bg-success/10 text-success',
    'Por vencer': 'bg-warning/10 text-warning',
    Vencido: 'bg-critical/10 text-critical',
};

const typeStyles: Record<ContractRow['type'], string> = {
    Licencia: 'bg-primary/10 text-primary',
    Soporte: 'bg-info/10 text-info',
    Servicio: 'bg-muted text-foreground',
    Hardware: 'bg-secondary text-secondary-foreground',
};

const columns = [
    columnHelper.accessor('name', {
        header: 'Contrato',
        cell: ({ row }) =>
            h('div', { class: 'space-y-1' }, [
                h('p', { class: 'font-mono text-[11px] text-muted-foreground' }, row.original.code),
                h('p', { class: 'font-medium text-sm' }, row.original.name),
            ]),
    }),
    columnHelper.accessor('vendor', {
        header: 'Proveedor',
        cell: ({ row }) => h('span', { class: 'text-sm' }, row.original.vendor),
    }),
    columnHelper.accessor('type', {
        header: 'Tipo',
        cell: ({ row }) => h(Badge, { class: `${typeStyles[row.original.type]} text-[11px]` }, () => row.original.type),
    }),
    columnHelper.accessor('endDate', {
        header: 'Vigencia',
        cell: ({ row }) =>
            h('div', { class: 'text-xs' }, [
                h('p', {}, row.original.startDate),
                h('p', { class: 'text-muted-foreground' }, `al ${row.original.endDate}`),
            ]),
    }),
    columnHelper.accessor('value', {
        header: 'Valor',
        cell: ({ row }) => h('span', { class: 'font-semibold' }, row.original.value),
    }),
    columnHelper.accessor('status', {
        header: 'Estado',
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-2' }, [
                h('span', { class: `h-2 w-2 rounded-full ${statusStyles[row.original.status].includes('success') ? 'bg-success' : statusStyles[row.original.status].includes('warning') ? 'bg-warning' : 'bg-critical'}` }),
                h(Badge, { class: `${statusStyles[row.original.status]} text-[11px]` }, () => row.original.status),
            ]),
    }),
];

const table = useVueTable({
    data: filteredRows,
    columns,
    getCoreRowModel: getCoreRowModel(),
});
</script>
