import { getEmptyEnumOption } from '@/lib/utils';
import { type EnumOption } from '@/types';
import {
    CalendarCheck,
    CalendarRange,
    CheckCircle,
    Clock,
    Cpu,
    HelpCircle,
    Key,
    LifeBuoy,
    Repeat,
    Wrench,
    XCircle,
} from 'lucide-vue-next';
import { type ContractBilling } from './contractBilling.interface';
import { ContractExpiration } from './contractExpiration.interface';
import { Notification } from './notification.interface';

export interface NotificationContract extends Notification {
    contract?: Contract;
}
export interface Contract {
    id: number;
    name: string;
    provider: string;
    type: ContractType;
    period: ContractPeriod;
    status: ContractStatus;
    start_date: string;
    end_date: string | null;
    notes: string | null;
    created_at: Date;
    updated_at: Date;
    billing?: ContractBilling;
    expiration?: ContractExpiration;
}

export enum ContractType {
    LICENSE = 'LICENSE',
    SERVICE = 'SERVICE',
    SUPPORT = 'SUPPORT',
    HARDWARE = 'HARDWARE',
    OTHER = 'OTHER',
}

export enum ContractPeriod {
    RECURRING = 'RECURRING',
    FIXED_TERM = 'FIXED_TERM',
    ONE_TIME = 'ONE_TIME',
}

export enum ContractStatus {
    ACTIVE = 'ACTIVE',
    EXPIRED = 'EXPIRED',
    CANCELED = 'CANCELED',
}

export const contractTypeOptions: Record<
    ContractType,
    EnumOption<ContractType>
> = {
    [ContractType.LICENSE]: {
        label: 'Licencia',
        value: ContractType.LICENSE,
        icon: Key,
        bg: 'bg-blue-500',
    },
    [ContractType.SERVICE]: {
        label: 'Servicio',
        value: ContractType.SERVICE,
        icon: Wrench,
        bg: 'bg-green-500',
    },
    [ContractType.SUPPORT]: {
        label: 'Soporte',
        value: ContractType.SUPPORT,
        icon: LifeBuoy,
        bg: 'bg-yellow-500',
    },
    [ContractType.HARDWARE]: {
        label: 'Hardware',
        value: ContractType.HARDWARE,
        icon: Cpu,
        bg: 'bg-purple-500',
    },
    [ContractType.OTHER]: {
        label: 'Otro',
        value: ContractType.OTHER,
        icon: HelpCircle,
        bg: 'bg-gray-500',
    },
};

export const contractPeriodOptions: Record<
    ContractPeriod,
    EnumOption<ContractPeriod>
> = {
    [ContractPeriod.RECURRING]: {
        label: 'Recurrente',
        value: ContractPeriod.RECURRING,
        icon: Repeat,
        bg: 'bg-indigo-500',
    },
    [ContractPeriod.FIXED_TERM]: {
        label: 'Plazo Fijo',
        value: ContractPeriod.FIXED_TERM,
        icon: CalendarRange,
        bg: 'bg-blue-500',
    },
    [ContractPeriod.ONE_TIME]: {
        label: 'Único',
        value: ContractPeriod.ONE_TIME,
        icon: CalendarCheck,
        bg: 'bg-emerald-500',
    },
};

export const contractStatusOptions: Record<
    ContractStatus,
    EnumOption<ContractStatus>
> = {
    [ContractStatus.ACTIVE]: {
        label: 'Activo',
        value: ContractStatus.ACTIVE,
        icon: CheckCircle,
        bg: 'bg-green-500',
    },
    [ContractStatus.EXPIRED]: {
        label: 'Expirado',
        value: ContractStatus.EXPIRED,
        icon: Clock,
        bg: 'bg-orange-500',
    },
    [ContractStatus.CANCELED]: {
        label: 'Cancelado',
        value: ContractStatus.CANCELED,
        icon: XCircle,
        bg: 'bg-red-500',
    },
};

export const getContractOp = <T>(
    type: 'type' | 'period' | 'status',
    op?: T | null,
): EnumOption<T | undefined | null> => {
    if (!op) return getEmptyEnumOption(op);
    switch (type) {
        case 'type':
            return contractTypeOptions[op as ContractType] as EnumOption<T>;
        case 'period':
            return contractPeriodOptions[op as ContractPeriod] as EnumOption<T>;
        case 'status':
            return contractStatusOptions[op as ContractStatus] as EnumOption<T>;
    }
};
