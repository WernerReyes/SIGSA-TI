<template>
    <Dialog v-if="type === 'dialog'" v-model:open="open" @update:open="
        if (!open) {
            $emit('close');
        }
    ">
        <DialogContent class="sm:max-w-11/12 flex items-center justify-center bg-transparent dialog-content border-0 h-fit">
         
             <Carousel class="w-full">
        <CarouselContent>
            <CarouselItem v-for="(item, i) in items" :key="i">
                <div class="h-full w-full">
                    <slot name="item" :item="item">
                        <Card>
                            <CardContent class="flex aspect-square items-center justify-center p-6">
                                <span class="text-4xl font-semibold">{{ i }}</span>
                            </CardContent>
                        </Card>
                    </slot>

                </div>
            </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
    </Carousel>
        </DialogContent>
    </Dialog>


    <Carousel v-else class="w-full max-w-xs">
        <CarouselContent>
            <CarouselItem v-for="(item, i) in items" :key="i">
                <div class="p-1">
                    <slot name="item" :item="item">
                        <Card>
                            <CardContent class="flex aspect-square items-center justify-center p-6">
                                <span class="text-4xl font-semibold">{{ item }}</span>
                            </CardContent>
                        </Card>
                    </slot>

                </div>
            </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
    </Carousel>
</template>

<script setup lang="ts" generic="T">
import { Card, CardContent } from '@/components/ui/card'
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel'
import { Dialog, DialogContent } from './ui/dialog';
import { ref, watch } from 'vue';

const { type, items } = defineProps<{
    type: 'default' | 'dialog';
    items: T[]
}>()



defineEmits<{
    (e: 'close'): void;
}>()
const open = ref(false);

watch(() => items, (items) => {
    if (type === 'dialog' && items.length) {
        open.value = true;
    } else {
        open.value = false;
    }
}, { immediate: true });


</script>
