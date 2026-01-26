<template>
    <div class="flex items-center gap-2">
        <template v-if="isSecret">
            <EyeOff v-if="!eye" class="size-4 text-muted-foreground cursor-pointer hover:text-foreground" @click="eye = true" />
            <Eye v-else class="size-4 text-muted-foreground cursor-pointer hover:text-foreground" @click="eye = false" />
            <span class="font-mono text-sm">{{ eye ? label : '•••••' }}</span>
        </template>
        <span v-else class="font-mono text-sm">{{ label }}</span>
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
import { Copy, EyeOff, Eye } from 'lucide-vue-next';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { ref } from 'vue';
defineProps<{
    label: string;
    isSecret?: boolean;
}>();
const { copy, copied } = useClipboard();

const eye = ref(false);
</script>