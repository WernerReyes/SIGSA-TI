<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-w-[min(100vw-1.5rem,500px)] sm:max-w-md p-0">
            <div class="space-y-4 py-4">
                <div class="p-4 bg-muted/50 rounded-lg space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-mono text-xs text-muted-foreground">DEV-2024-002</p>
                            <p class="font-medium mt-1">Integración API Proveedores</p>
                        </div>
                    </div>
                    <p class="text-sm text-muted-foreground">Conectar sistema con APIs externas de proveedores</p>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t">
                        <div>
                            <p class="text-xs text-muted-foreground">Fecha Estimada</p>
                            <p class="text-sm font-medium">2024-02-28</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Horas Estimadas</p>
                            <p class="text-sm font-medium">120</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
                    <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-alert w-5 h-5 text-muted-foreground">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" x2="12" y1="8" y2="12"></line>
                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                        </svg><span class="text-sm">Aprobación Técnica</span></div>
                    <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-alert w-5 h-5 text-muted-foreground">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" x2="12" y1="8" y2="12"></line>
                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                        </svg><span class="text-sm">Aprobación Estratégica</span></div>
                </div>
                <div class="p-3 bg-info/10 border border-info/20 rounded-lg">
                    <p class="text-sm text-info"><strong>Validar:</strong> Disponibilidad de recursos y corrección de la
                        estimación de tiempos.</p>
                </div>
                <div class="p-2 bg-muted/30 rounded text-xs text-muted-foreground">Aprobando como: <strong>Juan Pérez
                        (Gerente TI)</strong></div>
                <div class="space-y-2"><label
                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Comentarios
                        / Observaciones</label><textarea
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Agregar comentarios sobre la aprobación o motivo de rechazo..." rows="3"
                        spellcheck="false">asaasssaxxsxs</textarea></div>
            </div>
        </DialogContent>
    </Dialog>

</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FieldError, Label, VeeField } from '@/components/form';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Calendar } from '@/components/ui/calendar';
import { CheckCircle, ChevronDownIcon, Clock, CalendarIcon, Timer, Info } from 'lucide-vue-next';
import { format, toDate } from 'date-fns';


const open = defineModel<boolean>('open');
</script>