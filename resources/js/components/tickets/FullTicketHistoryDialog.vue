<template>
    <Dialog v-model:open="openHistory">

        <DialogScrollContent class="max-w-2xl">

            <DialogHeader>
                <DialogTitle>Historial del Ticket</DialogTitle>
                <DialogDescription>
                    Seguimiento completo de acciones
                </DialogDescription>
            </DialogHeader>

            <!-- Timeline -->
            <div class="relative mt-6 space-y-6">
                <div v-for="(item, index) in histories" :key="index" class="relative flex gap-4">
                    <!-- Línea -->
                    <div class="flex flex-col items-center">
                        <div class="h-4 w-4 rounded-full bg-primary" />
                        <div v-if="index !== histories.length - 1" class="w-px flex-1 bg-muted" />
                    </div>

                    <!-- Card -->
                    <Card class="w-full">
                        <CardContent class="space-y-1">
                            <div class="flex flex-col">
                                <p class="font-semibold">
                                    {{ item.action }}
                                </p>
                                <span class="text-xs text-muted-foreground">
                                    {{ format(new Date(item.performed_at), 'PPP p', {
                                        locale: es
                                    }) }}
                                </span>
                            </div>

                            <Badge variant="secondary" class="mt-2">
                                {{ item.performer?.full_name }}
                            </Badge>
                        </CardContent>
                    </Card>
                </div>
            </div>


        </DialogScrollContent>
    </Dialog>


</template>

<script setup lang="ts">

import {
    Dialog,
    DialogDescription,
    DialogHeader,
    DialogScrollContent,
    DialogTitle
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';

import { Card, CardContent } from '@/components/ui/card';

import type { TicketHistory } from '@/interfaces/ticketHistory.interface';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const openHistory = defineModel<boolean>('open');

defineProps<{
    histories: TicketHistory[];
}>();



</script>