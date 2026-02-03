<template>
    <ScrollArea class="h-96 sm:h-[65vh] w-full" @vue:mounted="() => {
        historyVisible = true
    }">
        <div v-if="isLoading" class="space-y-6 px-4 py-4 sm:px-6">
            <!-- Skeleton for Chart -->
            <section class="rounded-lg border bg-card p-4">
                <div class="flex items-center gap-2 mb-4">
                    <Skeleton class="h-4 w-4 rounded" />
                    <Skeleton class="h-5 w-40" />
                </div>
                <Skeleton class="w-full h-64 rounded-lg mb-3" />
                <div class="flex justify-between">
                    <Skeleton class="h-4 w-8" />
                    <Skeleton class="h-4 w-20" />
                    <Skeleton class="h-4 w-8" />
                </div>
            </section>

            <!-- Skeleton for Stats -->
            <section class="grid grid-cols-3 gap-3">
                <div class="rounded-lg border bg-card p-3">
                    <Skeleton class="h-3 w-16 mx-auto mb-2" />
                    <Skeleton class="h-8 w-12 mx-auto" />
                </div>
                <div class="rounded-lg border bg-card p-3">
                    <Skeleton class="h-3 w-16 mx-auto mb-2" />
                    <Skeleton class="h-8 w-12 mx-auto" />
                </div>
                <div class="rounded-lg border bg-card p-3">
                    <Skeleton class="h-3 w-16 mx-auto mb-2" />
                    <Skeleton class="h-8 w-12 mx-auto" />
                </div>
            </section>

            <!-- Skeleton for List Items -->
            <section class="space-y-3">
                <Skeleton class="h-5 w-32" />
                <div v-for="i in 3" :key="i" class="rounded-lg border bg-card p-3 space-y-2">
                    <div class="flex items-center gap-3">
                        <Skeleton class="h-10 w-10 rounded-full" />
                        <div class="flex-1 space-y-2">
                            <Skeleton class="h-3 w-32" />
                            <Skeleton class="h-2 w-full" />
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div v-else class="space-y-6 px-4 py-4 sm:px-6">
            <div v-if="progressHistory.length === 0" class="text-center py-8 text-muted-foreground">
                <History class="h-12 w-12 mx-auto mb-3 opacity-50" />
                <p class="text-sm">No hay registros de progreso aún</p>
            </div>

            <div v-else class="space-y-6">
                <!-- Gráfica de Progreso con Unovis -->
                <section class="rounded-lg border bg-card p-4">
                    <h3 class="text-sm font-semibold mb-4 flex items-center gap-2">
                        <TrendingUp class="h-4 w-4 text-primary" />
                        Evolución del Progreso
                    </h3>
                    <ProgressChart :data="sortedProgressHistory" />
                    <div class="mt-3 flex items-center justify-between text-xs text-muted-foreground">
                        <span>0%</span>
                        <span class="text-primary font-semibold">Progreso Total</span>
                        <span>100%</span>
                    </div>
                </section>

                <!-- Estadísticas de Progreso -->
                <section class="grid grid-cols-3 gap-3">
                    <div class="rounded-lg border bg-card p-3 text-center">
                        <p class="text-xs text-muted-foreground mb-1">Promedio</p>
                        <p class="text-2xl font-bold text-primary">{{ averageProgress }}%</p>
                    </div>
                    <div class="rounded-lg border bg-card p-3 text-center">
                        <p class="text-xs text-muted-foreground mb-1">Máximo</p>
                        <p class="text-2xl font-bold text-green-600">{{ maxProgress }}%</p>
                    </div>
                    <div class="rounded-lg border bg-card p-3 text-center">
                        <p class="text-xs text-muted-foreground mb-1">Registros</p>
                        <p class="text-2xl font-bold text-primary">{{ progressHistory.length }}</p>
                    </div>
                </section>

                <!-- Lista Detallada -->
                <section class="space-y-3">
                    <h3 class="text-sm font-semibold flex items-center gap-2">
                        <History class="h-4 w-4 text-muted-foreground" />
                        Detalle de Registros
                    </h3>
                    <div v-for="progress in progressHistory" :key="progress.id"
                        class="rounded-lg border bg-linear-to-br from-card to-muted/30 p-4 space-y-3 hover:shadow-lg transition-all hover:border-primary/50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="size-12 rounded-full bg-linear-to-br from-blue-500/20 to-blue-500/10 flex items-center justify-center ring-2 ring-blue-500/10">
                                    <span class="text-sm font-bold text-primary">{{
                                        progress.percentage }}%</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap justify-between">
                                        <div class="flex items-center gap-2">
                                            <CalendarIcon class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                            <p class="text-xs text-muted-foreground">
                                                {{ format(new Date(progress.created_at),
                                                    'dd MMM yyyy HH:mm',
                                                    { locale: es }) }}
                                            </p>
                                        </div>


                                        <Badge variant="secondary"
                                            class="ml-auto px-2 py-1 text-xs flex items-center gap-1">
                                            <User class=" shrink-0" />
                                            <p class="truncate">{{
                                                progress.performed_by?.full_name }}</p>
                                        </Badge>

                                        <!-- <div v-if="progress.performed_by"
                                                            class="flex items-center gap-2">
                                                        </div> -->
                                    </div>
                                    <Progress :model-value="progress.percentage" class="h-2 mt-2" />
                                </div>
                            </div>
                        </div>
                        <div v-if="progress.notes"
                            class="pl-3 py-2 border-l-2 border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/20 rounded-r-md">
                            <p class="text-xs text-foreground/80 leading-relaxed">{{ progress.notes }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </ScrollArea>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import ProgressChart from './ProgressChart.vue';
import { History, User, CalendarIcon, TrendingUp } from 'lucide-vue-next';
import { DevelopmentProgress } from '@/interfaces/developmentProgress.interface';
import { router, usePage } from '@inertiajs/vue3';
import { useApp } from '@/composables/useApp';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Skeleton } from '@/components/ui/skeleton';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { watch } from 'vue';
import { DevelopmentRequest } from '@/interfaces/developmentRequest.interface';

const loadedHistoryFor = defineModel<number | null>('loadedHistoryFor');

const { currDev } = defineProps<{
    currDev?: DevelopmentRequest | null;

}>();

const page = usePage();
const { isLoading } = useApp();

// const loadedHistoryFor = ref<number | null>(null);
const historyVisible = ref(false);

const progressHistory = computed<DevelopmentProgress[]>(() => {
    return page.props.progressHistory as DevelopmentProgress[] || [];
});

const sortedProgressHistory = computed(() => {
    return [...progressHistory.value].sort((a, b) =>
        new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
    );
});

const averageProgress = computed(() => {
    if (progressHistory.value.length === 0) return 0;
    const total = progressHistory.value.reduce((sum, p) => sum + p.percentage, 0);
    return Math.round(total / progressHistory.value.length);
});

const maxProgress = computed(() => {
    if (progressHistory.value.length === 0) return 0;
    return Math.max(...progressHistory.value.map(p => p.percentage));
});


watch(
    () => historyVisible.value,
    (visible) => {
        const id = currDev?.id;
        if (!id) return;
        if (loadedHistoryFor.value !== id && visible) {
            router.reload({
                only: ['progressHistory'],
                data: {
                    development_request_id: id
                },
                preserveUrl: true,
                onSuccess: () => {
                    loadedHistoryFor.value = id ?? null;
                }
            });
        }
    }

)
</script>