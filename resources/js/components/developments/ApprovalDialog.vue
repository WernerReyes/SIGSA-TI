<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) currDev = null;
    }">
        <DialogContent class="max-w-[min(100vw-1.5rem,560px)] sm:max-w-lg p-0">
            <DialogHeader class="border-b px-4 py-4 sm:px-6">
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-emerald-500/10 flex items-center justify-center ring-2 ring-emerald-500/10">
                        <CheckCircle class="size-5 text-emerald-600" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg sm:text-xl font-semibold leading-tight">
                            {{ title }}
                        </DialogTitle>
                        <DialogDescription>
                            {{ description }}
                        </DialogDescription>
                    </div>
                    <Badge variant="secondary" class="h-6">{{ role }}</Badge>
                </div>
            </DialogHeader>

            <ScrollArea class="max-h-[65vh]">
                <div class="space-y-5 px-4 py-4 sm:px-6">
                    <section class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-mono text-xs text-muted-foreground">DEV-{{ currDev?.id }}</p>
                                    <p class="font-medium mt-1">{{ currDev?.title }}</p>
                                </div>

                            </div>
                            <p class="text-sm text-muted-foreground">{{ currDev?.description }}</p>
                            <Separator />
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="flex items-center gap-2">
                                    <CalendarIcon class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <p class="text-xs text-muted-foreground">Fecha Estimada</p>
                                        <p class="text-sm font-medium">

                                            <!-- {{ currDev?.estimated_end_date }} -->
                                            {{ format(parseDateOnly(currDev?.estimated_end_date || ''), 'dd MMM yyyy')
                                            }}

                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Timer class="h-4 w-4 text-muted-foreground" />
                                    <div>
                                        <p class="text-xs text-muted-foreground">Horas Estimadas</p>
                                        <p class="text-sm font-medium">{{ currDev?.estimated_hours }}h</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <div class="flex flex-wrap gap-2">
                            <Badge :class="techStatusOp.bg">
                                <component :is="techStatusOp.icon" />
                                Aprobación Técnica
                            </Badge>
                            <Badge :class="strategicStatusOp.bg" class="flex items-center gap-2">
                                <component :is="strategicStatusOp.icon" />
                                Aprobación Estratégica
                            </Badge>
                        </div>

                        <div
                            class="flex items-center gap-2 rounded-md border bg-muted/30 px-3 py-2 text-xs text-muted-foreground">
                            <User class="h-4 w-4" />
                            Aprobando como: <strong class="text-foreground">{{ userAuth?.full_name }} ({{ role
                                }})</strong>
                        </div>
                    </section>

                    <section class="space-y-2">
                        <Label>Comentarios / Observaciones</Label>
                        <Textarea v-model="comment"
                            placeholder="Agregar comentarios sobre la aprobación o motivo de rechazo..." rows="3"
                            spellcheck="false" default-value="asaasssaxxsxs" />
                    </section>
                </div>
            </ScrollArea>

            <DialogFooter class="border-t px-4 py-4 sm:px-6">
                <Button @click="handleApprove(DevelopmentApprovalStatus.REJECTED)" :disabled="isLoading"
                    variant="destructive" type="button" class="w-full sm:w-auto">
                    <X />
                    Rechazar
                </Button>
                <Button @click="handleApprove(DevelopmentApprovalStatus.APPROVED)" :disabled="isLoading" type="button"
                    class="w-full sm:w-auto">
                    <Check />
                    Aprobar
                </Button>

            </DialogFooter>
        </DialogContent>
    </Dialog>

</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { DevelopmentApprovalStatus, getStatusOp } from '@/interfaces/developmentApproval.interface';
import { DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/vue3';
import { CalendarIcon, CheckCircle, Timer, User, Check, X } from 'lucide-vue-next';
import { computed } from 'vue';
import { format } from 'date-fns';
import { parseDateOnly } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { useApp } from '@/composables/useApp';
import { ref } from 'vue';

const open = defineModel<boolean>('open');
const currDev = defineModel<DevelopmentRequest | null>('current-development');

const { role } = defineProps<{
    title: string;
    description: string;
    role: 'Gerente TI' | 'Sub-Gerente de TI';
}>();

const { isLoading, userAuth } = useApp();

const comment = ref<string>('');

const techStatusOp = computed(() => {
    return getStatusOp(currDev?.value?.technical_approval?.status);
});

const strategicStatusOp = computed(() => {
    return getStatusOp(currDev?.value?.strategic_approval?.status);
});

const handleApprove = (status: DevelopmentApprovalStatus) => {
    if (!currDev.value) return;

    const url = role === 'Gerente TI' ?
        `/developments/${currDev.value.id}/approve-technical` :
        `/developments/${currDev.value.id}/approve-strategic`;


    router.post(url, {
        status,
        comments: comment.value,
    }, {
        only: ['developmentsByStatus'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash?.success) {
                open.value = false;
                currDev.value = null;
                comment.value = '';
            }
        },

    });
};


</script>