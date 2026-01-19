<template>
  
    <Countdown v-if="alerts.length > 0"
        title="Próxima notificación de alerta"
       targetLabel="Próxima notificación en:"
       :duration="1440"
       :targetDate="new Date(alerts[0].last_notified_at!)"
      />
  <div class="rounded-xl border shadow-card overflow-hidden">
    <div class="overflow-x-auto">
    
      <table class="w-full text-sm">
        <thead class="border-b bg-muted/50">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Tipo</th>
            <!-- <th class="px-4 py-3 text-left font-medium text-muted-foreground">Entidad</th> -->
            <!-- <th class="px-4 py-3 text-left font-medium text-muted-foreground">Mensaje</th> -->
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Estado</th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Notificado</th>
            <th class="px-4 py-3 text-center font-medium text-muted-foreground">Acciones</th>
          </tr>
        </thead>
       
        <WhenVisible as="tbody" class="divide-y" data="accessoriesOutOfStockAlerts">
          <template #fallback>
            <tr v-for="n in 5" :key="n" class="animate-pulse">
              <td class="px-4 py-3">
                <Skeleton class="h-4 w-24" />
              </td>
              <td class="px-4 py-3">
                <Skeleton class="h-4 w-48" />
              </td>
              <td class="px-4 py-3">
                <Skeleton class="h-4 w-20" />
              </td>
              <td class="px-4 py-3">
                <Skeleton class="h-4 w-32" />
              </td>
              <td class="px-4 py-3 text-center">
                <Skeleton class="h-4 w-8 mx-auto" />
              </td>
            </tr>
          </template>

          <tr v-if="alerts.length === 0">
            <td colspan="5" class="px-4 py-6 text-center text-muted-foreground">
              <Empty class="p-1!">
                <EmptyHeader>
                  <EmptyMedia variant="icon">
                    <MonitorSmartphone />
                  </EmptyMedia>
                  <EmptyTitle>
                    Sin alertas de accesorios agotados
                  </EmptyTitle>
                  <EmptyDescription>
                    No hay alertas de accesorios agotados en este momento.
                  </EmptyDescription>
                </EmptyHeader>


              </Empty>
            </td>
          </tr>


          <tr v-for="alert in alerts" :key="alert.id" class="hover:bg-muted/50 transition">
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <Mail class="size-4 text-blue-500" />
                <span class="font-medium">{{ alert.type }}</span>
              </div>
            </td>

            <!-- <td class="px-4 py-3 text-sm">
              <span class="line-clamp-2">{{ alert.message }}</span>
            </td> -->
            <td class="px-4 py-3">
              <Badge :class="alertStatusOptions[alert.status].bg">
                <component :is="alertStatusOptions[alert.status].icon" class="size-3 mr-1" />
                {{ alertStatusOptions[alert.status].label }}
              </Badge>
            </td>
            <td class="px-4 py-3 text-sm text-muted-foreground">
              {{ formatDate(alert.last_notified_at) }}
            </td>
            <td class="px-4 py-3 text-center">
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <button class="p-1 rounded-md hover:bg-muted transition">
                    <EllipsisVertical class="size-4 text-muted-foreground" />
                  </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem @click="$emit('view-details', alert)">
                    <Mail class="size-4 mr-2" />
                    Ver detalles
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem @click="$emit('mark-resolved', alert)">
                    <CheckCircle class="size-4 mr-2" />
                    Marcar como resuelto
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="$emit('archive', alert)">
                    <Archive class="size-4 mr-2" />
                    Archivar
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </td>
          </tr>
        </WhenVisible>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Alert } from '@/interfaces/alert.interface';
import { alertStatusOptions } from '@/interfaces/alert.interface';
import { Badge } from '@/components/ui/badge';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Mail, EllipsisVertical, CheckCircle, Archive, MonitorSmartphone } from 'lucide-vue-next';
import { usePage, WhenVisible } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import { Empty, EmptyHeader, EmptyTitle, EmptyDescription, EmptyMedia } from '@/components/ui/empty';
import Countdown from '../Countdown.vue';

const page = usePage();

const alerts = computed<Alert[]>(() => {
  return (page.props?.accessoriesOutOfStockAlerts || []) as Alert[];
});


defineEmits<{
  'view-details': [alert: Alert];
  'mark-resolved': [alert: Alert];
  'archive': [alert: Alert];
}>();

const formatDate = (date: string | null) => {
  if (!date) return 'No notificado';
  return new Date(date).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>