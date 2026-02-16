<template>
    <div v-if="generalAlert" class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-2">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger as-child>
                    <Button size="icon" variant="default"
                        class="relative h-12 w-12 rounded-full bg-slate-900 text-white hover:bg-slate-800 shadow-lg"
                        @click="showAlertPanel = !showAlertPanel" :aria-expanded="showAlertPanel"
                        aria-label="Ver alerta de accesorios">
                        <Bell class="size-5" />
                        <span
                            class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-white text-slate-800 text-[10px] font-semibold px-1.5 py-0.5 shadow">!</span>
                    </Button>
                </TooltipTrigger>
                <TooltipContent side="left" align="center" class="text-xs">Ver alerta de accesorios</TooltipContent>
            </Tooltip>
        </TooltipProvider>

        <Transition name="fade-slide">
            <Card v-if="showAlertPanel"
                class="w-80 border border-slate-200/80 bg-white/95 backdrop-blur dark:border-slate-800/80 dark:bg-slate-900/95 shadow-2xl">
                <CardHeader class="pb-2">
                    <div class="flex items-start gap-3">
                        <div
                            class="size-11 rounded-xl bg-slate-100 dark:bg-slate-800 grid place-content-center ring-1 ring-slate-200/70 dark:ring-slate-700/80">
                            <AlertCircle class="size-5 text-slate-700 dark:text-slate-200" />
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center gap-2">
                                <CardTitle class="text-sm font-semibold leading-tight">Accesorios sin stock</CardTitle>
                                <Badge variant="secondary" class="text-[11px] px-2 py-0.5">Stock</Badge>
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">
                                {{ generalAlert?.message || 'Correo enviado a Ventas indicando faltante de accesorios.'
                                }}
                            </p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3 pt-1">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Se notificó a Compras para reabastecer.</p>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                        <LoaderCircle v-if="isSending" class="size-3 animate-spin text-slate-500" />
                        <span>Última notificación: {{ lastNotifiedDisplay }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <Button size="sm" variant="outline" class="text-xs" :disabled="isSending"
                            @click="handleResendAlert">
                            <LoaderCircle v-if="isSending" class="size-4 animate-spin" />
                            <span v-else>Reenviar a Ventas</span>
                            <span v-if="isSending">Enviando...</span>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </Transition>
    </div>
</template>


<script setup lang="ts">
import type { Alert } from '@/interfaces/alert.interface';
import { router, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { AlertCircle, Bell, LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useApp } from '@/composables/useApp';

const page = usePage();
const { isFromRRHH  } = useApp();

const showAlertPanel = ref(false);

const generalAlert = computed(() => {
    return (page.props.accessoriesOutOfStockAlert || null) as Alert | null;
});

const updatedDate = ref<string | null>(null);

const lastNotifiedDisplay = computed(() => {
    if (updatedDate.value) return updatedDate.value;
    if (generalAlert.value?.updated_at) {
        return format(new Date(generalAlert.value.updated_at), 'dd/MM/yyyy HH:mm');
    }
    return 'N/A';
});

const isSending = ref(false);


const handleResendAlert = () => {
    if (isSending.value || !generalAlert.value || isFromRRHH.value) {
        return;
    }
    
    isSending.value = true;
    router.post('assets/resend-accessory-out-of-stock-alert', {}, {
        only: ['accessoriesOutOfStockAlert'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error)  return;
            updatedDate.value = format(new Date(), 'dd/MM/yyyy HH:mm');
        },
       
        onFinish: () => {
            isSending.value = false;
        }
    });

};
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(6px);
}
</style>