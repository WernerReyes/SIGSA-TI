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
                        <!-- <History class="size-6 text-primary" /> -->

                        <component :is="assetTypeOp(asset?.type?.name).icon" class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold leading-tight">Historial de {{ asset?.type?.name }}</h2>
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


            <ScrollArea class="dialog-content">
                <div class="relative space-y-6">

                    <div class="sticky top-0 z-10 bg-background/90 backdrop-blur border-b border-border/80 pt-4 pb-3">
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <Badge variant="outline">Filtros</Badge>
                                <Badge v-if="actions.length" class="cursor-pointer" variant="secondary"
                                    @click="actions = []">
                                    {{ actions.length }} acción(es)
                                    <XCircle />
                                </Badge>

                                <Badge v-if="dateRange" class="cursor-pointer" variant="secondary"
                                    @click="dateRange = undefined">
                                    Rango aplicado
                                    <XCircle />
                                </Badge>
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
                                                    ? ' - ' :
                                                    ''}${dateRange?.end?.toDate(getLocalTimeZone()).toLocaleDateString()
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
                                    <Badge :class="actionOp(history.action).bg">{{ actionOp(history.action).label }}
                                    </Badge>


                                    <span class="text-xs text-muted-foreground">{{ format(new
                                        Date(history.performed_at),
                                        'dd/MM/yyyy HH:mm') }}</span>
                                </div>

                                <template v-if="history.action === AssetHistoryAction.UPDATED">
                                    <ul v-if="history.description.split(',').length > 1"
                                        class="list-disc pl-5 mt-2 space-y-1">
                                        <li class="text-xs text-muted-foreground"
                                            v-for="desc in history.description.split(',')" :key="desc">
                                            <template v-for="(part, index) in parsedUpdateAction(desc)" :key="index">
                                                <span v-if="part.type === 'text'"
                                                    class="text-xs text-muted-foreground mt-2">{{
                                                        part.content }}</span>
                                                <Badge v-else class="mx-1" variant="outline">
                                                    <Pencil />
                                                    {{ part.content }}
                                                </Badge>
                                            </template>
                                        </li>
                                    </ul>
                                    <template v-else v-for="(part, index) in parsedUpdateAction(history.description)"
                                        :key="index">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>
                                        <Badge v-else class="mx-1" variant="outline">
                                            <Pencil />
                                            {{ part.content }}
                                        </Badge>
                                    </template>
                                </template>


                                <template v-else-if="history.action === AssetHistoryAction.STATUS_CHANGED"
                                    v-for="(part, index) in parsedStatusChange(history.description)">
                                    <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                        part.content }}</span>
                                    <Badge v-else :class="part.bg" class="mx-1">
                                        <component :is="part.icon" class="size-4" />
                                        {{ part.label }}
                                    </Badge>
                                </template>

                                <!-- TODO: Some cases haven't been parsed yet -->
                                <template v-else-if="history.action === AssetHistoryAction.ASSIGNED">
                                    <template v-if="includes(history.description, ['junto con los accesorios'])"
                                        v-for="(part, index) in parsedAssignmentWithAccessories(history.description)"
                                        :key="index">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>

                                        <Badge v-else-if="part.type === 'badge'" class="mx-1">
                                            <component :is="part.icon" class="size-4" />
                                            {{ part.content }}
                                        </Badge>


                                        <ul v-else-if="part.type === 'list'"
                                            class="block flex-wrap gap-2 list-disc pl-5 mt-2">
                                            <li v-for="(acc, accIndex) in part.list" :key="accIndex"
                                                class="text-xs text-muted-foreground">
                                                {{ acc }}
                                            </li>
                                        </ul>

                                    </template>

                                    <template v-else-if="includes(history.description, ['junto al equipo principal'])">
                                        <template
                                            v-for="(part, index) in parsedAssignmentWithMainParent(history.description)"
                                            :key="index">
                                            <span v-if="part.type === 'text'"
                                                class="text-xs text-muted-foreground mt-2">{{
                                                    part.content }}</span>
                                            <Badge v-else class="mx-1" :variant="part.variant">
                                                <component :is="part.icon" class="size-4" />
                                                {{ part.content }}
                                            </Badge>

                                        </template>
                                    </template>

                                    <template v-else-if="includes(history.description, ['liberado', 'asignado', ','])">
                                        <ul class="list-disc pl-5 mt-2 space-y-1">
                                            <li v-for="(part) in history.description.split(',')"
                                                class="text-xs text-muted-foreground">
                                                {{ part }}
                                            </li>
                                        </ul>
                                    </template>

                                    <template v-else-if="includes(history.description, [','])">
                                        <ul class="list-disc pl-5 mt-2 space-y-1">
                                            <li v-for="(part) in history.description.split(',')"
                                                class="text-xs text-muted-foreground">
                                                {{ part }}
                                            </li>
                                        </ul>
                                    </template>


                                    <template v-else v-for="(part) in parsedAssignmentChange(history.description)">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>
                                        <Badge v-else class="mx-1">
                                            <User />
                                            {{ part.content }}
                                        </Badge>

                                    </template>


                                </template>

                                <template v-else-if="history.action === AssetHistoryAction.RETURNED">
                                    <template v-if="includes(history.description, ['junto con los accesorios'])"
                                        v-for="(part, index) in parsedReturnWithAccessories(history.description)"
                                        :key="index">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>

                                        <Badge v-else-if="part.type === 'badge'" class="mx-1" :variant="part.variant">
                                            <component :is="part.icon" class="size-4" />
                                            {{ part.content }}
                                        </Badge>

                                        <ul v-else-if="part.type === 'list'"
                                            class="block flex-wrap gap-2 list-disc pl-5 mt-2">
                                            <li v-for="(acc, accIndex) in part.list" :key="accIndex"
                                                class="text-xs text-muted-foreground">
                                                {{ acc }}
                                            </li>
                                        </ul>

                                    </template>
                                    <template v-else-if="includes(history.description, ['junto al equipo principal'])"
                                        v-for="(part, index) in parsedReturnWithMainParent(history.description)">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>
                                        <Badge v-else :variant="part.variant" class="mx-1">
                                            <component :is="part.icon" class="size-4" />
                                            {{ part.content }}
                                        </Badge>
                                    </template>


                                    <template v-else v-for="(part, index) in parsedReturnChange(history.description)">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>
                                        <Badge v-else :variant="part.variant" class="mx-1">
                                            <component :is="part.icon" class="size-4" />
                                            {{ part.content }}
                                        </Badge>
                                    </template>
                                </template>

                                <template v-else-if="history.action === AssetHistoryAction.DELIVERY_RECORD_UPLOADED">
                                    <template v-for="(part, index) in parsedDeliveryRecordUpload(history.description)"
                                        :key="index">
                                        <span v-if="part.type === 'text'" class="text-xs text-muted-foreground mt-2">{{
                                            part.content }}</span>
                                        <Badge v-else class="mx-1">
                                            <User />
                                            {{ part.content }}
                                        </Badge>
                                    </template>
                                </template>


                                <p v-else class="text-xs text-muted-foreground mt-2">{{ history.description }}
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


                    </div>
                </div>
            </ScrollArea>

            <DialogFooter>
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
                                    :disabled="isLoading || item.active" @click="!isLoading && changePage(item.url)">
                                    {{ item.label }}
                                </PaginationItem>
                            </template>

                            <PaginationNext
                                :disabled="isLoading || historiesPaginated.current_page === historiesPaginated.last_page"
                                @click="!isLoading && changePage(historiesPaginated.next_page_url)" />
                        </PaginationContent>
                    </Pagination>
                </div>
            </DialogFooter>

        </DialogContent>

    </Dialog>


</template>

<script setup lang="ts">

import {
    Dialog,
    DialogContent,
    DialogFooter,
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

import SelectFilters from '@/components/SelectFilters.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useApp } from '@/composables/useApp';
import { AssetStatusOption, assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import { returnReasonOptions } from '@/interfaces/assetAssignment.interface';
import { actionOp, AssetHistory, AssetHistoryAction, assetHistoryActionOptions } from '@/interfaces/assetHistory.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import type { Paginated, Variant } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { getLocalTimeZone } from '@internationalized/date';
import { format } from 'date-fns';
import { DownloadIcon, History, MonitorSmartphone, Pencil, RefreshCcw, User, XCircle } from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import { computed, ref, watch, type Component } from 'vue';

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



const parsedUpdateAction = (description: string) => {
    const parts = description.split(`'`);
    const parsed = parts.map((part, index) => {
        if (index % 2 === 0) {
            return { type: 'text', content: part };
        } else {
            return { type: 'badge', content: part, };
        }
    });
    if (parsed.length !== 5) {
        return [{ type: 'text', content: description }];
    }

    return parsed;

};

const parsedStatusChange = (description: string): Array<(Partial<AssetStatusOption> & {
    type: 'badge' | 'text';
    content?: string;
})> => {
    const parts = description.split(`'`);
    const parsed = parts.map((part) => {
        const statusOpt = Object.values(assetStatusOptions).find(opt => opt.label.trim().toLowerCase() === part.trim().toLowerCase());
        if (statusOpt) {
            return { type: 'badge' as const, ...statusOpt };
        } else {
            return { type: 'text' as const, content: part };
        }
    });
    if (parsed.length !== 5) {
        return [{ type: 'text' as const, content: description }];
    }

    return parsed as Array<(Partial<AssetStatusOption> & {
        type: 'badge' | 'text';
        content?: string;
    })>;

};

const parsedAssignmentChange = (description: string, type = 'Equipo'): any => {
    const separator = `${type} asignado a `;
    const parts = description.split(separator);
    const parsed = parts.map((part, index) => {
        if (index % 2 === 0) {
            return { type: 'text', content: separator };
        } else {
            return { type: 'badge', content: part, icon: User };
        }
    });
    if (parsed.length !== 2) {
        return [{ type: 'text', content: description }];
    }

    return parsed;

};

const parsedAssignmentWithAccessories = (description: string): Array<{
    type: 'badge' | 'text' | 'list';
    content?: string;
    list?: string[];
    // label?: string;
    // bg?: string;
    icon?: Component;
}> => {
    const [basePart, accessoriesPart] = description.split(' junto con los accesorios:');
    // const [,assignmentTo] = basePart.split('Equipo asignado a ');
    const baseParsed = parsedAssignmentChange(basePart);
    const accessories = accessoriesPart
        ?.split(',')
        .map(a => a.trim())
        .filter(Boolean)

    const parsedAccessories = [
        ...baseParsed,
        { type: 'text' as const, content: ' junto con los accesorios: ' },
        {
            type: 'list' as const,
            list: accessories
        }
    ];

    if (parsedAccessories.length !== 4) {
        return [{ type: 'text' as const, content: description }];
    }

    return parsedAccessories;
};

const parsedAssignmentWithMainParent = (description: string): Array<{
    type: 'badge' | 'text';
    content?: string;
    label?: string;
    variant?: Variant;
    icon?: Component;
}> => {
    const separator = ' junto al equipo principal ';
    const [basePart, mainParentPart] = description.split(separator);


    const baseParsed = parsedAssignmentChange(basePart, 'Accesorio');
    const mainParent = mainParentPart
        ?.replace('(', '')
        .replace(')', '')
        .trim();
    const parsedMainParent = [
        ...baseParsed,
        { type: 'text' as const, content: separator },
        {
            type: 'badge' as const,
            content: mainParent,
            variant: 'secondary',
            icon: MonitorSmartphone,
        }
    ];



    if (parsedMainParent.length !== 4) {
        return [{ type: 'text' as const, content: description }];
    }

    return parsedMainParent;

};

const parsedReturnWithAccessories = (description: string): Array<{
    type: 'badge' | 'text' | 'list';
    content?: string;
    list?: string[];
    variant?: Variant;
    icon?: Component;
}> => {
    const [basePart, returnBy, rest] = description.split(' por');
    // const [,assignmentTo] = basePart.split('Equipo asignado a ');
    const baseParsed = parsedAssignmentChange(basePart);
    const [topic, accessoriesPart] = rest.split('junto con los accesorios:');
    const accessories = accessoriesPart
        ?.split(',')
        .map(a => a.trim())
        .filter(Boolean)


    const op = Object.values(returnReasonOptions).find(opt => opt.label.trim().toLowerCase() === topic.trim().toLowerCase());


    const parsedAccessories = [
        ...baseParsed,
        { type: 'text' as const, content: ' por' },
        { type: 'badge' as const, content: returnBy, icon: User },
        { type: 'text' as const, content: ' por' },
        { type: 'badge' as const, content: topic, icon: op?.icon, variant: 'secondary' },
        { type: 'text' as const, content: ' junto con los accesorios: ' },
        {
            type: 'list' as const,
            list: accessories
        }
    ];

    if (parsedAccessories.length !== 7) {
        return [{ type: 'text' as const, content: description }];
    }

    return parsedAccessories;
};



const parsedReturnWithMainParent = (description: string): Array<{
    type: 'badge' | 'text';
    content?: string;
    label?: string;
    variant?: Variant;
    icon?: Component;
}> => {
    const separator = ' junto al equipo principal ';
    const [basePart, restPart] = description.split(separator);
    const baseParsed = parsedAssignmentChange(basePart, 'Accesorio');
    const [mainParentPart, topic] = restPart.split(' por');
    const mainParent = mainParentPart
        ?.replace('(', '')
        .replace(')', '')
        .trim();

    const op = Object.values(returnReasonOptions).find(opt => opt.label.trim().toLowerCase() === topic.trim().toLowerCase());

    const parsedMainParent = [
        ...baseParsed,
        { type: 'text' as const, content: separator },
        {
            type: 'badge' as const,
            content: mainParent,
            variant: 'secondary',
            icon: MonitorSmartphone,
        }
        ,
        { type: 'text' as const, content: ' por' },
        { type: 'badge' as const, content: topic, icon: op?.icon, variant: 'secondary' },
    ];
    if (parsedMainParent.length !== 5) {
        return [{ type: 'text' as const, content: description }];
    }
    return parsedMainParent;
};

const parsedReturnChange = (description: string): Array<{
    type: 'badge' | 'text';
    content?: string;
    label?: string;
    variant?: Variant;
    icon?: Component;
}> => {
    const separator = ' por ';
    const [basePart, returnByPart] = description.split(separator);
    const baseParsed = parsedAssignmentChange(basePart, 'Equipo');

    const parsedReturnBy = [
        ...baseParsed,
        { type: 'text' as const, content: separator },
        {
            type: 'badge' as const,
            content: returnByPart,
            icon: User,
        }
    ];

    if (parsedReturnBy.length !== 3) {
        return [{ type: 'text' as const, content: description }];
    }

    return parsedReturnBy;

};

const parsedDeliveryRecordUpload = (description: string): Array<{
    type: 'badge' | 'text';
    content?: string;
}> => {
    const parts = description.split(`'`);
    const parsed = parts.map((part, index) => {
        if (index % 2 === 0) {
            return { type: 'text', content: part };
        } else {
            return { type: 'badge', content: part, };
        }
    });
    if (parsed.length !== 3) {
        return [{ type: 'text', content: description }];
    }

    return parsed as Array<{
        type: 'badge' | 'text';
        content?: string;
    }>;

};


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