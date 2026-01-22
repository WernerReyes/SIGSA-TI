<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button variant="outline" role="combobox" :aria-expanded="open" class="w-fit justify-between gap-2">
                <span v-if="selecteds.length && showSelectedFocus" class="relative flex size-2">
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                    <span class="relative inline-flex size-2 rounded-full bg-sky-500"></span>
                </span>
                    {{selecteds}}
                <component :is="icon" v-if="icon" class="size-4" />
                {{ selectedAsLabel && selecteds.length
                    ? getLabel(computedItems.find(i => getValue(i) === selecteds[0])!)
                    : label }}
                <!-- {{ label }} -->
                <ChevronsUpDown class="h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-full p-0">
            <Command>
                <CommandInput :placeholder="searchPlaceholder" />

                <CommandShortcut v-if="showRefresh"  @click="() => {
                    if (isLoading || !selecteds.length) return;
                    clearSelection();
                }"
                    class="flex w-full items-center justify-center gap-2 p-2" :class="disabledClass">
                    Refrescar lista
                    <RefreshCcw class="size-4" :class="isLoading ? 'animate-spin' : ''" />
                </CommandShortcut>

                <CommandList>
                    <!-- 🔥 SOPORTE WhenVisible -->
                    <WhenVisible v-if="dataKey" :data="dataKey">
                        <template #fallback>
                            <CommandGroup>
                                <CommandItem v-for="n in skeletonCount" :key="n" :value="`skeleton-${n}`">
                                    <Skeleton class="h-4 w-full" />
                                </CommandItem>
                            </CommandGroup>
                        </template>

                        <CommandEmpty>{{ emptyText }}</CommandEmpty>

                        <CommandGroup>
                            <CommandItem v-for="item in computedItems" :key="getValue(item)" :value="getValue(item)"
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
                            <CommandItem v-for="item in computedItems" :key="getValue(item)" :value="getValue(item)"
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

<script setup lang="ts" generic="T">
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

/* ───── Props ───── */

const props = withDefaults(
    defineProps<{
        items: T[]
        label?: string
        itemValue: keyof T
        itemLabel: keyof T
        dataKey?: string
        multiple?: boolean
        allowNull?: boolean
        nullLabel?: string
        showRefresh?: boolean
        showSelectedFocus?: boolean
        skeletonCount?: number
        icon?: Component
        selectedAsLabel?: boolean
        defaultSelecteds: (string | number | null)[]
    }>(),
    {
        label: 'Seleccionar',
        multiple: false,
        allowNull: false,
        nullLabel: 'Sin asignar',
        showRefresh: true,
        showSelectedFocus: true,
        skeletonCount: 5,
        selectedAsLabel: false,
        // defaultSelecteds: []
        
    }
)


const emit = defineEmits<{
    (e: 'select', value: (string | number | null)[] | (string | number | null)): void
}>()



/* ───── Model ───── */
const selecteds = defineModel<(string | number | null)[]>('selecteds', {
    default: () => []
})

// watch(
//     () => props.defaultSelecteds,
//     (newVal) => {
//         if (!newVal) return;
//         console.log('Default selecteds changed:', newVal);
//         selecteds.value = newVal
//     },
//     { immediate: true }
// )

/* ───── State ───── */
const open = ref(false)
const { isLoading } = useApp()

/* ───── Computed ───── */
const computedItems = computed(() => {
    if (!props.allowNull) return props.items

    return [
        { [props.itemValue]: null, [props.itemLabel]: props.nullLabel ?? 'Sin asignar' } as T,
        ...props.items
    ]
})

const disabledClass = computed(() =>
    isLoading.value || !selecteds.value.length
        ? 'cursor-not-allowed opacity-50'
        : 'cursor-pointer'
)

const searchPlaceholder = `Buscar ${props.label?.toLowerCase() ?? 'opciones'}`
const emptyText = `${props.label ?? 'Items'} no encontrados`

/* ───── Helpers ───── */
const getValue = (item: T) => item[props.itemValue] as any
const getLabel = (item: T) => item[props.itemLabel] as any

const toggle = (item: T) => {    
    const value = getValue(item)

    console.log('Toggling value:', value);

    if (!props.multiple) {
        selecteds.value = [value]
        emit('select', value)
        open.value = false
        return
    }

    selecteds.value = selecteds.value.includes(value)
        ? selecteds.value.filter(v => v !== value)
        : [...selecteds.value, value]


        console.log('Selecteds:', selecteds.value);
    emit('select', selecteds.value)
}

const clearSelection = () => (selecteds.value = [])
</script>
