<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            resetForm();
        }
    }">
        <DialogContent class="max-w-[min(100vw-1.5rem,700px)] sm:max-w-2xl p-0">
            <DialogHeader class="border-b px-4 py-4 sm:px-6">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-blue-500/10 flex items-center justify-center ring-2 ring-blue-500/10">
                        <Activity class="size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                            Registrar Progreso de Desarrollo
                        </DialogTitle>
                        <DialogDescription>
                            Registra el avance del desarrollo y visualiza el historial
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <Tabs default-value="register" class="w-full sm:px-6">
                <TabsList class="grid w-full grid-cols-2 ">
                    <TabsTrigger value="register" class="gap-2">
                        <PlusCircle class="h-4 w-4" />
                        Registrar
                    </TabsTrigger>
                    <TabsTrigger value="history" class="gap-2">
                        <History class="h-4 w-4" />
                        Historial
                    </TabsTrigger>
                </TabsList>

                <!-- Tab: Registrar Progreso -->
                <TabsContent value="register" class="overflow-hidden">
                    <ScrollArea class="h-96 sm:h-[65vh] w-full pr-6">
                        <div class="space-y-5 px-4 py-4 sm:px-2">
                            <!-- Información del Desarrollo -->
                            <section class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                <div class="p-4 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-mono text-xs text-muted-foreground">DEV-{{ currDev?.id }}
                                            </p>
                                            <p class="font-medium mt-1">{{ currDev?.title }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{{ currDev?.description }}</p>
                                </div>
                            </section>

                            <!-- Último Progreso Registrado -->
                            <section v-if="latestProgress"
                                class="rounded-lg border-2 border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-950/30 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <TrendingUp class="h-4 w-4 text-primary" />
                                    <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Último
                                        Progreso
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="size-16 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-primary">{{ latestProgress.percentage
                                            }}%</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <CalendarIcon class="h-3 w-3 text-muted-foreground" />
                                            <p class="text-xs text-muted-foreground">
                                                {{ format(new Date(latestProgress.created_at), 'dd MMM yyyy HH:mm',
                                                    { locale: es }) }}
                                            </p>
                                        </div>
                                        <Progress :model-value="latestProgress.percentage" class="h-2" />
                                        <p v-if="latestProgress.notes" class="text-xs text-foreground/70 mt-2">
                                            {{ latestProgress.notes }}
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <!-- Formulario de Progreso -->
                            <section class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="percentage" class="flex items-center gap-2">
                                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                                        Porcentaje de Avance (%)
                                    </Label>
                                    <div class="flex items-center gap-3">

                                        <Slider :default-value="form.percentage" v-model="form.percentage" :min="0"
                                            :max="100" :step="5" class="flex-1" :disabled="isLoading" />
                                        <span class="text-sm font-semibold min-w-12 text-right">
                                            {{ form.percentage[0] }}%
                                        </span>
                                    </div>
                                    <Progress :model-value="form.percentage[0]" class="h-2" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="notes" class="flex items-center gap-2">
                                        <FileText class="h-4 w-4 text-muted-foreground" />
                                        Notas del Progreso
                                    </Label>
                                    <Textarea id="notes" v-model="form.notes"
                                        placeholder="Describe los avances realizados, obstáculos encontrados, próximos pasos..."
                                        rows="5" :disabled="isLoading" />
                                    <p class="text-xs text-muted-foreground">
                                        Detalla el trabajo realizado en este período
                                    </p>
                                </div>
                            </section>
                        </div>
                    </ScrollArea>

                    <DialogFooter class="border-t px-4 py-3 sm:px-6">
                        <Button variant="outline" @click="open = false" :disabled="isLoading">
                            Cancelar
                        </Button>
                        <Button @click="handleSubmit" :disabled="isLoading" class="gap-2">
                            <Save class="h-4 w-4" />
                            Guardar Progreso
                        </Button>
                    </DialogFooter>
                </TabsContent>

                <!-- Tab: Historial de Progreso -->
                <TabsContent value="history" class="overflow-hidden">
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
                                        class="rounded-lg border bg-card p-3 space-y-2 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 flex-1">
                                                <div
                                                    class="size-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                                                    <span class="text-sm font-bold text-primary">{{
                                                        progress.percentage }}%</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <CalendarIcon class="h-3 w-3 text-muted-foreground" />
                                                        <p class="text-xs text-muted-foreground">
                                                            {{ format(new Date(progress.created_at),
                                                                'dd MMM yyyy HH: mm',
                                                                { locale: es }) }}
                                                        </p>
                                                    </div>
                                                    <Progress :model-value="progress.percentage" class="h-1.5 mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="progress.notes"
                                            class="pl-2 border-l-2 border-blue-200 dark:border-blue-800">
                                            <p class="text-xs text-foreground/70">{{ progress.notes }}</p>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </ScrollArea>
                </TabsContent>
            </Tabs>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import type { DevelopmentProgress } from '@/interfaces/developmentProgress.interface';
import type { DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import {
    Activity,
    CalendarIcon,
    FileText,
    History,
    PlusCircle,
    Save,
    TrendingUp,
} from 'lucide-vue-next';
import ProgressChart from './ProgressChart.vue';
import Slider from '@/components/ui/slider/Slider.vue';
import { computed, reactive, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const open = defineModel<boolean>('open');
const currDev = defineModel<DevelopmentRequest | null>('currentDevelopment');

const page = usePage();
const { isLoading } = useApp();

const form = reactive({
    percentage: [0],
    notes: '',
});


const progressHistory = computed<DevelopmentProgress[]>(() => {
    return page.props.progressHistory as DevelopmentProgress[] || [];
});


const loadedHistoryFor = ref<number | null>(null)
const historyVisible = ref(false);

const latestProgress = computed({
    get: () => {
        const lastest = currDev.value?.latest_progress || null;
        if (lastest) {
            form.percentage = [lastest.percentage];
        };
        return lastest;

    }, set: (val: DevelopmentProgress) => {
        console.log('Setting latest progress to:', val);
        currDev.value!.latest_progress = val;

    }
});


watch(
    () => historyVisible.value,
    (visible) => {
        const id = currDev.value?.id;
        if (!id) return;
        loadedHistoryFor.value = null;
        if (loadedHistoryFor.value !== id && visible) {
            router.reload({
                only: ['progressHistory'],
                data: {
                    development_request_id: id
                },
                preserveUrl: true,
                onSuccess: () => {
                    loadedHistoryFor.value = currDev.value?.id ?? null;
                }
            });
        }
    }

)

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

const resetForm = () => {
    form.percentage = [0];
    form.notes = '';

    currDev.value = null;
    historyVisible.value = false;
    // resetForm();
};

const handleSubmit = () => {
    if (!currDev.value) return;

    if (form.percentage[0] === 0 && !form.notes) {
        toast.error('Debes especificar al menos el porcentaje o agregar notas');
        return;
    }

    if (latestProgress.value && form.percentage[0] === latestProgress.value.percentage) {
        toast.error(`El porcentaje ingresado es igual al último registrado (${latestProgress.value.percentage}%)`);
        return;
    }

    if (form.percentage[0] < (latestProgress.value?.percentage || 0) && form.notes.trim() === '') {
        toast.error(`Debes proporcionar el motivo del retroceso de avance en las notas.`);
        return;
    }

    router.post(
        `/developments/${currDev.value.id}/progress`,
        {
            percentage: form.percentage[0],
            notes: form.notes || null,
        },
        {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.success) {
                    resetForm();
                    open.value = false;
                    const progress = flash.progress as DevelopmentProgress || null;
                    console.log('New progress from flash:', progress);
                    if (progress) {
                        latestProgress.value = progress;
                    }

                }

            }

        }
    );
};
</script>
