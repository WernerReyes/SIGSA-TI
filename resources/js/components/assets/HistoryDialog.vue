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
                                    <Badge :class="action.bg">{{ action.label }}</Badge>
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>

                    <Popover v-slot="{ close }">
                        <PopoverTrigger as-child>
                            <Button variant="outline" class="w-full sm:w-52 justify-between font-normal">

                                {{ Object.keys(dateRange || {}).length > 0
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
                                disable-days-outside-current-view />
                        </PopoverContent>
                    </Popover>
                </div>

                <div class="absolute left-4 top-0 bottom-0 w-px bg-border"></div>
                <div class="space-y-6">


                    <div v-if="histories.length === 0" class="text-center text-sm text-muted-foreground py-10">
                        No hay historial para mostrar.
                    </div>
                    <div v-for="history in histories" class="relative flex gap-4 pl-10">
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
                                    v-for="desc in history.description.split(',')" :key="desc">{{ desc }}</li>
                            </ul>


                            <template v-else>
                                <p class="text-xs text-muted-foreground mt-1"
                                    v-if="history.action === AssetHistoryAction.STATUS_CHANGED">
                                    <template v-for="(des, i) in history.description.split(`'`)" :key="i">

                                        <Badge v-if="getStatusOpByDes(des)" class="mx-1"
                                            :class="getStatusOpByDes(des)?.bg">{{
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
                                v-if="history.action === AssetHistoryAction.DELIVERY_RECORD_UPLOADED">

                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button class="ml-auto" size="icon">
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
import { AssetStatus, assetStatusOptions, type Asset } from '@/interfaces/asset.interface';
import { actionOp, AssetHistoryAction, assetHistoryActionOptions } from '@/interfaces/assetHistory.interface';
import { DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { endOfDay, format, isWithinInterval, startOfDay } from 'date-fns';
import { DownloadIcon } from 'lucide-vue-next';
import type { DateRange } from 'reka-ui';
import { computed, Ref, ref } from 'vue';

const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');

const actions = ref<Array<AssetHistoryAction>>([]);

const dateRange = ref<DateRange | undefined>(undefined);


const histories = computed(() => {
    let filtered = asset?.value?.histories || [];
    if (actions.value.length > 0) {
        filtered = filtered.filter((history) => actions.value.includes(history.action));
    }

    if (dateRange.value && dateRange.value.start && dateRange.value.end) {
        filtered = filtered.filter((history) => {
            const historyDate = new Date(history.performed_at);
            const startDate = dateRange.value?.start?.toDate(getLocalTimeZone());
            const endDate = dateRange.value?.end?.toDate(getLocalTimeZone());
            return isWithinInterval(historyDate, { start: startOfDay(startDate), end: endOfDay(endDate) });
        });
    }

    return filtered;
});


const getStatusOpByDes = (des: string) => {
    return Object.values(assetStatusOptions).find(opt => opt.label.trim().toLowerCase() === des.trim().toLowerCase());
};
</script>