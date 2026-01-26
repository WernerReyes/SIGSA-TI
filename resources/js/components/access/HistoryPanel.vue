<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ShieldCheck } from 'lucide-vue-next';
import type { AccessRow } from './types';

defineProps<{
    selectedRow: AccessRow | null;
    history: Record<number, { when: string; event: string; actor: string }[]>;
}>();
</script>

<template>
    <Card v-if="selectedRow" class="border border-primary/30">
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <ShieldCheck class="size-4" /> Historial de {{ selectedRow.platform }} — {{ selectedRow.account }}
            </CardTitle>
            <p class="text-sm text-muted-foreground">Eventos recientes de provisión, rotación y revocación.</p>
        </CardHeader>
        <CardContent class="space-y-3">
            <div v-for="item in history[selectedRow.id]" :key="item.when" class="rounded-lg border border-border/70 bg-muted/40 p-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold">{{ item.event }}</span>
                    <span class="text-xs text-muted-foreground">{{ item.when }}</span>
                </div>
                <p class="text-xs text-muted-foreground">Por: {{ item.actor }}</p>
            </div>
            <p v-if="!history[selectedRow.id]?.length" class="text-sm text-muted-foreground">Sin eventos registrados.</p>
        </CardContent>
    </Card>
</template>
