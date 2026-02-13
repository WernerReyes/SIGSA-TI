<template>
    <Sheet v-model:open="open">
        <SheetContent side="right" class="w-full sm:w-105 overflow-y-auto">
            <SheetHeader class="space-y-3 pb-3 border-b">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-linear-to-br from-cyan-200/60 to-blue-200/60 dark:from-cyan-900/40 dark:to-blue-900/40 flex items-center justify-center">
                        <MonitorSmartphone class="size-5 text-blue-700 dark:text-blue-300" />
                    </div>
                    <div class="flex-1">
                        <SheetTitle class="text-lg">Equipos del solicitante</SheetTitle>
                        <p class="text-xs text-muted-foreground">Asignaciones actuales y anteriores</p>
                    </div>
                </div>
            </SheetHeader>

            <div class="space-y-4 py-3">
                <div v-if="!requesterId"
                    class="rounded-xl border border-dashed bg-muted/30 p-4 text-center text-xs text-muted-foreground">
                    Selecciona un solicitante para ver sus equipos.
                </div>

                <div class="space-y-4 px-3" v-if="isLoading">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <Skeleton class="h-4 w-32"></Skeleton>
                            <Skeleton class="h-6 w-12"></Skeleton>
                        </div>
                        <div class="space-y-2">
                            <Skeleton class="h-12 rounded-xl"></Skeleton>
                            <Skeleton class="h-12 rounded-xl"></Skeleton>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <Skeleton class="h-4 w-40"></Skeleton>
                            <Skeleton class="h-6 w-12"></Skeleton>
                        </div>
                        <div class="space-y-2">
                            <Skeleton class="h-12 rounded-xl"></Skeleton>
                            <Skeleton class="h-12 rounded-xl"></Skeleton>
                        </div>
                    </div>
                </div>
                <template v-else>
                    <div class="space-y-3 px-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                                Asignaciones actuales</h4>
                            <Badge
                                class="text-[11px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                                {{ currentAssignments.length }}</Badge>
                        </div>

                        <Accordion v-if="currentAssignments.length" type="multiple" class="space-y-2">
                            <AccordionItem v-for="currentAssignment in currentAssignments" :key="currentAssignment.id"
                                :value="`current-${currentAssignment.id}`" class="rounded-xl border bg-card/70">
                                <AccordionTrigger class="px-4 py-2">
                                    <div class="flex w-full items-center gap-3">
                                        <div
                                            class="size-9 rounded-lg bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 flex items-center justify-center shrink-0">
                                            <component :is="assetTypeOp(currentAssignment.asset?.type?.name)?.icon"
                                                class="size-4 text-emerald-700 dark:text-emerald-300" />
                                        </div>
                                        <div class="min-w-0 flex-1 max-w-full">
                                            <div class="text-sm font-semibold wrap-break-word">{{
                                                currentAssignment.asset?.full_name || 'Equipo' }}</div>
                                            <div class="text-[11px] text-muted-foreground tracking-wide">{{
                                                assetTypeOp(currentAssignment.asset?.type?.name)?.value
                                                || 'Tipo no especificado' }}</div>
                                        </div>
                                        <!-- <Badge class="text-[10px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">Asignado</Badge> -->
                                    </div>
                                </AccordionTrigger>
                                <AccordionContent class="px-3 pb-3">
                                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                                        <div class="rounded-lg border bg-background/70 px-2 py-2">
                                            <div class="text-muted-foreground">Asignado</div>
                                            <div class="font-medium mt-0.5">{{
                                                formatDate(currentAssignment.assigned_at) }}</div>
                                        </div>
                                        <div class="rounded-lg border bg-background/70 px-2 py-2">
                                            <div class="text-muted-foreground">Estado</div>
                                            <div class="font-medium mt-0.5">Activo</div>
                                        </div>
                                    </div>

                                    <div v-if="currentAssignment.children_assignments?.length" class="mt-3">
                                        <div
                                            class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wide">
                                            Accesorios</div>
                                        <div
                                            class="mt-2 grid gap-2 border-l-2 border-dashed border-emerald-200 dark:border-emerald-900/50 pl-3">
                                            <div v-for="child in currentAssignment.children_assignments" :key="child.id"
                                                class="rounded-lg border bg-muted/30 px-2 py-2">
                                                <div class="text-xs font-semibold tracking-wide">{{
                                                    child.asset?.full_name
                                                    || 'Accesorio' }}</div>
                                                <div class="text-[11px] text-muted-foreground truncate">{{
                                                    child.asset?.type?.name || 'Accesorio' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>

                        <div v-else
                            class="rounded-xl border border-dashed bg-muted/30 p-3 text-center text-xs text-muted-foreground">
                            Sin asignaciones actuales
                        </div>
                    </div>

                    <div class="space-y-3 px-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                                Asignaciones anteriores</h4>
                            <Badge
                                class="text-[11px] bg-slate-100 text-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                                {{ previousAssignments.length }}</Badge>
                        </div>

                        <Accordion v-if="previousAssignments.length" type="multiple" class="space-y-2">
                            <AccordionItem v-for="assignment in previousAssignments" :key="assignment.id"
                                :value="`prev-${assignment.id}`" class="rounded-xl border bg-card/60">
                                <AccordionTrigger class="px-3 py-2">
                                    <div class="flex w-full items-center gap-3">
                                        <div
                                            class="size-9 rounded-lg bg-gradient-to-br from-slate-100 to-zinc-100 dark:from-slate-900/50 dark:to-zinc-900/50 flex items-center justify-center shrink-0">
                                            <component :is="assetTypeOp(assignment.asset?.type?.name)?.icon"
                                                class="size-4 text-slate-700 dark:text-slate-300" />
                                        </div>
                                        <div class="min-w-0 flex-1 max-w-full">
                                            <div class="text-sm font-semibold wrap-break-word">{{
                                                assignment.asset?.full_name || 'Equipo' }}</div>
                                            <div class="text-[11px] text-muted-foreground truncate">{{
                                                assetTypeOp(assignment.asset?.type?.name)?.value
                                                || 'Tipo no especificado' }}</div>
                                        </div>
                                        <Badge
                                            class="text-[10px] bg-slate-100 text-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                                            Devuelto</Badge>
                                    </div>
                                </AccordionTrigger>
                                <AccordionContent class="px-3 pb-3">
                                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                                        <div class="rounded-lg border bg-background/70 px-2 py-2">
                                            <div class="text-muted-foreground">Asignado</div>
                                            <div class="font-medium mt-0.5">{{
                                                formatDate(assignment.assigned_at) }}</div>
                                        </div>
                                        <div class="rounded-lg border bg-background/70 px-2 py-2">
                                            <div class="text-muted-foreground">Devuelto</div>
                                            <div class="font-medium mt-0.5">{{ assignment.returned_at ?
                                                formatDate(assignment.returned_at) : '—' }}</div>
                                        </div>
                                    </div>

                                    <div v-if="assignment.children_assignments?.length" class="mt-3">
                                        <div
                                            class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wide">
                                            Accesorios</div>
                                        <div
                                            class="mt-2 grid gap-2 border-l-2 border-dashed border-slate-200 dark:border-slate-900/50 pl-3">
                                            <div v-for="child in assignment.children_assignments" :key="child.id"
                                                class="rounded-lg border bg-muted/30 px-2 py-2">
                                                <p class="text-xs font-semibold truncate wrap-break-word">{{
                                                    child.asset?.full_name
                                                    || 'Accesorio' }}</p>
                                                <div class="text-[11px] text-muted-foreground truncate">{{
                                                    child.asset?.type?.name || 'Accesorio' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>

                        <div v-else
                            class="rounded-xl border border-dashed bg-muted/30 p-3 text-center text-xs text-muted-foreground">
                            Sin asignaciones anteriores
                        </div>
                    </div>
                </template>

            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Skeleton } from '@/components/ui/skeleton';
import { useApp } from '@/composables/useApp';
import type { AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { router, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { MonitorSmartphone } from 'lucide-vue-next';
import { computed, watch } from 'vue';

const open = defineModel<boolean>('open');



const props = defineProps<{
    requesterId: number | null | undefined;
}>();

const page = usePage();
const { isLoading } = useApp();

const assignments = computed<AssetAssignment[]>(() => {
    return (page.props?.userAssignments || []) as AssetAssignment[];
});

const currentAssignments = computed<AssetAssignment[]>(() => {
    return assignments.value.filter(assignment => !assignment.returned_at);
});

const previousAssignments = computed<AssetAssignment[]>(() => {
    return assignments.value.filter(assignment => assignment.returned_at);
});

const formatDate = (value: string) => {
    return format(new Date(value), 'PPP p', { locale: es });
};

watch(() => props.requesterId, (newVal) => {
    if (newVal) {
        isLoading.value = true;
        router.reload({
            only: ['userAssignments'],
            data: { requester_id: newVal },
            preserveUrl: true,
            onFinish: () => {
                isLoading.value = false;
            },
        });
    }
}, { immediate: true });
</script>
