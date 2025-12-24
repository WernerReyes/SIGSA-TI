<template>

    <!-- <td class="py-4 px-4"> -->
    <div class="flex flex-col"><span class="font-mono text-sm text-primary font-medium">TK-{{ ticket.id }}</span><span
            class="font-medium text-sm mt-1">{{
                ticket.title
            }}</span><span class="text-xs text-muted-foreground mt-1">{{
                format(new Date(ticket.created_at), 'PPP', {
                    locale: es
                })
            }}</span>
        <div class="flex items-center gap-3 mt-2 text-muted-foreground"><span
                class="flex items-center gap-1 text-xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-message-square w-3 h-3">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg> 5</span><span class="flex items-center gap-1 text-xs"><svg xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paperclip w-3 h-3">
                    <path
                        d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48">
                    </path>
                </svg> 2</span></div>
    </div>
    <!-- </td> -->
</template>

<script setup lang="ts">
import { type Ticket } from '@/interfaces/ticket.interface';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

defineProps<{
    ticket: Ticket;
}>();
</script>