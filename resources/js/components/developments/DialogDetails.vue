<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            currentDevelopment = null;
        }
    }">
        <DialogContent class="max-w-[min(100vw-1.5rem,900px)] sm:max-w-3xl p-0">
            <DialogHeader class="border-b px-4 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                            <FileText class="size-6 text-blue-600" />
                        </div>
                        <div class="flex text-start flex-col gap-1 max-w-[72vw] sm:max-w-none">
                            <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                                {{ currentDevelopment?.title || 'Detalles del Requerimiento' }}
                            </DialogTitle>
                            <p class="text-sm text-muted-foreground leading-snug">
                                Información completa de la solicitud de desarrollo
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2.5 items-center">
                        <Badge v-if="currentDevelopment?.priority"
                            :class="getPriorityOp(currentDevelopment.priority).bg + ' text-xs font-medium flex items-center gap-1'">
                            <component :is="getPriorityOp(currentDevelopment.priority).icon" class="h-3 w-3" />
                            {{ getPriorityOp(currentDevelopment.priority).label }}
                        </Badge>
                        <Badge v-if="currentDevelopment?.status"
                            :class="getStatusOp(currentDevelopment?.status).bg + ' text-xs font-medium flex items-center gap-1'">
                            <component :is="getStatusOp(currentDevelopment?.status).icon" class="h-3 w-3" />
                            {{ getStatusOp(currentDevelopment?.status).label }}
                        </Badge>
                        <Badge variant="outline" class="text-xs">
                            {{ currentDevelopment?.area?.descripcion_area || 'Sin área' }}
                        </Badge>
                    </div>
                </div>
            </DialogHeader>

            <ScrollArea class="max-h-96 sm:max-h-[65vh]">
                <div class="space-y-6 px-4 pb-5 sm:px-6 sm:pb-6">
                    <!-- Status Badges -->


                    <!-- Approval Status -->
                    <section class="space-y-4 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="h-4 w-4 text-emerald-600" />
                            <div>
                                <p class="text-sm font-medium">Estado de Aprobaciones</p>
                                <p class="text-xs text-muted-foreground">Requerimientos de autorización</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                            <!-- Technical Approval -->
                            <div :class="`p-3 rounded-lg border-2 flex flex-col space-y-3 transition-all ${currentDevelopment?.technical_approval
                                ? currentDevelopment.technical_approval.status === DevelopmentApprovalStatus.APPROVED
                                    ? 'bg-emerald-50/60 border-emerald-200/60 dark:bg-emerald-950/30 dark:border-emerald-900/50'
                                    : 'bg-red-50/60 border-red-200/60 dark:bg-red-950/30 dark:border-red-900/50'
                                : 'bg-background border-border/50'
                                }`">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">

                                        <component :is="techAppStatusOp.icon" :class="`size-4 ${techAppStatusOp.color
                                            }`" />
                                        <span class="text-sm font-medium">Aprobación Técnica</span>
                                    </div>
                                    <Badge class="text-xs" :class="techAppStatusOp.bg">
                                        {{ techAppStatusOp.label }}
                                    </Badge>
                                </div>

                                <p class="text-xs text-muted-foreground ml-6">
                                    <template v-if="currentDevelopment?.technical_approval?.approved_by">

                                        {{ currentDevelopment?.technical_approval?.approved_by?.full_name
                                        }}
                                        (Gerente de TI)
                                    </template>
                                    <template v-else>
                                        Gerente de TI
                                    </template>


                                </p>
                                <div v-if="currentDevelopment?.technical_approval?.comments"
                                    class="mt-2 p-2 bg-background/50 rounded text-xs border border-border/30">
                                    <p class="font-medium text-foreground/70 mb-1">Comentarios:</p>
                                    <p class="text-muted-foreground">{{ currentDevelopment.technical_approval.comments
                                    }}</p>
                                </div>

                                <template v-if="currentDevelopment?.technical_approval?.updated_at">

                                    <small class="text-xs text-muted-foreground ml-auto">
                                        {{ format(
                                            currentDevelopment.technical_approval.updated_at || '',
                                            'dd MMM yyyy HH:mm',
                                            { locale: es }
                                        ) }}
                                    </small>
                                </template>

                            </div>
                            <!-- Strategic Approval -->
                            <div :class="`p-3 rounded-lg border-2 flex flex-col space-y-3 transition-all ${currentDevelopment?.strategic_approval
                                ? currentDevelopment.strategic_approval.status === 'APPROVED'
                                    ? 'bg-emerald-50/60 border-emerald-200/60 dark:bg-emerald-950/30 dark:border-emerald-900/50'
                                    : 'bg-red-50/60 border-red-200/60 dark:bg-red-950/30 dark:border-red-900/50'
                                : 'bg-background border-border/50'
                                }`">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <component :is="stratAppStatusOp.icon" :class="`size-4 ${stratAppStatusOp.color
                                            }`" />
                                        <span class="text-sm font-medium">Aprobación Estratégica</span>
                                    </div>
                                    <Badge class="text-xs" :class="stratAppStatusOp.bg">
                                        {{ stratAppStatusOp.label }}

                                    </Badge>
                                </div>
                                <p class="text-xs text-muted-foreground ml-6">
                                    <template v-if="currentDevelopment?.strategic_approval?.approved_by">

                                        {{ currentDevelopment?.strategic_approval?.approved_by?.full_name
                                        }}
                                        (Sub-Gerente de TI)
                                    </template>
                                    <template v-else>
                                        Sub-Gerente de TI
                                    </template>

                                </p>
                                <div v-if="currentDevelopment?.strategic_approval?.comments"
                                    class="mt-2 p-2 bg-background/50 rounded text-xs border border-border/30">
                                    <p class="font-medium text-foreground/70 mb-1">Comentarios:</p>
                                    <p class="text-muted-foreground">{{ currentDevelopment.strategic_approval.comments
                                    }}</p>
                                </div>

                                <template v-if="currentDevelopment?.strategic_approval?.updated_at">

                                    <small class="text-xs text-muted-foreground ml-auto">
                                        {{ format(
                                            currentDevelopment.strategic_approval.updated_at || '',
                                            'dd MMM yyyy HH:mm',
                                            { locale: es }
                                        ) }}
                                    </small>
                                </template>

                            </div>
                        </div>
                    </section>

                    <!-- Estimation Details -->
                    <section class="space-y-4 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <Clock class="h-4 w-4 text-amber-500" />
                            <div>
                                <p class="text-sm font-medium">Detalles de Estimación</p>
                                <p class="text-xs text-muted-foreground">Tiempo y recursos estimados</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg bg-background border border-border/50 space-y-1">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <Calendar class="h-3.5 w-3.5" />
                                    <span>Fecha Estimada</span>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{ currentDevelopment?.estimated_end_date
                                        ? format(parseDateOnly(currentDevelopment.estimated_end_date), 'dd MMM yyyy', {
                                            locale: es,

                                        })
                                        : 'Por definir'
                                    }}
                                </p>
                            </div>
                            <div class="p-3 rounded-lg bg-background border border-border/50 space-y-1">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <Timer class="h-3.5 w-3.5" />
                                    <span>Horas/Hombre estimadas</span>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{ currentDevelopment?.estimated_hours ?
                                        `${currentDevelopment.estimated_hours}h` : 'Por definir' }}
                                </p>
                            </div>

                        </div>
                    </section>


                    <!-- Description + Requester -->
                    <section class="space-y-3 grid  gap-2 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4"
                        :class="{
                            'grid-cols-2': currentDevelopment?.requirement_url
                        }">

                        <div class="space-y-3">

                            <div class="space-y-3">

                                <div
                                    class="flex items-center gap-2 rounded-md border border-border/50 bg-background/50 p-3">
                                    <FileText class="h-4 w-4 text-blue-600" />
                                    <div class="text-sm">
                                        <p class="font-medium">Descripción</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ currentDevelopment?.description || 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">

                                <div
                                    class="flex items-center gap-2 rounded-md border border-border/50 bg-background/50 p-3">
                                    <FileText class="h-4 w-4 text-blue-600" />
                                    <div class="text-sm">
                                        <p class="font-medium">Impacto Esperado</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ currentDevelopment?.impact || 'Sin impacto esperado' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">

                                <div
                                    class="flex items-center gap-2 rounded-md border border-border/50 bg-background/50 p-3">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <div class="text-sm">
                                        <p class="font-medium">Solicitante</p>
                                        <p class="text-xs text-muted-foreground">{{
                                            currentDevelopment?.requested_by?.full_name || 'Sin asignar' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attached Files -->
                        <section v-if="currentDevelopment?.requirement_url"
                            class="space-y-3 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="size-8 rounded-md bg-purple-500/10 flex items-center justify-center ring-1 ring-purple-500/15">
                                    <FileArchive class="h-4 w-4 text-purple-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Archivo Adjunto</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ currentDevelopment?.requirement_url ? 'Documento cargado'
                                            : 'Sin archivo adjunto' }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="currentDevelopment?.requirement_url"
                                class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <a :href="currentDevelopment.requirement_url" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2 rounded-md bg-background px-3 py-2 text-sm font-medium text-foreground border border-border/60 shadow-xs hover:bg-muted/60 transition-colors">
                                    <FileArchive class="h-4 w-4 text-purple-600" />
                                    Ver archivo
                                </a>
                                <span class="text-xs text-muted-foreground">Se abrirá en una nueva pestaña</span>
                            </div>
                            <div v-else
                                class="rounded-md border border-dashed border-border/60 bg-background/40 px-3 py-2 text-xs text-muted-foreground">
                                Aún no se ha cargado ningún documento.
                            </div>
                        </section>

                    </section>








                </div>
            </ScrollArea>

            <Separator />

            <DialogFooter class="px-4 py-4 sm:px-6">
                <Button variant="outline" type="button" @click="open = false" class="w-full sm:w-auto">
                    Cerrar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { getStatusOp as getAppStatusOp } from '@/interfaces/developmentApproval.interface';
import { type DevelopmentRequest, getPriorityOp, getStatusOp } from '@/interfaces/developmentRequest.interface';
import { parseDateOnly } from '@/lib/utils';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Calendar, CheckCircle, Clock, FileArchive, FileText, Timer, User } from 'lucide-vue-next';
import { DevelopmentApprovalStatus } from '@/interfaces/developmentApproval.interface';
import { computed } from 'vue';

const open = defineModel<boolean>('open');
const currentDevelopment = defineModel<DevelopmentRequest | null>('currentDevelopment');

const techAppStatusOp = computed(() => {
    return getAppStatusOp(currentDevelopment?.value?.technical_approval?.status);
});

const stratAppStatusOp = computed(() => {
    return getAppStatusOp(currentDevelopment?.value?.strategic_approval?.status);
});
</script>