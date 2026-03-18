<template>

    <div class="space-y-4">
        <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
            <div class="flex max-md:flex-col gap-4 items-start p-4">
                <div class="flex flex-col gap-2 w-full max-w-xl">

                    <div class="flex items-center gap-2">
                        <!-- <Input class="w-full" placeholder="Buscar activos..." v-model="form.search" /> -->
                        <InputGroup>
                            <InputGroupInput class="w-full" placeholder="Buscar activos..." v-model="form.search" />
                            <InputGroupAddon>
                                <Search />
                            </InputGroupAddon>
                        </InputGroup>

                        <Button variant="ghost" size="icon" :disabled="isLoading || !hasFilters" class="rounded-full"
                            @click="resetFilters">
                            <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />
                        </Button>
                    </div>

                   

                    <div v-if="hasFilters"
                        class="flex flex-wrap gap-2 text-xs text-muted-foreground items-center animate-in fade-in-50">
                        <Badge variant="outline" class="rounded-full">{{ filterCount }} {{
                            filterCount === 1 ? 'filtro aplicado' : 'filtros aplicados'
                            }}</Badge>

                        <template v-for="filter in filterstersRenders" :key="filter.label">
                            <Badge v-if="filter.value" variant="secondary" class="cursor-pointer" :class="{
                                'disabled': isLoading
                            }" @click="filter.click">
                                <Badge v-if="typeof filter.value === 'number' && filter.label !== 'Fechas'"
                                    class="h-4 min-w-4 rounded-full px-1 font-mono tabular-nums">
                                    {{ filter.value }}
                                </Badge>
                                {{ filter.label }}
                                <XCircle class="size-4 ml-1" />
                            </Badge>
                        </template>
                    </div>
                </div>

                <!-- max-sm:w-full ml-auto md:max-w-2/3 flex gap-2 flex-wrap justify-end items-end -->

                <div class="max-sm:w-full ml-auto flex gap-2 flex-wrap justify-end items-end">


                    <SelectFilters label="Asignados" :items="users" data-key="users" :icon="Users" :allow-null="true"
                        show-refresh show-selected-focus item-value="staff_id" item-label="full_name" :multiple="true"
                        :default-value="form.assigned_to" @select="(selects) => form.assigned_to = selects" />


                    <SelectFilters label="Departamentos" :items="departments" data-key="departments" :icon="Building"
                        show-refresh show-selected-focus item-value="id" item-label="name" :multiple="true"
                        :default-value="form.department_id" @select="(selects) => form.department_id = selects" />

                    <!-- {{ Compu }} -->

                    <SelectFilters label="Tipos" :items="assetTypes" data-key="types" :icon="MonitorSmartphone"
                        show-refresh show-selected-focus item-value="id" item-label="name" :multiple="true"
                        :default-value="form.types" @select="(selects) => form.types = selects">

                        <template #item="{ item }">
                            <div class="flex items-center gap-2">
                                <component :is="assetTypeOp(item.name)?.icon" class="size-4" />
                                {{ item.name }}
                            </div>
                        </template>

                    </SelectFilters>

                    <SelectFilters label="Marca" :items="uniqueBrands" data-key="uniqueBrands" :icon="Tag" show-refresh
                        show-selected-focus item-value="name" item-label="name" :multiple="true"
                        :default-value="form.brands" @select="(selects) => form.brands = selects" />


                    <SelectFilters label="Modelos" :items="uniqueModels" data-key="uniqueModels" :icon="Box" show-refresh
                        show-selected-focus item-value="name" item-label="name" :multiple="true"
                        :default-value="form.models" @select="(selects) => form.models = selects" />



                    <SelectFilters label="Estados" :items="Object.values(assetStatusOptions)" :icon="ChartArea"
                        show-refresh show-selected-focus item-value="value" item-label="label" :multiple="true"
                        :default-value="form.status" @select="(selects) => form.status = selects">

                        <template #item="{ item }">
                            <Badge :class="item.bg">
                                <component :is="item.icon" class="size-4" />
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
                            <Button variant="outline">
                                <Columns4 class="size-4" />
                                Columnas
                                <ChevronDown class="ml-2 size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">

                            <DropdownMenuCheckboxItem :disabled="isLoading"
                                v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                                :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
                                @update:model-value="column.toggleVisibility()">
                                {{ column.columnDef.header }}
                            </DropdownMenuCheckboxItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>


        <div class="rounded-xl border bg-card/70 shadow-sm backdrop-blur overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b bg-muted/20">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-wider text-muted-foreground">Panel de resultados</p>
                    <p class="text-sm font-semibold truncate">Tabla de activos y asignaciones</p>
                </div>
                <Button variant="outline" class="gap-2 shrink-0" @click="openAssignmentsByUserSheet = true">
                    <Users class="size-4" />
                    Ver asignaciones por usuario
                </Button>
            </div>

            <div class="overflow-x-auto">

                <Table class="min-w-full">
                    <TableHeader class="bg-muted/70 backdrop-blur sticky top-0 z-10">
                        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <TableHead class="pl-5 uppercase text-[11px] tracking-wide text-muted-foreground"
                                v-for="header in headerGroup.headers" :key="header.id" :style="{
                                    maxWidth: header.getSize() + 'px'
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
                                    <TableRow @contextmenu="() => {
                                        activeRow = row.original
                                    }" :key="row.id" v-for="row in table.getRowModel().rows"
                                        :id="`asset-row-${row.original.id}`"
                                        :data-state="row.getIsSelected() ? 'selected' : undefined" :class="[
                                            'cursor-context-menu transition hover:bg-muted/60',
                                            row.depth > 0
                                                ? 'bg-muted/15 text-muted-foreground border-l-2 border-primary/40'
                                                : 'odd:bg-muted/30',
                                            lastSelected === row.original.id
                                                ? 'bg-primary/15! border-l-4 border-primary/70 shadow-sm hover:bg-primary/20!'
                                                : '',
                                        ]">
                                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" :class="[
                                            'align-middle truncate whitespace-nowrap',
                                            cell.column.id === 'id'
                                                ? (row.depth > 0 ? 'pl-14' : 'pl-5')
                                                : 'pl-5'
                                        ]" :style="{ maxWidth: cell.column.getSize() + 'px' }">
                                            <FlexRender :render="cell.column.columnDef.cell"
                                                :props="cell.getContext()" />
                                        </TableCell>
                                    </TableRow>
                                </TableBody>

                            </ContextMenuTrigger>

                            <ContextMenuContent class="w-52">

                                <ContextMenuItem @click="handleOpenDetails()">
                                    <Eye />
                                    Ver detalle
                                </ContextMenuItem>
                                <ContextMenuItem @click="openEdit = true">
                                    <Pencil />
                                    Editar
                                </ContextMenuItem>
                                <ContextMenuItem @click="changeStatus = true">
                                    <Pencil />
                                    Cambiar estado
                                </ContextMenuItem>

                                <ContextMenuItem @click="openInvoice = true">
                                    <UploadCloud />
                                    Cargar factura
                                </ContextMenuItem>


                                <ContextMenuItem
                                    :disabled="[AssetStatus.DECOMMISSIONED, AssetStatus.IN_REPAIR].includes(activeRow!.status)"
                                    @click="openAssign = true">
                                    <MonitorSmartphone />
                                    Asignar
                                </ContextMenuItem>
                                <ContextMenuItem 
                                    :disabled="!activeRow?.current_assignment || activeRow?.status !== AssetStatus.ASSIGNED" @click="openDevolution = true">
                                    <MonitorSmartphone />
                                    Devolver
                                </ContextMenuItem>
                                <ContextMenuItem @click="handleOpenHistories()">
                                    <History />
                                    Ver historial
                                </ContextMenuItem>
                                <ContextMenuItem @click="openDelete = true"
                                    :disabled="AssetStatus.AVAILABLE !== activeRow?.status">
                                    <X />
                                    Eliminar
                                </ContextMenuItem>
                            </ContextMenuContent>
                        </ContextMenu>
                    </template>

                    <template v-else>
                        <TableBody>
                            <TableRow>
                                <TableCell :colspan="columns.length" class="text-center">

                                    <Empty class="p-6">
                                        <EmptyHeader>
                                            <EmptyMedia variant="icon">
                                                <MonitorSmartphone />
                                            </EmptyMedia>
                                            <EmptyTitle>
                                                Sin resultados
                                            </EmptyTitle>
                                            <EmptyDescription>
                                                No se encontraron equipos que coincidan con los filtros aplicados.
                                            </EmptyDescription>
                                        </EmptyHeader>


                                    </Empty>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </template>
                </Table>
            </div>

            <div class="flex space-x-2 p-4">
                <div class="text-sm text-muted-foreground">
        Mostrando <span class="font-medium">{{ assets.from }}</span> a <span class="font-medium">{{ assets.to ||
          0
        }}</span> de <span class="font-medium">{{ assets.total }}</span> activos
      </div>
                <Pagination class="mx-0  w-fit ml-auto!" :items-per-page="assets.per_page" :total="assets.total"
                    :default-page="assets.current_page">
                    <PaginationContent class="flex-wrap">
                        <PaginationPrevious :disabled="isLoading || assets.current_page === 1"
                            @click="changePage(assets.prev_page_url || '')">
                            <ChevronLeftIcon />
                            Anterior
                        </PaginationPrevious>
                        <template v-for="(item, index) in assets.links.filter(link => +link.label)" :key="index">
                            <PaginationItem :value="+item.label" :is-active="item.active"
                                :disabled="isLoading || item.active" @click="changePage(item.url || '')">
                                {{ item.label }}
                            </PaginationItem>
                        </template>

                        <PaginationNext :disabled="isLoading || assets.current_page === assets.last_page"
                            @click="changePage(assets.next_page_url || '')">
                            Siguiente
                            <ChevronRightIcon />
                        </PaginationNext>

                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>

    <Sheet v-model:open="openAssignmentsByUserSheet">
        <SheetContent side="right" class="w-full sm:w-200 overflow-y-auto">
            <SheetHeader class="space-y-3 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-linear-to-br from-cyan-200/60 to-blue-200/60 dark:from-cyan-900/40 dark:to-blue-900/40 flex items-center justify-center">
                        <Users class="size-5 text-blue-700 dark:text-blue-300" />
                    </div>
                    <div class="flex-1">
                        <SheetTitle class="text-lg">Asignaciones por usuario</SheetTitle>
                        <p class="text-xs text-muted-foreground">Consulta global de asignaciones por usuario (actuales
                            e históricas)</p>
                    </div>
                </div>
            </SheetHeader>

            <div class="space-y-4 py-4 px-2">
                <div class="rounded-xl border bg-card/60 p-3">
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Selecciona un usuario</p>
                    <SelectFilters :full-width="true" label="Usuario"  :items="users" data-key="users" :icon="Users"
                        show-refresh show-selected-focus :selected-as-label="true" item-value="staff_id" item-label="full_name"
                        :multiple="false" :default-value="selectedAssignmentsUserId ?? undefined"
                        @select="(selected) => selectedAssignmentsUserId = selected as number" />
                </div>

                <div v-if="!selectedAssignmentsUserId"
                    class="rounded-xl border border-dashed bg-muted/30 p-4 text-center text-xs text-muted-foreground">
                    Primero selecciona un usuario para cargar sus asignaciones globales.
                </div>

                <div v-else-if="isUserAssignmentsLoading" class="rounded-xl border bg-card/40 p-4 text-sm text-muted-foreground">
                    Cargando asignaciones del usuario...
                </div>

                <template v-else>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Asignaciones actuales</p>
                            <Badge class="text-[11px]">{{ currentUserAssignments.length }}</Badge>
                        </div>

                        <div v-if="!currentUserAssignments.length"
                            class="rounded-xl border border-dashed bg-muted/20 p-3 text-center text-xs text-muted-foreground">
                            No tiene asignaciones activas.
                        </div>

                        <Accordion v-else type="multiple" class="space-y-2">
                            <AccordionItem v-for="assignment in currentUserAssignments" :key="assignment.id"
                                :value="`current-${assignment.id}`" class="rounded-xl border bg-card/70 px-3">
                                <AccordionTrigger class="py-3 hover:no-underline">
                                    <div class="w-full text-left">
                                        <p class="text-sm flex items-center gap-2 font-semibold wrap-break-word">
                                            
                                             <component :is="assetTypeOp(assignment.asset?.type?.name as TypeName)?.icon" class="size-4 " />
                                            {{ assignment.asset?.full_name || `AST-${assignment.asset_id}` }}</p>
                                        <div class="flex items-center flex-wrap gap-2 mt-1">
                                            <p class="text-[11px] text-muted-foreground">Asignado: {{ formatDateLabel(assignment.assigned_at) }}</p>
                                            <Badge v-if="isStandaloneActiveAccessory(assignment)" variant="outline"
                                                class="text-[10px] border-amber-300 text-amber-700 dark:border-amber-700 dark:text-amber-300">
                                                Accesorio activo sin principal activo
                                            </Badge>
                                        </div>
                                    </div>
                                </AccordionTrigger>
                                <AccordionContent class="pb-3 space-y-2">
                                    <button type="button"
                                        class="w-full rounded-md border bg-card px-3 py-2 text-left hover:bg-muted transition"
                                        @click="openAssetDetailsFromSheet(assignment.asset?.id)">
                                        <p class="text-xs text-muted-foreground">{{ isStandaloneActiveAccessory(assignment)
                                            ? 'Accesorio principal activo' : 'Equipo principal' }}</p>
                                        <p class="text-sm font-semibold wrap-break-word">{{ assignment.asset?.full_name || `AST-${assignment.asset_id}` }}</p>
                                    </button>
                                    <div v-if="isStandaloneActiveAccessory(assignment)"
                                        class="rounded-md border border-amber-300/70 bg-amber-50/50 dark:border-amber-800 dark:bg-amber-950/20 px-3 py-2">
                                        <p class="text-[11px] text-amber-700 dark:text-amber-300">
                                            Principal devuelto:
                                            <span class="font-semibold">{{ assignment.parent_assignment?.asset?.full_name ||
                                                `AST-${assignment.parent_assignment?.asset_id || '-'}` }}</span>
                                        </p>
                                    </div>
                                    <div v-if="(assignment.children_assignments || []).length">
                                        <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wide mb-1">Accesorios</p>
                                        <div class="grid gap-2 border-l-2 border-dashed border-slate-200 dark:border-slate-900/50 pl-3">
                                            <button v-for="child in (assignment.children_assignments || [])" :key="child.id" type="button"
                                                class="rounded-md border px-2 py-2 text-left transition"
                                                :class="child.returned_at
                                                    ? 'bg-muted/20 border-slate-300/60 dark:border-slate-800'
                                                    : 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-800'"
                                                @click="openAssetDetailsFromSheet(child.asset?.id)">
                                                <p class="text-xs flex items-center gap-2 font-semibold wrap-break-word">
                                                        <component :is="assetTypeOp(child.asset?.type?.name as TypeName)?.icon" class="size-3   " />
                                                    {{ child.asset?.full_name || `AST-${child.asset_id}` }}</p>
                                                <div class="flex items-center justify-between gap-2 mt-1">
                                                    <p class="text-[11px] text-muted-foreground truncate">{{ child.asset?.type?.name || 'Accesorio' }}</p>
                                                    <Badge :variant="child.returned_at ? 'secondary' : 'default'" class="text-[10px]">
                                                        {{ child.returned_at ? 'Devuelto' : 'Activo' }}
                                                    </Badge>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>
                    </div>

                    <div class="space-y-2 pt-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Asignaciones devueltas</p>
                            <Badge variant="secondary" class="text-[11px]">{{ previousUserAssignments.length }}</Badge>
                        </div>

                        <div v-if="!previousUserAssignments.length"
                            class="rounded-xl border border-dashed bg-muted/20 p-3 text-center text-xs text-muted-foreground">
                            No tiene asignaciones históricas devueltas.
                        </div>

                        <Accordion v-else type="multiple" class="space-y-2">
                            <AccordionItem v-for="assignment in previousUserAssignments" :key="assignment.id"
                                :value="`prev-${assignment.id}`" class="rounded-xl border bg-card/60 px-3">
                                <AccordionTrigger class="py-3 hover:no-underline">
                                    <div class="w-full text-left">
                                        <p class="text-sm flex items-center gap-2 font-semibold wrap-break-word">
                                            <component :is="assetTypeOp(assignment.asset?.type?.name as TypeName)?.icon" class="size-4" />
                                            {{ assignment.asset?.full_name || `AST-${assignment.asset_id}` }}</p>
                                        <div class="flex items-center flex-wrap gap-2 mt-1">
                                            <p class="text-[11px] text-muted-foreground">Devuelto: {{ formatDateLabel(assignment.returned_at) }}</p>
                                            <Badge v-if="hasActiveChildren(assignment)" variant="outline"
                                                class="text-[10px] border-amber-300 text-amber-700 dark:border-amber-700 dark:text-amber-300">
                                                Tiene accesorios aun activos
                                            </Badge>
                                        </div>
                                    </div>
                                </AccordionTrigger>
                                <AccordionContent class="pb-3 space-y-2">
                                    <button type="button"
                                        class="w-full rounded-md border bg-card px-3 py-2 text-left hover:bg-muted transition"
                                        @click="openAssetDetailsFromSheet(assignment.asset?.id)">
                                        <p class="text-xs text-muted-foreground">Equipo principal</p>
                                        <p class="text-sm font-semibold wrap-break-word">{{ assignment.asset?.full_name || `AST-${assignment.asset_id}` }}</p>
                                    </button>
                                    <div v-if="(assignment.children_assignments || []).length">
                                        <p class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wide mb-1">Accesorios</p>
                                        <div class="grid gap-2 border-l-2 border-dashed border-slate-200 dark:border-slate-900/50 pl-3">
                                            <button v-for="child in (assignment.children_assignments || [])" :key="child.id" type="button"
                                                class="rounded-md border px-2 py-2 text-left transition"
                                                :class="child.returned_at
                                                    ? 'bg-muted/20 border-slate-300/60 dark:border-slate-800'
                                                    : 'bg-amber-50/60 border-amber-200 dark:bg-amber-950/20 dark:border-amber-800'"
                                                @click="openAssetDetailsFromSheet(child.asset?.id)">
                                                <p class="text-xs flex items-center gap-2 font-semibold wrap-break-word">
                                                     <component :is="assetTypeOp(child.asset?.type?.name as TypeName)?.icon" class="size-3 " />
                                                    {{ child.asset?.full_name || `AST-${child.asset_id}` }}</p>
                                                <div class="flex items-center justify-between gap-2 mt-1">
                                                    <p class="text-[11px] text-muted-foreground truncate">{{ child.asset?.type?.name || 'Accesorio' }}</p>
                                                    <Badge :variant="child.returned_at ? 'secondary' : 'default'" class="text-[10px]">
                                                        {{ child.returned_at ? 'Devuelto' : 'Activo' }}
                                                    </Badge>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>
                    </div>
                </template>
            </div>
        </SheetContent>
    </Sheet>
    

    <DialogDetails v-if="openDetails" v-model:open="openDetails" v-model:asset="activeRow"
        @locate-in-table="handleLocateFromDetails" />
    <Dialog v-model:open-editor="openEdit" v-model:current-asset="activeRow" />
    <InvoiceDialog v-if="openInvoice" v-model:open="openInvoice" :asset="activeRow" />
    <StatusDialog v-if="changeStatus" v-model:open="changeStatus" :asset="activeRow" />
    <AssignDialog v-if="openAssign" v-model:open="openAssign" v-model:asset="activeRow" />
    <DevolutionDialog v-if="openDevolution" v-model:open="openDevolution" v-model:asset="activeRow" />
    <HistoryDialog v-if="openHistory" v-model:open="openHistory" v-model:asset="activeRow" />

    <AlertDialog v-model:open="openDelete" title="¿Estás seguro de que deseas eliminar este equipo?"
        description="Esta acción no se puede deshacer. El equipo será eliminado permanentemente del sistema, incluyendo todos sus registros asociados ( asignaciones, historial de cambios de estado, etc. )."
        actionText="Eliminar" @confirm="handleDeleteAsset" />

</template>


<script setup lang="ts">

import type { ColumnDef, ExpandedState, SortingState } from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
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
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
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
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { valueUpdater } from '@/lib/utils';


import { Empty, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { type Asset, AssetStatus, assetStatusOptions, statusOp } from '@/interfaces/asset.interface';
import { assetTypeOp, TypeName } from '@/interfaces/assetType.interface';

import { router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { format, isAfter, isSameDay, parseISO, startOfDay } from 'date-fns';
import { Building, CalendarSearch, ChartArea, Check, ChevronDown, ChevronLeftIcon, ChevronRight, ChevronRightIcon, Tag, Box, Columns4, Eye, History, MonitorSmartphone, Pencil, RefreshCcw, Search, UploadCloud, UserIcon, Users, X, XCircle } from 'lucide-vue-next';
import { computed, h, nextTick, reactive, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { useApp } from '@/composables/useApp';
import type { Paginated } from '@/types';
import { getLocalTimeZone, parseDate } from '@internationalized/date';
import { type DateRange } from 'reka-ui';
import AlertDialog from '../AlertDialog.vue';
import SelectFilters from '../SelectFilters.vue';
import AssignDialog from './AssignDialog.vue';
import DevolutionDialog from './DevolutionDialog.vue';
import Dialog from './Dialog.vue';
import DialogDetails from './DialogDetails.vue';
import HistoryDialog from './HistoryDialog.vue';
import InvoiceDialog from './InvoiceDialog.vue';
import StatusDialog from './StatusDialog.vue';
import type { AssetAssignment } from '@/interfaces/assetAssignment.interface';


const { assets } = defineProps<{ assets: Paginated<Asset> }>()

const page = usePage();
const { isLoading, users, departments, assetTypes, assetAccessories, brands, models } = useApp();



const activeRow = ref<Asset | null>(null);

const openDetails = ref(false);
const openEdit = ref(false);
const changeStatus = ref(false);
const openAssign = ref(false);
const openDevolution = ref(false);
const openHistory = ref(false);
const openInvoice = ref(false);
const openDelete = ref(false);
const openAssignmentsByUserSheet = ref(false);
const locateAssetId = ref('');
const pendingLocateId = ref<number | null>(null);
const selectedAssignmentsUserId = ref<number | null>(null);
const isUserAssignmentsLoading = ref(false);

const sorting = ref<SortingState>([])

const lastSelected = ref<Asset['id'] | null>(null);

const filters = computed(() => page.props.filters as Record<string, any>);

const assetId = computed(() => activeRow.value?.id || null);

const uniqueBrands = computed(() =>  (page?.props?.uniqueBrands || []) as { name: string }[]);
const uniqueModels = computed(() =>  (page?.props?.uniqueModels || []) as { name: string }[]);

watch(() => activeRow.value, (asset) => {
    if (asset) {
        lastSelected.value = asset.id;
    }
})

const form = reactive<{
    search: string;
    status: AssetStatus[];
    types: number[];
    brands: string[];
    models: string[];
    assigned_to: (number | null)[];
    department_id: number[];
    dateRange?: DateRange | null;
}>({
    search: filters.value.search || '',
    status: filters.value.status || [],
    types: filters.value.types?.map((id: string) => +id) || [],
    brands: filters.value.brands || [],
    models: filters.value.models || [],
    assigned_to: filters.value.assigned_to?.map((id: string | null) => id ? +id : null) || [],
    department_id: filters.value.department_id || [],
    dateRange: filters.value?.startDate || filters.value?.endDate ? {
        start: filters.value?.startDate ? parseDate(filters.value.startDate) : undefined,
        end: filters.value?.endDate ? parseDate(filters.value.endDate) : undefined
    } : undefined
})

const filterCount = computed(() => {
    const base = form.search ? 1 : 0;
    const buckets = [form.status.length, form.types.length, form.assigned_to.length, form.department_id.length, form.dateRange ? 1 : 0];
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
    value: form.search,
    click: (): void => { form.search = '' }

}, {
    label: 'Estados',
    value: form.status.length,
    click: (): void => { form.status = [] }

}, {
    label: 'Tipos',
    value: form.types.length,
    click: (): void => { form.types = [] }

},
{
    label: 'Marcas',
    value: form.brands.length,
    click: (): void => { form.brands = [] }

}, {
    label: 'Modelos',
    value: form.models.length,
    click: (): void => { form.models = [] }

},

{
    label: 'Asignados',
    value: form.assigned_to.length,
    click: (): void => { form.assigned_to = [] }

}, {
    label: 'Departamentos',
    value: form.department_id.length,
    click: (): void => { form.department_id = [] }
}, {
    label: 'Fechas',
    value: form.dateRange ? 1 : 0,
    click: (): void => { form.dateRange = undefined }


}]
);

const userAssignments = computed<AssetAssignment[]>(() => {
    return (page.props.userAssignments || []) as AssetAssignment[];
});

const currentUserAssignments = computed<AssetAssignment[]>(() => {
    return userAssignments.value.filter(assignment => !assignment.returned_at);
});

const previousUserAssignments = computed<AssetAssignment[]>(() => {
    return userAssignments.value.filter(assignment => !!assignment.returned_at);
});

const isStandaloneActiveAccessory = (assignment: AssetAssignment): boolean => {
    return !!assignment.parent_assignment_id && !assignment.returned_at;
};

const hasActiveChildren = (assignment: AssetAssignment): boolean => {
    return (assignment.children_assignments || []).some(child => !child.returned_at);
};

const formatDateLabel = (value?: string | null): string => {
    if (!value) return '-';

    try {
        return format(parseISO(value), 'dd/MM/yyyy HH:mm');
    } catch {
        return value;
    }
};

const fetchAssignmentsByUser = (userId?: number | null) => {
    if (!userId) return;

    isUserAssignmentsLoading.value = true;
    router.reload({
        only: ['userAssignments'],
        data: { selected_user_id: userId },
        preserveUrl: true,
        onFinish: () => {
            isUserAssignmentsLoading.value = false;
        },
    });
};

watch(
    () => form.search,
    useDebounceFn(() => {
        applyFilters()
    }, 400)
)

watch(
    () => form.status,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.types,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.brands,
    () => {
        applyFilters()
    },
    { deep: true }
)


watch(
    () => form.models,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.assigned_to,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.department_id,
    () => {
        applyFilters()
    },
    { deep: true }
)

watch(
    () => form.dateRange,
    () => {
        applyFilters()
    },
    { deep: true }
)



const handleOpenDetails = () => {
    router.reload({
        only: ['details'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: () => {
            activeRow.value = {
                ...activeRow.value!,
                ...page.props.details as Asset,
            }
            openDetails.value = true;
        }

    });

}

const resolveAssetById = (id: number): Asset | null => {
    for (const currentAsset of assets.data) {
        if (currentAsset.id === id) {
            return currentAsset;
        }

        const children = currentAsset.current_assignment?.active_children_assignments || currentAsset.current_assignment?.children_assignments || [];
        const childMatch = children.find(child => child.asset?.id === id);

        if (childMatch?.asset) {
            return childMatch.asset;
        }
    }

    return null;
};

const focusRowByAssetId = async (id: number) => {
    lastSelected.value = id;
    await nextTick();

    const rowElement = document.getElementById(`asset-row-${id}`);
    if (!rowElement) return;

    rowElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

const locateAssetById = async () => {
    const cleaned = locateAssetId.value.replace(/\D/g, '');
    const parsedId = Number.parseInt(cleaned, 10);

    if (!Number.isFinite(parsedId) || parsedId <= 0) {
        toast.error('Ingresa un ID de activo válido.');
        return;
    }

    const selectedAsset = resolveAssetById(parsedId);

    if (!selectedAsset) {
        pendingLocateId.value = parsedId;
        form.search = parsedId.toString();
        applyFilters();
        toast.message('Buscando activo por ID en todo el inventario...');
        return;
    }

    activeRow.value = selectedAsset;
    await focusRowByAssetId(parsedId);
    toast.success(`Activo AST-${parsedId} ubicado.`);
};

const openAssetDetailsFromSheet = (assetId?: number) => {
    if (!assetId) return;

    activeRow.value = { id: assetId } as Asset;


    openAssignmentsByUserSheet.value = false;
    handleOpenDetails();
};

const handleLocateFromDetails = (assetId: number) => {
    openDetails.value = false;
    locateAssetId.value = assetId.toString();
    form.search = assetId.toString();
    applyFilters();
    toast.success(`Filtro aplicado por AST-${assetId}.`);
};

watch(
    () => assets.data,
    async () => {
        if (!pendingLocateId.value) return;

        const selectedAsset = resolveAssetById(pendingLocateId.value);
        if (!selectedAsset) return;

        activeRow.value = selectedAsset;
        await focusRowByAssetId(pendingLocateId.value);
        toast.success(`Activo AST-${pendingLocateId.value} ubicado.`);
        pendingLocateId.value = null;
    },
    { deep: true }
);

watch(
    () => selectedAssignmentsUserId.value,
    (userId) => {
        fetchAssignmentsByUser(userId);
    }
);

const handleOpenHistories = () => {
    router.reload({
        only: ['historiesPaginated'],
        data: { asset_id: assetId.value },
        preserveUrl: true,
        onSuccess: () => {
            openHistory.value = true;
        }
    });
}


const applyFilters = () => {
    const startDate = form.dateRange?.start ? format(form.dateRange.start.toDate(getLocalTimeZone()), 'yyyy-MM-dd') : null;
    const endDate = form.dateRange?.end ? format(form.dateRange.end.toDate(getLocalTimeZone()), 'yyyy-MM-dd') : null;
    router.get(
        assets.path,
        {
            search: form.search || undefined,
            status: form.status.length ? form.status : undefined,
            types: form.types.length ? form.types : undefined,
            brands: form.brands.length ? form.brands : undefined,
            models: form.models.length ? form.models : undefined,
            assigned_to: form.assigned_to.length ? form.assigned_to : undefined,
            department_id: form.department_id.length ? form.department_id : undefined,
            startDate: startDate || undefined,
            endDate: endDate || undefined,
        },
        {
            only: ['assetsPaginated', 'filters', 'stats'],
            preserveState: true,
            replace: true,
        }
    )
}

const resetFilters = () => {
    form.search = '';
    form.status = [];
    form.types = [];
    form.assigned_to = [];
    form.department_id = [];
    applyFilters();
};

const changePage = (url: string) => {
    if (isLoading.value) return;
    router.visit(url, {
        preserveScroll: true,
        replace: true,
        preserveState: true,
        only: ['assetsPaginated'],
    });
}

const handleDeleteAsset = () => {

    if (!assetId.value) return;

    const only = ['assetsPaginated', 'stats'];
    if (assetAccessories.value.some(acc => acc.id === assetId.value)) {
        only.push('accessories');
    }

    router.delete(`
                     /assets/${assetId.value}
                    `, {
        only,
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            activeRow.value = null;
            openDelete.value = false;
        },
    });
}

const columns: ColumnDef<Asset>[] = [
    {
        accessorKey: 'id',
        id: 'id',
        header: '#',
        size: 150,
         cell: ({ row }) => {
            const id = row.getValue('id')?.toString()?.padStart(3, '0');
            return row.getCanExpand() ? (
                h('div', { class: 'flex items-center justify-start truncate' }, [
                    h(
                        Button,
                        {
                            variant: 'ghost',
                            size: 'icon',
                            onClick: row.getToggleExpandedHandler()
                        },
                        () =>
                            row.getIsExpanded()
                                ? h(ChevronDown)
                                : h(ChevronRight)
                    ),
                    id
                ])
            ) : (
                h('div', { class: 'truncate' },
                   id)
            );
        }
    },
    {

        accessorKey: 'full_name',
        id: 'name',
        header: 'Nombre',
        size: 300,
        enableGlobalFilter: true,

        cell: ({ row }) => {
            return  h('div', { class: 'truncate' },
                    row.original.name) || h(Badge, { variant: 'secondary' }, () => 'N/A')
            
        }


    },
    {
        accessorKey: 'brand',
        id: 'brand',
        header: 'Marca',
        size: 150,
        cell: info => (info.getValue() as { name?: string } | null)?.name || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {
        accessorKey: 'model',
        id: 'model',
        header: 'Modelo',
        size: 200,
        cell: info => (info.getValue() as { name?: string } | null)?.name || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {
        accessorKey: 'serial_number',
        id: 'serial_number',
        header: 'Serial',
        size: 300,
        cell: info => info.getValue() || h(Badge, { variant: 'secondary' }, () => 'N/A'),

    },
    {

        accessorFn: row => row?.current_assignment?.assigned_to ? row.current_assignment.assigned_to.full_name : 'Sin asignar',
        id: 'assigned_to',
        header: 'Asignado a',
        size: 300,
        cell: info => {
            const assignedTo = info.row.original.current_assignment?.assigned_to;
            if (assignedTo) {
                return h(Badge, { variant: 'default', class: 'flex items-center gap-2' }, () => [
                    h(UserIcon, { class: '!size-5' }),
                    h('div', { class: 'flex flex-col' }, [
                        assignedTo.full_name,
                        h('small', { class: 'text-[10px] text-gray-400 dark:text-gray-700 italic' }, assignedTo.department ? assignedTo.department.name : 'Sin departamento')
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
        accessorKey: 'type.name',
        id: 'type',
        header: 'Tipo',
        size: 200,
        cell: info => h(
            'div',
            { class: 'flex items-center gap-2' },
            [
                h(
                    assetTypeOp(info.getValue() as TypeName)?.icon || 'span',
                    { class: 'size-4' }
                ),
                info.getValue() as string
            ]
        )
    },
    {
        accessorFn: row => row.is_new ? 'si' : 'no',
        id: 'is_new',
        header: 'Nuevo',
        cell: info => {
            const isNew = info.row.original.is_new;
            return isNew ? h(Badge, { class: 'bg-green-500' }, () => 'Sí') : h(Badge, { class: 'bg-gray-500' }, () => 'No');
        }
    },


    {
        accessorFn: row => statusOp(row.status)?.label.toLowerCase() ?? '',
        id: 'status',
        header: 'Estado',
        cell: info => {
            const op = statusOp(info.row.original.status as AssetStatus);
            return h(
                Badge,
                {
                    class: op?.bg
                },
                () => [op?.icon ? h(op.icon) : null, op?.label]
            );
        }
    }, {
        accessorKey: 'purchase_date',
        id: 'purchase_date',
        header: 'Fecha de compra',
        cell: info => info.getValue() ? format(info.getValue() as string, 'dd/MM/yyyy') : h(Badge, { variant: 'secondary' }, () => 'Sin fecha'),
    }, {
        accessorKey: 'warranty_expiration',
        id: 'warranty_expiration',
        header: 'Garantía hasta',
        cell: info => {
            const warrantyDate = info.getValue() as string | null;
            if (!warrantyDate) {
                return h(Badge, { variant: 'secondary' }, () => 'Sin fecha');
            }
            const isValid = isWarrantyValid(warrantyDate);
            return h(
                Badge,
                {
                    class: isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                },
                () => [
                    h(isValid ? Check : X),
                    format(warrantyDate, 'dd/MM/yyyy')
                ]
            );

        }
    }, {
        accessorKey: 'created_at',
        id: 'created_at',
        header: 'Creado el',
        cell: info => format(info.getValue() as string, 'dd/MM/yyyy HH:mm:ss')

    }, {
        accessorKey: 'updated_at',
        id: 'updated_at',
        header: 'Actualizado el',
        cell: info => format(info.getValue() as string, 'dd/MM/yyyy HH:mm:ss'),
    }


]

const isWarrantyValid = (warrantyEndDate: string): boolean => {
    const endDate = startOfDay(new Date(warrantyEndDate));
    const today = startOfDay(new Date());
    return isAfter(endDate, today) || isSameDay(endDate, today);
};

const expanded = ref<ExpandedState>({})

const table = useVueTable({
    get data() { return assets.data },
    get columns() { return columns },
    getSubRows: row => row.current_assignment?.active_children_assignments?.map(child => child.asset!) || [],
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    globalFilterFn: 'includesString',
    paginateExpandedRows: false,

    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() { return sorting.value },
        get expanded() { return expanded.value },
    },
})

</script>
