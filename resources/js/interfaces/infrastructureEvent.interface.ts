import { getEmptyEnumOption } from '@/lib/utils';
import { EnumOption } from '@/types';
import { User } from './user.interface';
import { ClipboardCheck, Flame, RefreshCcw, Repeat, ShieldAlert, Wrench } from 'lucide-vue-next';

export enum InfrastructureEventType {
    CHANGE = 'CHANGE',
    MAINTENANCE = 'MAINTENANCE',
    SECURITY = 'SECURITY',
    AUDIT = 'AUDIT',
    INCIDENT = 'INCIDENT',
    RENEWAL = 'RENEWAL',
}

export interface InfrastructureEvent {
    id: number;
    title: string;
    type: InfrastructureEventType;
    description: string;
    date: Date;
    created_at: Date;
    updated_at: Date;
    responsible_id: number;
    responsible?: User;
}

export const infrastructureEventTypeOptions: Record<
  InfrastructureEventType,
  EnumOption<InfrastructureEventType>
> = {
  [InfrastructureEventType.CHANGE]: {
    label: 'Cambio',
    value: InfrastructureEventType.CHANGE,
    icon: RefreshCcw,
    bg: 'bg-blue-500',
  },
  [InfrastructureEventType.MAINTENANCE]: {
    label: 'Mantenimiento',
    value: InfrastructureEventType.MAINTENANCE,
    icon: Wrench,
    bg: 'bg-yellow-500',
  },
  [InfrastructureEventType.SECURITY]: {
    label: 'Seguridad',
    value: InfrastructureEventType.SECURITY,
    icon: ShieldAlert,
    bg: 'bg-red-500',
  },
  [InfrastructureEventType.AUDIT]: {
    label: 'Auditoría',
    value: InfrastructureEventType.AUDIT,
    icon: ClipboardCheck,
    bg: 'bg-purple-500',
  },
  [InfrastructureEventType.INCIDENT]: {
    label: 'Incidente',
    value: InfrastructureEventType.INCIDENT,
    icon: Flame,
    bg: 'bg-orange-500',
  },
  [InfrastructureEventType.RENEWAL]: {
    label: 'Renovación',
    value: InfrastructureEventType.RENEWAL,
    icon: Repeat,
    bg: 'bg-green-500',
  },
}


export const infrastructureEventTypeOptionsArray: EnumOption<InfrastructureEventType>[] =
    Object.values(infrastructureEventTypeOptions);

export const getInfrasEventOp = <T>(
    type: 'type',
    op?: T | null,
): EnumOption<T | undefined | null> => {
    if (!op) return getEmptyEnumOption(op);
    switch (type) {
        case 'type':
            return infrastructureEventTypeOptions[
                op as InfrastructureEventType
            ] as EnumOption<T | undefined | null>;
        default:
            return getEmptyEnumOption(op);
    }
};
