<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            open = false;
            ticket = null;
        }
    }">
        <DialogContent class="max-h-screen sm:max-w-5xl">
            <DialogHeader class="space-y-2 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <!-- <History class="size-6 text-primary" /> -->

                        <!-- <component :is="assetTypeOp(asset?.type?.name).icon" class="size-6 text-primary" /> -->
                         <Ticket class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold leading-tight">Historial de {{ ticket?.title }}</h2>
                        <p class="text-sm text-muted-foreground">Consulta los movimientos y acciones realizadas sobre
                            este ticket.</p>
                        <p v-if="ticket"
                            class="text-xs text-muted-foreground mt-1 inline-flex gap-2 items-center bg-muted px-2 py-1 rounded-md">
                            <span class="font-mono">TK-{{ ticket?.id }}</span>
                            <span class="text-foreground">·</span>
                            <span class="font-medium line-clamp-1">{{ ticket?.description }}</span>
                        </p>
                    </div>
                </div>
            </DialogHeader>


            <div class="relative space-y-6 max-h-125 overflow-y-auto">

                <div class="sticky top-0 z-10 bg-background/90 backdrop-blur border-b border-border/80 pt-4 pb-3">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                            <Badge variant="secondary" class="rounded-full">Filtros</Badge>
                            <span v-if="actions.length" class="text-foreground">{{ actions.length }} acción(es)</span>
                            <span v-if="dateRange" class="text-foreground">Rango aplicado</span>
                        </div>
                        <div class="flex flex-wrap gap-3 md:ml-auto items-center">


                            <SelectFilters :items="Object.values(assetHistoryActionOptions)"
                                :show-selected-focus="false" :show-refresh="false" :label="'Seleccione una acción'"
                                item-label="label" item-value="value" selected-as-label :default-value="actions"
                                @select="(value) => actions = value" :multiple="true"
                                filter-placeholder="Buscar empleado..." empty-text="No se encontraron empleados">
                                <template #item="{ item }">
                                    <Badge :class="item.bg">
                                        <component :is="item.icon" class="size-4" />
                                        {{ item.label }}
                                    </Badge>
                                </template>
                            </SelectFilters>

                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button variant="outline" class="w-full sm:w-60 justify-between font-normal">
                                        {{ JSON.stringify(dateRange) !== '{}' && dateRange
                                            ?
                                            `${dateRange?.start?.toDate(getLocalTimeZone()).toLocaleDateString()}${dateRange?.end?.toDate(getLocalTimeZone()).toLocaleDateString()
                                                ? ' - ' : ''}${dateRange?.end?.toDate(getLocalTimeZone()).toLocaleDateString()
                                                || ''}`
                                            : 'Seleccionar rango de fechas' }}


                                        <ChevronDownIcon />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto overflow-hidden p-0" align="start">

                                    <RangeCalendar v-model="dateRange as any" class="rounded-md border shadow-sm"
                                        :number-of-months="2" disable-days-outside-current-view locale="es" />
                                </PopoverContent>
                            </Popover>

                            <Button variant="ghost" size="sm" :disabled="!actions.length && !dateRange"
                                @click="resetFilters">
                                <RefreshCcw class="size-4 mr-1" />
                            </Button>
                        </div>
                    </div>
                </div>


                <div class="space-y-6 sticky">
                    <Pagination class="mx-0 w-fit ml-auto!" :items-per-page="historiesPaginated.per_page"
                        :total="historiesPaginated.total" :default-page="historiesPaginated.current_page">
                        <PaginationContent class="flex-wrap">
                            <PaginationPrevious :disabled="isLoading || historiesPaginated.current_page === 1"
                                @click="!isLoading && changePage(historiesPaginated.prev_page_url)">

                                <ChevronLeftIcon />
                                Anterior
                            </PaginationPrevious>
                            <template v-for="(item, index) in historiesPaginated.links.filter(link => +link.label)"
                                :key="index">
                                <PaginationItem :value="+item.label" :is-active="item.active"
                                    :disabled="isLoading || item.active" @click="!isLoading && changePage(item.url)">
                                    {{ item.label }}
                                </PaginationItem>
                            </template>

                            <PaginationNext
                                :disabled="isLoading || historiesPaginated.current_page === historiesPaginated.last_page"
                                @click="!isLoading && changePage(historiesPaginated.next_page_url)" />



                        </PaginationContent>
                    </Pagination>




                    <Empty v-if="historiesPaginated.data.length === 0">
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <History />
                            </EmptyMedia>
                            <EmptyTitle>Sin historial</EmptyTitle>
                            <EmptyDescription>
                                No hay historial para este equipo.
                            </EmptyDescription>
                        </EmptyHeader>


                    </Empty>

                    <div v-for="history in historiesPaginated.data" class="relative flex gap-4 pl-12">
                        <div class="absolute left-4 top-0 bottom-0 w-px bg-border"></div>
                        <div
                            class="absolute left-2 w-6 h-6 rounded-full bg-card border-2 flex items-center justify-center text-info shadow-sm">

                            <component :is="actionOp(history.action).icon" class="size-3" />


                        </div>
                        <div class="flex-1 bg-muted/40 rounded-xl p-4 border">
                            <div class="flex items-start justify-between gap-3">
                                <Badge :class="actionOp(history.action).bg">{{ actionOp(history.action).label }}</Badge>


                                <span class="text-xs text-muted-foreground">{{ format(new Date(history.performed_at),
                                    'dd/MM/yyyy HH:mm') }}</span>
                            </div>

                           

                              


                            <p  class="text-xs text-muted-foreground mt-2">{{ history.description }}
                            </p>
                            <!-- </template> -->

                            <div class="flex items-center mt-3 gap-2"
                                v-if="[AssetHistoryAction.DELIVERY_RECORD_UPLOADED, AssetHistoryAction.INVOICE_UPLOADED].includes(history.action)">

                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger as-child>

                                            <Button class="ml-auto" size="sm" variant="outline" @click="() => {
                                                handleDownloadReceipt(history?.delivery_record?.file_url || history.invoice_url || '')
                                            }">

                                                <DownloadIcon class="size-4" />
                                                Descargar
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Descargar Comprobante</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>

                            <p class="text-xs text-muted-foreground mt-3">Por:

                                <Badge variant="outline">{{ history.performer?.full_name }}</Badge>
                            </p>
                        </div>
                    </div>


                    <div class="space-y-6">
                        <Pagination class="mx-0 w-fit ml-auto!" :items-per-page="historiesPaginated.per_page"
                            :total="historiesPaginated.total" :default-page="historiesPaginated.current_page">
                            <PaginationContent class="flex-wrap">
                                <PaginationPrevious :disabled="isLoading || historiesPaginated.current_page === 1"
                                    @click="!isLoading && changePage(historiesPaginated.prev_page_url)">

                                    <ChevronLeftIcon />
                                    Anterior
                                </PaginationPrevious>
                                <template v-for="(item, index) in historiesPaginated.links.filter(link => +link.label)"
                                    :key="index">
                                    <PaginationItem :value="+item.label" :is-active="item.active"
                                        :disabled="isLoading || item.active"
                                        @click="!isLoading && changePage(item.url)">
                                        {{ item.label }}
                                    </PaginationItem>
                                </template>

                                <PaginationNext
                                    :disabled="isLoading || historiesPaginated.current_page === historiesPaginated.last_page"
                                    @click="!isLoading && changePage(historiesPaginated.next_page_url)" />
                            </PaginationContent>
                        </Pagination>
                    </div>




                </div>
            </div>

        </DialogContent>
    </Dialog>


</template>

<script setup lang="ts">

import {
    Dialog,
    DialogContent,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious
} from '@/components/ui/pagination';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';

import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useApp } from '@/composables/useApp';
import { AssetStatusOption, assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import { actionOp, AssetHistory, AssetHistoryAction, assetHistoryActionOptions } from '@/interfaces/assetHistory.interface';
import type { Paginated, Variant } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { getLocalTimeZone } from '@internationalized/date';
import { format } from 'date-fns';
import { DownloadIcon, History, MonitorSmartphone, RefreshCcw, User, Ticket } from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import { type Component, computed, ref, watch } from 'vue';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { returnReasonOptions } from '@/interfaces/assetAssignment.interface';
import SelectFilters from '@/components/SelectFilters.vue';
import { type Ticket as ITicket } from '@/interfaces/ticket.interface';

const ticket = defineModel<ITicket | null>('ticket');
const open = defineModel<boolean>('open');

const page = usePage();
const { isLoading } = useApp();

const actions = ref<Array<AssetHistoryAction>>([]);

const dateRange = ref<DateRange | undefined>(undefined);

const historiesPaginated = computed<Paginated<AssetHistory>>(() => {

    return (page.props?.historiesPaginated || {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        links: [],
    }) as Paginated<AssetHistory>;
});





const includes = (str: string, search: string[]) => {
    return search.every(s => str.toLowerCase().includes(s.toLowerCase()));
};

watch(actions, () => {
    applyFilters();
});

watch(dateRange, () => {
    applyFilters();
});

const applyFilters = () => {
    router.visit(
        historiesPaginated.value?.path || '',
        {
            preserveScroll: true,
            replace: true,
            preserveState: true,
            preserveUrl: true,

            only: ['historiesPaginated'],
            data: {
                ticket_id: ticket?.value?.id,
                actions: actions.value,
                start_date: dateRange.value?.start ? dateRange.value.start.toDate(getLocalTimeZone()).toISOString().split('T')[0] : null,
                end_date: dateRange.value?.end ? dateRange.value.end.toDate(getLocalTimeZone()).toISOString().split('T')[0] : null,
            },
        });
};

const resetFilters = () => {
    actions.value = [];
    dateRange.value = undefined;
    applyFilters();
};

const changePage = (url?: string | null) => {
    router.visit(url || '', {
        preserveScroll: true,
        replace: true,
        preserveState: true,
        preserveUrl: true,
        only: ['historiesPaginated']
    })
};


const handleDownloadReceipt = (filePath: string) => {
    window.open(filePath, '_blank');
};


</script>