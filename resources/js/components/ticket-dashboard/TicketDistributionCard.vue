<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import type { TicketDashboardDistributionItem } from '@/interfaces/ticketDashboard.interface';

defineProps<{
    title: string;
    description: string;
    items: TicketDashboardDistributionItem[];
}>();
</script>

<template>
    <Card class="border-border/80">
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>
            <CardDescription>{{ description }}</CardDescription>
        </CardHeader>
        <CardContent class="space-y-5">
            <div v-for="item in items" :key="item.value" class="space-y-2">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="font-medium">{{ item.label }}</span>
                    <span class="text-muted-foreground tabular-nums">
                        {{ item.count }} · {{ item.percentage }}%
                    </span>
                </div>
                <Progress :model-value="item.percentage" class="h-2" />
            </div>
        </CardContent>
    </Card>
</template>
