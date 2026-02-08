import { computed, Ref } from 'vue'
import { ContractPeriod, NotificationContract } from '@/interfaces/contract.interface'

export const useNotifications = (notifications: Ref<NotificationContract[]>) => {
  const unread = computed(() => notifications.value.filter(n => !n.read_at))
  const read = computed(() => notifications.value.filter(n => !!n.read_at))

  const periodLabel = (p?: ContractPeriod) => ({
    RECURRING: 'Recurrente',
    FIXED_TERM: 'Plazo fijo',
    ONE_TIME: 'Único'
  })

  const severityColor = (n: NotificationContract) => {
    const end = n.contract?.end_date ? new Date(n.contract.end_date) : null
    if (!end) return 'bg-blue-500'
    const days = (end.getTime() - Date.now()) / 86400000
    if (days < 3) return 'bg-red-500'
    if (days < 7) return 'bg-orange-500'
    if (days < 30) return 'bg-yellow-500'
    return 'bg-green-500'
  }

  const markAsRead = (n: NotificationContract) => n.read_at = new Date().toISOString()

  return { unread, read, periodLabel, severityColor, markAsRead }
}
