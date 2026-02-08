<template>
    <Sheet v-model:open="open">
        <SheetContent side="right" class="w-full sm:w-[540px] overflow-y-auto">
            <SheetHeader class="space-y-3 pb-4 border-b">
                <div class="flex items-start gap-3">
                    <div class="size-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <MonitorSmartphone class="size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <SheetTitle class="text-xl">Equipos del solicitante</SheetTitle>
                        <p class="text-sm text-muted-foreground">Asignaciones actuales y anteriores</p>
                    </div>
                </div>
            </SheetHeader>

            <div class="space-y-6 py-4">
                <div v-if="!requesterId" class="rounded-xl border border-dashed bg-muted/30 p-6 text-center text-sm text-muted-foreground">
                    Selecciona un solicitante para ver sus equipos.
                </div>

                <template v-else>
                    <div class="space-y-3 px-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Asignación actual</h4>
                            <Badge variant="secondary" class="text-xs">Actual</Badge>
                        </div>

                        <div v-if="currentAssignment" class="rounded-2xl border bg-card p-4 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate">{{ currentAssignment.asset?.full_name || 'Equipo' }}</p>
                                    <Badge variant="outline" class="mt-1 text-xs inline-flex items-center gap-1">
                                        <component :is="assetTypeOp(currentAssignment.asset?.type?.name)?.icon" class="size-3" />
                                        {{ assetTypeOp(currentAssignment.asset?.type?.name)?.value || 'Tipo no especificado' }}
                                    </Badge>
                                </div>
                                <Badge variant="outline" class="text-[11px]">Asignado</Badge>
                            </div>

                            <div class="mt-3 grid gap-2 text-xs text-muted-foreground sm:grid-cols-2">
                                <div class="rounded-lg border bg-background/60 px-3 py-2">
                                    <span class="font-semibold text-foreground/80">Asignado</span>
                                    <div class="mt-1">{{ formatDate(currentAssignment.assigned_at) }}</div>
                                </div>
                                <div class="rounded-lg border bg-background/60 px-3 py-2">
                                    <span class="font-semibold text-foreground/80">Estado</span>
                                    <div class="mt-1">Activo</div>
                                </div>
                            </div>

                            <div v-if="currentAssignment.children_assignments?.length" class="mt-4">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Accesorios</p>
                                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                    <div
                                        v-for="child in currentAssignment.children_assignments"
                                        :key="child.id"
                                        class="rounded-lg border bg-muted/30 px-3 py-2"
                                    >
                                        <p class="text-xs font-semibold truncate">{{ child.asset?.name || 'Accesorio' }}</p>
                                        <p class="text-[11px] text-muted-foreground truncate">{{ child.asset?.type?.name || 'Accesorio' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-xl border border-dashed bg-muted/30 p-4 text-center text-sm text-muted-foreground">
                            Sin asignación actual
                        </div>
                    </div>

                    <div class="space-y-3 px-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Asignaciones anteriores</h4>
                            <Badge variant="outline" class="text-xs">{{ previousAssignments.length }}</Badge>
                        </div>

                        <div v-if="previousAssignments.length" class="space-y-3">
                            <div
                                v-for="assignment in previousAssignments"
                                :key="assignment.id"
                                class="rounded-2xl border bg-card/70 p-4 transition-all hover:-translate-y-0.5 hover:shadow-md"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ assignment.asset?.full_name || 'Equipo' }}</p>
                                        <Badge variant="outline" class="mt-1 text-xs inline-flex items-center gap-1">
                                            <component :is="assetTypeOp(assignment.asset?.type?.name)?.icon" class="size-3" />
                                            {{ assetTypeOp(assignment.asset?.type?.name)?.value || 'Tipo no especificado' }}
                                        </Badge>
                                    </div>
                                    <Badge variant="outline" class="text-[11px]">Devuelto</Badge>
                                </div>

                                <div class="mt-3 grid gap-2 text-xs text-muted-foreground sm:grid-cols-2">
                                    <div class="rounded-lg border bg-background/60 px-3 py-2">
                                        <span class="font-semibold text-foreground/80">Asignado</span>
                                        <div class="mt-1">{{ formatDate(assignment.assigned_at) }}</div>
                                    </div>
                                    <div class="rounded-lg border bg-background/60 px-3 py-2">
                                        <span class="font-semibold text-foreground/80">Devuelto</span>
                                        <div class="mt-1">{{ assignment.returned_at ? formatDate(assignment.returned_at) : '—' }}</div>
                                    </div>
                                </div>

                                <div v-if="assignment.children_assignments?.length" class="mt-4">
                                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Accesorios</p>
                                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                        <div
                                            v-for="child in assignment.children_assignments"
                                            :key="child.id"
                                            class="rounded-lg border bg-muted/30 px-3 py-2"
                                        >
                                            <p class="text-xs font-semibold truncate">{{ child.asset?.name || 'Accesorio' }}</p>
                                            <p class="text-[11px] text-muted-foreground truncate">{{ child.asset?.type?.name || 'Accesorio' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-xl border border-dashed bg-muted/30 p-4 text-center text-sm text-muted-foreground">
                            Sin asignaciones anteriores
                        </div>
                    </div>
                </template>
            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Badge } from '@/components/ui/badge';
import { MonitorSmartphone } from 'lucide-vue-next';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import type { AssetAssignment } from '@/interfaces/assetAssignment.interface';

const open = defineModel<boolean>('open');

const props = defineProps<{
    requesterId: number | null | undefined;
    assignments: AssetAssignment[];
}>();

const currentAssignment = computed<AssetAssignment | null>(() => {
    return props.assignments.find(assignment => !assignment.returned_at) || null;
});

const previousAssignments = computed<AssetAssignment[]>(() => {
    return props.assignments.filter(assignment => !!assignment.returned_at);
});

const formatDate = (value: string) => {
    return format(new Date(value), 'PPP p', { locale: es });
};
</script>
