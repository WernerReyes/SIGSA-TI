<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button 
             :disabled="disabled"
            variant="outline" role="combobox" :aria-expanded="open" class="justify-between gap-2" :class="fullWidth ? 'w-full' : 'w-fit'">
                <span v-if="selecteds.length && showSelectedFocus" class="relative flex size-2">
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                    <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                </span>
                <component :is="icon" v-if="icon" class="size-4" />

                {{ itemsLabel }}
                <ChevronsUpDown class="h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-full p-0">
            <Command :disabled="disabled">
                <CommandInput :placeholder="searchPlaceholder" />

                <CommandShortcut v-if="showRefresh" @click="() => {
                    if (isLoading || !selecteds.length) return;
                    clearSelection();
                }" class="flex w-full items-center justify-center gap-2 p-2" :class="disabledClass">
                    Refrescar lista
                    <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />
                </CommandShortcut>

                <CommandList class="max-h-60">
                    <!-- SOPORTE WhenVisible -->
                    <WhenVisible v-if="dataKey" :data="dataKey" :params="params ? {
                        preserveUrl: true,

                            data: params
                    } : undefined" :always="always">
                        <template #fallback>
                            <!-- <CommandGroup> -->
                                <!-- <CommandItem v-for="n in skeletonCount" :key="n" :value="`skeleton-${n}`">  -->
                                    <div class="w-full space-y-2 py-2 px-3">

                                        <Skeleton class="h-4 w-full" v-for="n in skeletonCount" :key="n" />
                                    </div>
                                <!-- </CommandItem>                          -->
                            <!-- </CommandGroup> -->
                        </template>

                        <CommandEmpty >{{ emptyText }}</CommandEmpty>
                         
                        <CommandGroup>
                            <CommandItem v-if="computedItems.length === 0" disabled value="no-results">
                                {{ emptyText }}
                            </CommandItem>
                            <CommandItem v-else v-for="(item, i) in computedItems" :key="i" :value="getValue(item)"
                                @select="toggle(item)">
                                <slot name="item" :item="item">
                                    {{ getLabel(item) }}
                                </slot>
                                <Check v-if="selecteds.includes(getValue(item))" class="ml-auto size-4" />
                            </CommandItem>
                        </CommandGroup>
                    </WhenVisible>

                    <!-- 🧠 MODO NORMAL (sin Inertia) -->
                    <template v-else>
                        <CommandEmpty>{{ emptyText }}</CommandEmpty>

                        <CommandGroup>
                            <CommandItem v-if="computedItems.length === 0" disabled value="no-results">
                                {{ emptyText }}
                            </CommandItem>
                            <CommandItem v-else v-for="(item, i) in computedItems" :key="i" :value="getValue(item)"
                                @select="toggle(item)">
                                <slot name="item" :item="item">
                                    {{ getLabel(item) }}
                                </slot>
                                <Check v-if="selecteds.includes(getValue(item))" class="ml-auto size-4" />
                            </CommandItem>
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>


<script setup lang="ts" generic="T, K extends keyof T, N extends boolean   = false, M extends boolean = false">
// import { ref, computed, type Component } from 'vue'
import { ref, computed, type Component, watch } from 'vue'
import { Check, ChevronsUpDown, RefreshCcw } from 'lucide-vue-next'
import { WhenVisible } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import { Button } from '@/components/ui/button'
import {
    Popover,
    PopoverContent,
    PopoverTrigger
} from '@/components/ui/popover'
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandShortcut
} from '@/components/ui/command'
import { useApp } from '@/composables/useApp'

const { isLoading } = useApp()

/* ───── Props ───── */
const props = withDefaults(
    defineProps<{
        items: T[]
        label?: string
        itemValue: K
        itemLabel: keyof T
        dataKey?: string
        multiple?: M
        allowNull?: N
        nullLabel?: string
        showRefresh?: boolean
        showSelectedFocus?: boolean
        skeletonCount?: number
        icon?: Component
        selectedAsLabel?: boolean
        defaultValue?: SelectValue
        maxLabelLength?: number
        filterPlaceholder?: string
        emptyText?: string
        fullWidth?: boolean
        disabled?: boolean
        always?: boolean
        params?: Record<string, any>
    }>(),
    {
        label: 'Seleccionar',
        multiple: false,
        allowNull: false,
        always: false,
        nullLabel: 'Sin asignar',
        showRefresh: true,
        showSelectedFocus: true,
        skeletonCount: 5,
        selectedAsLabel: false,
        fullWidth: false,
        maxLabelLength: 3,
        disabled: false,

    }
)

/* ───── Types ───── */
type V = N extends true ? T[K] | null : T[K]

type SelectValue = M extends true ? V[] : V
/* ───── Emits ───── */
const emit = defineEmits<{
    (e: 'select', value: SelectValue): void
}>()

/* ───── State ───── */
const open = ref(false)
const selecteds = ref<V[]>([])


/* ───── Computed ───── */
const computedItems = computed<T[]>(() => {
    if (!props.allowNull) return props.items

    return [
        {
            [props.itemValue]: null,
            [props.itemLabel]: props.nullLabel,
        } as T,
        ...props.items,
    ]
})

const itemsLabel = computed(() => {
    const labels = computedItems.value
        .filter(i => selecteds.value.includes(getValue(i)))
        .map(i => getLabel(i));

    return props.selectedAsLabel && selecteds.value.length
        ? labels.length
            ? labels.length > props.maxLabelLength
                ? labels.slice(0, props.maxLabelLength).join(', ') + ` +${labels.length - props.maxLabelLength}`
                : labels.join(', ')
            : props.label
        : props.label
    // return computedItems.value.map(i => String(i[props.itemLabel]))
}
)

const disabledClass = computed(() =>
    isLoading.value || !selecteds.value.length
        ? 'cursor-not-allowed opacity-50'
        : 'cursor-pointer'
)

const searchPlaceholder = computed(() =>
    props.filterPlaceholder ?? `Buscar ${props.label?.toLowerCase() ?? 'opciones'}`
)
const emptyText = computed(() =>
    props.emptyText ?? `${props.label ?? 'Items'} no encontrados`
)


watch(
    () => props.defaultValue,
    (newVal) => {
        if (newVal === undefined) return

        if (Array.isArray(newVal)) {
            selecteds.value = newVal as V[]
        } else {
            selecteds.value = [newVal as V]
        }
    },
    { immediate: true }
)


/* ───── Helpers ───── */
const getValue = (item: T): V => item[props.itemValue]
const getLabel = (item: T): string => String(item?.[props.itemLabel])

/* ───── Actions ───── */
const toggle = (item: T) => {
    const value = getValue(item)



    if (!props.multiple) {
        selecteds.value = [value]
        emit('select', value as SelectValue)
        open.value = false
        return
    }

    selecteds.value = selecteds.value.includes(value)
        ? selecteds.value.filter(v => v !== value)
        : [...selecteds.value, value]




    emit('select', selecteds.value as SelectValue)
}

const clearSelection = () => {
    selecteds.value = []
    emit('select', props.multiple ? [] : null)
}
</script>