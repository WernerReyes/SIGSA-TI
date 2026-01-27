<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Service } from '@/interfaces/service.interface';
import { differenceInDays } from 'date-fns';
import { CheckCircle2, Circle, Clock, Zap } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{ 
    services: Service[];
}>();

const stats = computed(() => {
    const services = props.services || [];
    
    return {
        active: services.filter(s => s.is_active).length,
        inactive: services.filter(s => !s.is_active).length,
        recentlyUpdated: services.filter(s => 
            differenceInDays(new Date(), new Date(s.updated_at)) <= 7
        ).length,
        total: services.length,
    };
});

const activePercentage = computed(() => {
    if (stats.value.total === 0) return 0;
    return Math.round((stats.value.active / stats.value.total) * 100);
});
</script>

<template>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Servicios activos -->
        <Card class="border border-emerald-100 dark:border-emerald-900/50 bg-linear-to-br from-emerald-50/40 to-emerald-900/5 dark:from-emerald-950/20 dark:to-emerald-900/10">
            <CardHeader class="pb-2">
                <div class="flex items-center justify-between">
                    <CardTitle class="text-sm text-muted-foreground">Servicios activos</CardTitle>
                    <CheckCircle2 class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div class="text-3xl font-black text-emerald-700 dark:text-emerald-300">
                        {{ stats.active }}
                    </div>
                    <Badge class="bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-100">
                        {{ activePercentage }}% del total
                    </Badge>
                </div>
            </CardContent>
        </Card>

        <!-- Servicios inactivos -->
        <Card class="border border-slate-200 dark:border-slate-800 bg-linear-to-br from-slate-50/40 to-slate-900/5 dark:from-slate-950/20 dark:to-slate-900/10">
            <CardHeader class="pb-2">
                <div class="flex items-center justify-between">
                    <CardTitle class="text-sm text-muted-foreground">Servicios inactivos</CardTitle>
                    <Circle class="h-4 w-4 text-slate-600 dark:text-slate-400" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div class="text-3xl font-black text-slate-700 dark:text-slate-300">
                        {{ stats.inactive }}
                    </div>
                    <Badge variant="outline" class="border-slate-300 dark:border-slate-700">
                        Deshabilitados
                    </Badge>
                </div>
            </CardContent>
        </Card>

        <!-- Actualizaciones recientes -->
        <Card class="border border-blue-100 dark:border-blue-900/50 bg-linear-to-br from-blue-50/40 to-blue-900/5 dark:from-blue-950/20 dark:to-blue-900/10">
            <CardHeader class="pb-2">
                <div class="flex items-center justify-between">
                    <CardTitle class="text-sm text-muted-foreground">Actualizados últimos 7d</CardTitle>
                    <Clock class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div class="text-3xl font-black text-blue-700 dark:text-blue-300">
                        {{ stats.recentlyUpdated }}
                    </div>
                    <Badge class="bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-100">
                        Cambios recientes
                    </Badge>
                </div>
            </CardContent>
        </Card>

        <!-- Total de servicios -->
        <Card class="border border-purple-100 dark:border-purple-900/50 bg-linear-to-br from-purple-50/40 to-purple-900/5 dark:from-purple-950/20 dark:to-purple-900/10">
            <CardHeader class="pb-2">
                <div class="flex items-center justify-between">
                    <CardTitle class="text-sm text-muted-foreground">Total de servicios</CardTitle>
                    <Zap class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div class="text-3xl font-black text-purple-700 dark:text-purple-300">
                        {{ stats.total }}
                    </div>
                    <Badge class="bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-100">
                        Registrados
                    </Badge>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
