import { AlertCircle, Mail, CheckCircle, Clock } from 'lucide-vue-next';

export enum AlertStatus {
  ACTIVE = 'ACTIVE',
  RESOLVED = 'RESOLVED',
  ARCHIVED = 'ARCHIVED',
}

export enum AlertType {
    ACCESSORY_OUT_OF_STOCK = 'accessory_out_of_stock',
}

export enum EntityType {
    ASSET = 'asset',
}

export interface Alert {
  id: number;
  entity: EntityType;
  entity_id: number | null;
  type: AlertType;
  message: string | null;
  status: AlertStatus;
  last_notified_at: string | null;
  metadata: Record<string, any> | null;
  created_at: string;
  updated_at: string;
}

export const alertStatusOptions = {
  [AlertStatus.ACTIVE]: {
    value: AlertStatus.ACTIVE,
    label: 'Activo',
    icon: AlertCircle,
    color: 'text-amber-600 dark:text-amber-400',
    bg: 'bg-amber-100 dark:bg-amber-900/30',
  },
  [AlertStatus.RESOLVED]: {
    value: AlertStatus.RESOLVED,
    label: 'Resuelto',
    icon: CheckCircle,
    color: 'text-emerald-600 dark:text-emerald-400',
    bg: 'bg-emerald-100 dark:bg-emerald-900/30',
  },
  [AlertStatus.ARCHIVED]: {
    value: AlertStatus.ARCHIVED,
    label: 'Archivado',
    icon: Clock,
    color: 'text-slate-600 dark:text-slate-400',
    bg: 'bg-slate-100 dark:bg-slate-900/30',
  },
};