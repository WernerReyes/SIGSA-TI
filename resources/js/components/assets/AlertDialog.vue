<template>
  <Dialog :open="open" @update:open="$emit('update:open', $event)">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>Detalles de la Alerta</DialogTitle>
      </DialogHeader>

      <div v-if="alert" class="space-y-6">
        <!-- Header Info -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium text-muted-foreground">Tipo de Alerta</label>
            <p class="text-lg font-semibold mt-1">{{ alert.type }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-muted-foreground">Estado</label>
            <div class="mt-1">
              <Badge :class="alertStatusOptions[alert.status].bg">
                <component :is="alertStatusOptions[alert.status].icon" class="size-3 mr-1" />
                {{ alertStatusOptions[alert.status].label }}
              </Badge>
            </div>
          </div>
        </div>

        <!-- Entity Info -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium text-muted-foreground">Tipo de Entidad</label>
            <p class="text-sm mt-1 bg-muted p-2 rounded">{{ alert.entity_type }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-muted-foreground">ID de Entidad</label>
            <p class="text-sm mt-1 bg-muted p-2 rounded">{{ alert.entity_id || 'N/A' }}</p>
          </div>
        </div>

        <!-- Message -->
        <div>
          <label class="text-sm font-medium text-muted-foreground">Mensaje</label>
          <div class="mt-2 p-4 bg-muted rounded-lg border border-border">
            <p class="text-sm leading-relaxed">{{ alert.message }}</p>
          </div>
        </div>

        <!-- Metadata -->
        <div v-if="alert.metadata && Object.keys(alert.metadata).length > 0">
          <label class="text-sm font-medium text-muted-foreground">Información Adicional</label>
          <div class="mt-2 space-y-2">
            <div v-for="(value, key) in alert.metadata" :key="key" class="p-3 bg-muted rounded border border-border">
              <p class="text-xs font-medium text-muted-foreground uppercase">{{ key }}</p>
              <p class="text-sm mt-1">{{ JSON.stringify(value) }}</p>
            </div>
          </div>
        </div>

        <!-- Timestamps -->
        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
          <div>
            <label class="text-sm font-medium text-muted-foreground">Creado</label>
            <p class="text-sm mt-1">{{ formatDate(alert.created_at) }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-muted-foreground">Último Notificado</label>
            <p class="text-sm mt-1">{{ formatDate(alert.last_notified_at) }}</p>
          </div>
        </div>
      </div>

      <DialogFooter class="flex gap-2">
        <button
          @click="$emit('update:open', false)"
          class="px-4 py-2 rounded-lg border hover:bg-muted transition"
        >
          Cerrar
        </button>
        <button
          @click="$emit('mark-resolved')"
          class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition"
        >
          Marcar como Resuelto
        </button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import type { Alert } from '@/interfaces/alert.interface';
import { alertStatusOptions } from '@/interfaces/alert.interface';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';

defineProps<{
  open: boolean;
  alert: Alert | null;
}>();

defineEmits<{
  'update:open': [value: boolean];
  'mark-resolved': [];
}>();

const formatDate = (date: string | null) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>