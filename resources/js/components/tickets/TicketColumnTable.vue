<template>
    <div class="flex flex-col w-56 rounded-xl  shadow-sm gap-2">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary">
                <Ticket class="h-4 w-4" />
            </span>
            <span class="font-mono text-xs font-semibold uppercase tracking-wide text-primary">
                TK-{{ ticket.id }}
            </span>
        </div>

        <div class="space-y-1">
            <p class="text-sm font-semibold leading-snug break-wrap-break-word whitespace-normal line-clamp-3">
                {{ ticket.title }}
            </p>
            <p class="text-xs text-muted-foreground leading-snug wrap-break-word whitespace-normal line-clamp-3">
                {{ ticket.description }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { type Ticket as ITicket } from '@/interfaces/ticket.interface';
import { Ticket } from 'lucide-vue-next';

defineProps<{
    ticket: ITicket;
}>();
</script>