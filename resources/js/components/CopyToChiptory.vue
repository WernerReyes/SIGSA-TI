<template>
    <div class="flex items-center gap-2">
        <span class="font-mono text-sm">{{ label }}</span>
        <TooltipProvider>
            <Tooltip :open="copied ? true : undefined">
                <TooltipTrigger as-child>
                    <button
                        :class="`transition-all ${copied ? 'text-emerald-600' : 'text-muted-foreground hover:text-foreground'}`"
                        @click="copy(label)">
                        <Copy class="size-4" />
                    </button>
                </TooltipTrigger>
                <TooltipContent>
                    <p>{{ copied ? '¡Copiado!' : 'Copiar' }}</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>
</template>

<script setup lang="ts">
import { useClipboard } from '@vueuse/core';
import { Copy } from 'lucide-vue-next';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'
defineProps<{
    label: string;
}>();
const { copy, copied } = useClipboard();
</script>