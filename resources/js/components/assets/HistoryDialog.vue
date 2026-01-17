<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            open = false;
            asset = null;
        }
    }">
        <DialogContent class="max-h-screen sm:max-w-5/12  overflow-y-auto">
            <DialogHeader>
                <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                    <h2 id="radix-:ri:" class="text-lg font-semibold leading-none tracking-tight">Historial de Activo
                    </h2>
                    <p class="text-sm text-muted-foreground">{{ asset?.name }} (AST-{{ asset?.id }})</p>
                </div>

            </DialogHeader>


            <div class="relative">

                <!-- Sticky filters -->
                <div
                    class="sticky top-0 bg-background flex justify-center sm:justify-end items-center flex-wrap gap-4 z-10 pt-4 pb-2 mb-4 border-b border-border">
                    <!-- <Cable -->

                    <Select multiple v-model="actions" class="sm:w-48">
                        <SelectTrigger class="sm:w-48 w-full" ">
                            <SelectValue placeholder=" Seleccione una acción" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Acciones</SelectLabel>

                                <SelectItem v-for="action in Object.values(assetHistoryActionOptions)"
                                    :key="action.value" :value="action.value">
                                    <Badge :class="action.bg">
                                        <component :is="action.icon" class="size-4" />
                                        {{ action.label }}
                                    </Badge>
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>

                    <Popover>
                        <PopoverTrigger as-child>
                            <Button variant="outline" class="w-full sm:w-52 justify-between font-normal">
                                {{ JSON.stringify(dateRange) !== '{}' && dateRange
                                    ?
                                    `${dateRange?.start?.toDate(getLocalTimeZone()).toLocaleDateString()}
                                ${dateRange?.end?.toDate(getLocalTimeZone()).toLocaleDateString() ? ' - ' : ''}
                                ${dateRange?.end?.toDate(getLocalTimeZone()).toLocaleDateString() || ''}`
                                    : 'Seleccionar rango de fechas' }}


                                <ChevronDownIcon />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">

                            <RangeCalendar v-model="dateRange" class="rounded-md border shadow-sm" :number-of-months="2"
                                disable-days-outside-current-view locale="es" />
                        </PopoverContent>
                    </Popover>
                </div>

                <div class="absolute left-4 top-0 bottom-0 w-px bg-border"></div>
                <div class="space-y-6">
                    <Pagination class="mx-0  w-fit ml-auto!" :items-per-page="historiesPaginated.per_page"
                        :total="historiesPaginated.total" :default-page="historiesPaginated.current_page">
                        <PaginationContent class="flex-wrap">
                            <PaginationPrevious :disabled="isLoading || historiesPaginated.current_page === 1" @click="!isLoading && router.visit(historiesPaginated.prev_page_url || '', {
                                preserveScroll: true,
                                replace: true,
                                preserveState: true,
                                preserveUrl: true,
                                only: ['historiesPaginated']
                            })">

                                <ChevronLeftIcon />
                                Anterior
                            </PaginationPrevious>
                            <template v-for="(item, index) in historiesPaginated.links.filter(link => +link.label)"
                                :key="index">
                                <PaginationItem :value="+item.label" :is-active="item.active"
                                    :disabled="isLoading || item.active" @click="!isLoading && router.visit(item.url, {
                                        preserveScroll: true,
                                        replace: true,
                                        preserveState: true,
                                        preserveUrl: true,
                                        only: ['historiesPaginated']
                                    })">
                                    {{ item.label }}
                                </PaginationItem>
                            </template>

                            <PaginationNext
                                :disabled="isLoading || historiesPaginated.current_page === historiesPaginated.last_page"
                                @click="!isLoading && router.visit(historiesPaginated.next_page_url || '', {
                                    preserveScroll: true,
                                    replace: true,
                                    preserveState: true,
                                    preserveUrl: true,
                                    only: ['historiesPaginated']
                                })" />



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

                    <div v-for="history in historiesPaginated.data" class="relative flex gap-4 pl-10">
                        <div
                            class="absolute left-2 w-5 h-5 rounded-full bg-card border-2 flex items-center justify-center text-info">

                            <component :is="actionOp(history.action).icon" class="size-3" />


                        </div>
                        <div class="flex-1 bg-muted/30 rounded-lg p-3">
                            <div class="flex items-start justify-between">
                                <Badge :class="actionOp(history.action).bg">{{ actionOp(history.action).label }}</Badge>


                                <span class="text-xs text-muted-foreground">{{ format(new Date(history.performed_at),
                                    'yyyy-MM-dd HH:mm') }}</span>
                            </div>

                            <ul v-if="history.description.split(',').length > 1" class="list-disc pl-5">
                                <li class="text-xs text-muted-foreground mt-1"
                                    v-for="desc in history.description.split(',')" :key="desc">{{
                                        desc }}</li>
                            </ul>


                            <template v-else>
                                <p class="text-xs text-muted-foreground mt-1"
                                    v-if="history.action === AssetHistoryAction.STATUS_CHANGED">
                                    <template v-for="(des, i) in history.description.split(`'`)" :key="i">

                                        <Badge v-if="getStatusOpByDes(des)" class="mx-1"
                                            :class="getStatusOpByDes(des)?.bg">
                                            <component :is="getStatusOpByDes(des)?.icon" class="size-4" />
                                            {{
                                                getStatusOpByDes(des)?.label }}
                                        </Badge>
                                        <span v-else>
                                            {{ des }}
                                        </span>
                                    </template>
                                </p>

                                <p v-else class="text-xs text-muted-foreground mt-1">{{ history.description }}
                                </p>
                            </template>

                            <div class="flex items-center mt-2 gap-2"
                                v-if="[AssetHistoryAction.DELIVERY_RECORD_UPLOADED, AssetHistoryAction.INVOICE_UPLOADED].includes(history.action)">

                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger as-child>

                                            <Button class="ml-auto" size="icon" @click="() => {
                                                handleDownloadReceipt(history?.delivery_record?.file_url || history.invoice_url || '')
                                            }">

                                                <DownloadIcon />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Descargar Comprobante</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>

                            <p class="text-xs text-muted-foreground mt-2">Por:

                                <Badge variant="outline">{{ history.performer?.full_name }}</Badge>
                            </p>
                        </div>
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
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useApp } from '@/composables/useApp';
import { assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import { actionOp, AssetHistory, AssetHistoryAction, assetHistoryActionOptions } from '@/interfaces/assetHistory.interface';
import type { Paginated } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { getLocalTimeZone } from '@internationalized/date';
import { format } from 'date-fns';
import { DownloadIcon, History } from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import { computed, ref, watch } from 'vue';

const asset = defineModel<Asset | null>('asset');
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
                asset_id: asset?.value?.id,
                actions: actions.value,
                start_date: dateRange.value?.start ? dateRange.value.start.toDate(getLocalTimeZone()).toISOString().split('T')[0] : null,
                end_date: dateRange.value?.end ? dateRange.value.end.toDate(getLocalTimeZone()).toISOString().split('T')[0] : null,
            },
        });
};


const handleDownloadReceipt = (filePath: string) => {
    window.open(filePath, '_blank');
};

const getStatusOpByDes = (des: string) => {
    return Object.values(assetStatusOptions).find(opt => opt.label.trim().toLowerCase() === des.trim().toLowerCase());
};
</script>