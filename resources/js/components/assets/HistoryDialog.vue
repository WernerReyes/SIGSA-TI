<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            open = false;
            asset = null;
        }
    }">
        <DialogContent class="max-h-screen sm:max-w-5xl">
            <DialogHeader class="space-y-2 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <History class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold leading-tight">Historial de Activo</h2>
                        <p class="text-sm text-muted-foreground">Consulta los movimientos y acciones realizadas sobre
                            este activo.</p>
                        <p v-if="asset"
                            class="text-xs text-muted-foreground mt-1 inline-flex gap-2 items-center bg-muted px-2 py-1 rounded-md">
                            <span class="font-mono">AST-{{ asset?.id }}</span>
                            <span class="text-foreground">·</span>
                            <span class="font-medium line-clamp-1">{{ asset?.name }}</span>
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
                            <Select multiple v-model="actions" class="sm:w-52">
                                <SelectTrigger class="sm:w-52 w-full">
                                    <SelectValue placeholder="Seleccione una acción" />
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

                            <ul v-if="history.description.split(',').length > 1" class="list-disc pl-5 mt-2 space-y-1">
                                <li class="text-xs text-muted-foreground" v-for="desc in history.description.split(',')"
                                    :key="desc">
                                    <template v-for="(part, index) in desc.split(`'`)" :key="index">
                                        <span v-if="index % 2 === 0" class="text-xs text-muted-foreground mt-2">{{ part
                                            }}</span>
                                        <Badge v-else class="mx-1">
                                            <component :is="History" class="size-4" />
                                            {{ part }}
                                        </Badge>
                                    </template>



                                </li>
                            </ul>


                            <template v-else>


                                <template v-if="history.action === AssetHistoryAction.UPDATED"
                                    v-for="(part, index) in history.description.split(`'`)" :key="index">

                                    <span v-if="index % 2 === 0" class="text-xs text-muted-foreground mt-2">{{ part
                                    }}</span>
                                    <Badge v-else class="mx-1">
                                        <component :is="History" class="size-4" />
                                        {{ part }}
                                    </Badge>
                                </template>

                                <template v-else-if="history.action === AssetHistoryAction.STATUS_CHANGED"
                                    v-for="(des, i) in history.description.split(`'`)" :key="i">

                                    <Badge v-if="getStatusOpByDes(des)" class="mx-1" :class="getStatusOpByDes(des)?.bg">
                                        <component :is="getStatusOpByDes(des)?.icon" class="size-4" />
                                        {{
                                            getStatusOpByDes(des)?.label }}
                                    </Badge>
                                    <span v-else class="text-xs text-muted-foreground mt-2">
                                        {{ des }}
                                    </span>
                                </template>

                                <template v-else-if="history.action === AssetHistoryAction.ASSIGNED">
                                    <template v-if="history.description.includes('Asignación cambiada de')">
                                        <template v-for="(part, index) in history.description.split(/\s+de\s+|\s+a\s+/)"
                                            :key="index">
                                            <span v-if="index === 0"
                                                class="text-xs text-muted-foreground mt-2">Asignación
                                                cambiada de </span>
                                            <template v-else>
                                                <span v-if="index % 2 === 0" class="text-xs text-muted-foreground mt-2">
                                                    a
                                                </span>
                                                <Badge class="mx-1">
                                                    <User />
                                                    {{ part }}
                                                </Badge>

                                            </template>
                                        </template>
                                    </template>

                                    <template v-else-if="history.description.includes('con los accesorios')">
                                        <!-- {{history.description.split(' junto con los accesorios:')}} -->
                                        {{parsedAccessories(history.description)}}
                                        <template v-for="(part, index) in history.description.split(' con los accesorios ')">

                                            <span v-if="index === 0" class="text-xs text-muted-foreground mt-2">{{ part
                                            }} con los accesorios </span>
                                            <Badge v-else class="mx-1">
                                                <User />
                                                {{ part }}
                                            </Badge>
                                        </template>
                                    </template>

                                    <template v-else
                                        v-for="(part, index) in history.description.split('Equipo asignado a')">

                                        <span v-if="index === 0" class="text-xs text-muted-foreground mt-2">{{ part
                                        }}Equipo asignado a</span>
                                        <Badge v-else class="mx-1">
                                            <User />
                                            {{ part }}
                                        </Badge>
                                    </template>

                                </template>

                                <template v-else-if="history.action === AssetHistoryAction.RETURNED">
                                    <template v-for="(part, index) in history.description.split(' por ')" :key="index">
                                        <span v-if="index % 2 === 0" class="text-xs text-muted-foreground mt-2">
                                            <template v-if="getReturnReasonByDes(part)">
                                                por
                                                <Badge class="mx-1" variant="secondary">
                                                    <component :is="getReturnReasonByDes(part)?.icon" class="size-4" />
                                                    {{ getReturnReasonByDes(part)?.label }}
                                                </Badge>
                                            </template>
                                            <template v-else>
                                                {{ part
                                                }} por
                                            </template>
                                        </span>
                                        <Badge v-else class="mx-1">
                                            <User />
                                            {{ part }}
                                        </Badge>
                                    </template>

                                </template>


                                <p v-else class="text-xs text-muted-foreground mt-2">{{ history.description }}
                                </p>
                            </template>

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
                                    @click="!isLoading && router.visit(historiesPaginated.prev_page_url || '', {
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
                        </div>




                    </div>
                </div>

        </DialogContent>
    </Dialog>

    <!-- {{ test() }} -->
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
import { DownloadIcon, History, RefreshCcw, User } from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import { returnReasonOptions } from '@/interfaces/assetAssignment.interface';

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
// const parsedHistory = computed(() => {
//   const text = history.description

//   const separator = ' junto con los accesorios:'
//   if (!text.includes(separator)) {
//     return null
//   }

//   const [base, accessoriesRaw] = text.split(separator)

//   const accessories = accessoriesRaw
//     ?.split(',')
//     .map(a => a.trim())
//     .filter(Boolean)

//   return {
//     base,
//     accessories,
//   }
// })

const parsedAccessories = (description: string) => {
    const separator = ' junto con los accesorios:'
    if (!description.includes(separator)) {
        return null
    }

    const [base, accessoriesRaw] = description.split(separator)
    const [,assignmentTo] = description.split(' asignado a ')


    const accessories = accessoriesRaw
        ?.split(',')
        .map(a => a.trim())
        .filter(Boolean)

    return {
        base,
        accessories,
    }
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
                asset_id: asset?.value?.id,
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


const handleDownloadReceipt = (filePath: string) => {
    window.open(filePath, '_blank');
};

const getStatusOpByDes = (des: string) => {
    return Object.values(assetStatusOptions).find(opt => opt.label.trim().toLowerCase() === des.trim().toLowerCase());
};

const getReturnReasonByDes = (des: string) => {
    return Object.values(returnReasonOptions).find(opt => opt.label.trim().toLowerCase() === des.trim().toLowerCase());
};

// const test = () => {
// const texto = "Nombre cambiado de 'Otro mas7' a 'Otro a mas7e'";
// const resultado = texto?.match(/'([^']+)'/g)?.map(s => s.replace(/'/g, ""));
// console.log(resultado); // ["Otro mas7", "Otro a mas7e"]
// };

const getUpdatedNames = (description: string) => {
    return description?.match(/'([^']+)'/g)?.map(s => s.replace(/'/g, ""));
};

</script>