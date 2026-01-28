<template>
    <Dialog v-model:open="open">
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
                </div>
            </DialogHeader>

            <ScrollArea class="max-h-96 sm:max-h-[65vh]">
                <div class="space-y-6 px-4 pb-5 sm:px-6 sm:pb-6">
                    <!-- Status Badges -->
                    <section class="flex flex-wrap gap-2.5 items-center">
                        <Badge v-if="currentDevelopment?.priority" :class="getPriorityOp(currentDevelopment.priority).bg + ' text-xs font-medium flex items-center gap-1'">
                            <component :is="getPriorityOp(currentDevelopment.priority).icon" class="h-3 w-3" />
                            {{ getPriorityOp(currentDevelopment.priority).label }}
                        </Badge>
                        <Badge 
                         v-if="currentDevelopment?.status"
                        :class="getStatusOp(currentDevelopment?.status).bg + ' text-xs font-medium flex items-center gap-1'">
                            <component :is="getStatusOp(currentDevelopment?.status).icon" class="h-3 w-3" />
                            {{ getStatusOp(currentDevelopment?.status).label }}
                        </Badge>
                        <Badge variant="outline" class="text-xs">
                            {{ currentDevelopment?.area?.descripcion_area || 'Sin área' }}
                        </Badge>
                    </section>

                    <!-- Description Section -->
                    <section class="space-y-3 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <FileText class="h-4 w-4 text-blue-600" />
                            <div>
                                <p class="text-sm font-medium">Descripción</p>
                                <p class="text-xs text-muted-foreground">Detalles de la solicitud</p>
                            </div>
                        </div>
                        <p class="text-sm text-foreground bg-background/50 p-3 rounded border border-border/50">
                            {{ currentDevelopment?.description || 'Sin descripción' }}
                        </p>
                        <p v-if="currentDevelopment?.impact" class="text-sm text-foreground">
                            <span class="font-medium text-muted-foreground">Impacto esperado:</span> {{ currentDevelopment.impact }}
                        </p>
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
                                        ? formatDate(currentDevelopment.estimated_end_date)
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
                                    {{ currentDevelopment?.estimated_hours ? `${currentDevelopment.estimated_hours}h` : 'Por definir' }}
                                </p>
                            </div>
                            <!-- <div class="p-3 rounded-lg bg-background border border-border/50 space-y-1">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <Users class="h-3.5 w-3.5" />
                                    <span>Personas</span>
                                </div>
                                <p class="text-sm font-semibold">
                                    {{ currentDevelopment?.people_amount || 'Por asignar' }}
                                </p>
                            </div> -->
                        </div>
                    </section>

                    <!-- Assignment Section -->
                    <section class="space-y-3 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <Users class="h-4 w-4 text-green-600" />
                            <div>
                                <p class="text-sm font-medium">Asignación</p>
                                <p class="text-xs text-muted-foreground">Responsables del requerimiento</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="p-3 rounded-lg bg-background border border-border/50">
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-muted-foreground" />
                                    <div class="text-sm">
                                        <p class="font-medium">Solicitante</p>
                                        <p class="text-xs text-muted-foreground">{{ currentDevelopment?.requested_by?.full_name || 'Sin asignar' }}</p>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </section>

                    <!-- Approval Status -->
                    <section class="space-y-4 rounded-lg border border-border/80 bg-muted/20 p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="h-4 w-4 text-emerald-600" />
                            <div>
                                <p class="text-sm font-medium">Estado de Aprobaciones</p>
                                <p class="text-xs text-muted-foreground">Requerimientos de autorización</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg bg-background border border-border/50 space-y-2">
                                <div class="flex items-center gap-2">
                                    <CircleAlert class="h-4 w-4 text-amber-500" />
                                    <span class="text-sm font-medium">Aprobación Técnica</span>
                                </div>
                                <p class="text-xs text-muted-foreground ml-6">Gerente de TI</p>
                            </div>
                            <div class="p-3 rounded-lg bg-background border border-border/50 space-y-2">
                                <div class="flex items-center gap-2">
                                    <CircleAlert class="h-4 w-4 text-amber-500" />
                                    <span class="text-sm font-medium">Aprobación Estratégica</span>
                                </div>
                                <p class="text-xs text-muted-foreground ml-6">Sub-Gerente de TI</p>
                            </div>
                        </div>
                    </section>

                   
                    
                </div>
            </ScrollArea>

            <Separator />

            <DialogFooter class="px-4 py-4 sm:px-6">
                <Button 
                    variant="outline"
                    type="button"
                    @click="open = false"
                    class="w-full sm:w-auto"
                >
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
import { useApp } from '@/composables/useApp';
import { type DevelopmentRequest, getPriorityOp, getStatusOp } from '@/interfaces/developmentRequest.interface';
import { Briefcase, Calendar, CheckCircle, CircleAlert, Clock, FileText, Timer, User, Users } from 'lucide-vue-next';

const open = defineModel<boolean>('open');
const currentDevelopment = defineModel<DevelopmentRequest | null>('currentDevelopment');

const { isLoading } = useApp();

const formatDate = (date: string | null | undefined) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-ES', { 
        month: 'short', 
        day: 'numeric',
        year: 'numeric'
    });
};

const formatDateWithTime = (date: string | null | undefined) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-ES', { 
        month: 'short', 
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>