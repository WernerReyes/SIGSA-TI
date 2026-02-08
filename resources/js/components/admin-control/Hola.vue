<script setup lang="ts">
import { Check, Eye } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { NotificationContract, ContractPeriod } from '@/interfaces/contract.interface'

defineProps<{
  notification: NotificationContract
  periodLabel: (p?: ContractPeriod) => string
  severityColor: (n: NotificationContract) => string
  markAsRead: (n: NotificationContract) => void
}>()
</script>

<template>
  <div
    class="group relative flex gap-3 rounded-xl border p-3 transition hover:bg-muted/40 hover:shadow-sm"
  >
    <div class="absolute left-0 top-0 h-full w-1 rounded-l-xl" :class="severityColor(notification)" />

    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2">
        <p class="text-sm font-semibold truncate">
          {{ notification.contract?.name || notification.data.message }}
        </p>

        <Badge variant="outline" class="text-[9px]">
          {{ periodLabel(notification.contract?.period) }}
        </Badge>

        <span v-if="!notification.read_at" class="w-2 h-2 bg-primary rounded-full"></span>
      </div>

      <p class="text-xs text-muted-foreground truncate">
        {{ notification.contract?.provider }}
      </p>

      <p v-if="notification.contract?.billing" class="text-[11px] text-muted-foreground mt-1">
        Próxima facturación: {{ notification.contract.billing.next_billing_date }}
      </p>
    </div>

    <div class="opacity-0 group-hover:opacity-100 transition flex gap-1">
      <Button size="icon" variant="ghost" @click="markAsRead(notification)">
        <Check class="w-4 h-4" />
      </Button>
      <Button size="icon" variant="ghost">
        <Eye class="w-4 h-4" />
      </Button>
    </div>
  </div>
</template>
