import {
    ArrowDown,
    ArrowUp,
    BadgeCheck,
    CheckCircle2,
    Code2,
    FilePlus,
    Flame,
    FlaskConical,
    Minus,
    Search,
    XCircle,
} from 'lucide-vue-next';
import { type Component } from 'vue';
import { type Area } from './area.interface';
import { type DevelopmentApproval } from './developmentApproval.interface';
import { type User } from './user.interface';

export interface DevelopmentRequest {
    id: number;
    title: string;
    priority: DevelopmentRequestPriority;
    status: DevelopmentRequestStatus;
    description: string;
    impact?: string;
    // devs_needed?: number;
    estimated_hours?: number;
    estimated_end_date?: string;
    area_id: number;
    requirement_url?: string;
    requested_by_id: number;
    area?: Area;
    requested_by?: User;
    created_at: Date;
    updated_at: Date;
    technical_approval?: DevelopmentApproval;
    strategic_approval?: DevelopmentApproval;
    approvals?: DevelopmentApproval[];
}

export interface DevelopmentRequestSection {
   [key: string]: DevelopmentRequest[];
}

export enum DevelopmentRequestPriority {
    LOW = 'LOW',
    MEDIUM = 'MEDIUM',
    HIGH = 'HIGH',
    URGENT = 'URGENT',
}

export enum DevelopmentRequestStatus {
    REGISTERED = 'REGISTERED',
    IN_ANALYSIS = 'IN_ANALYSIS',
    APPROVED = 'APPROVED',
    IN_DEVELOPMENT = 'IN_DEVELOPMENT',
    IN_TESTING = 'IN_TESTING',
    COMPLETED = 'COMPLETED',
    REJECTED = 'REJECTED',
}

export type DevelopmentRequestPriorityOptions = {
    label: string;
    value: DevelopmentRequestPriority;
    bg: string;
    icon: Component;
};

export const developmentRequestPriorityOptions: Record<
    DevelopmentRequestPriority,
    DevelopmentRequestPriorityOptions
> = {
    [DevelopmentRequestPriority.LOW]: {
        label: 'Baja',
        value: DevelopmentRequestPriority.LOW,
        bg: 'bg-green-500',
        icon: ArrowDown,
    },
    [DevelopmentRequestPriority.MEDIUM]: {
        label: 'Media',
        value: DevelopmentRequestPriority.MEDIUM,
        bg: 'bg-yellow-500',
        icon: Minus,
    },
    [DevelopmentRequestPriority.HIGH]: {
        label: 'Alta',
        value: DevelopmentRequestPriority.HIGH,
        bg: 'bg-orange-500',
        icon: ArrowUp,
    },
    [DevelopmentRequestPriority.URGENT]: {
        label: 'Urgente',
        value: DevelopmentRequestPriority.URGENT,
        bg: 'bg-red-500',
        icon: Flame,
    },
};

export const getPriorityOp = (priority: DevelopmentRequestPriority) => {
    return developmentRequestPriorityOptions[priority];
};

export type DevelopmentRequestStatusOptions = {
    label: string;
    value: DevelopmentRequestStatus;
    bg: string;
    icon: Component;
};

export const DevelopmentRequestStatusOptions: Record<
    DevelopmentRequestStatus,
    DevelopmentRequestStatusOptions
> = {
    [DevelopmentRequestStatus.REGISTERED]: {
        label: 'Registrado',
        value: DevelopmentRequestStatus.REGISTERED,
        bg: 'bg-gray-100',
        icon: FilePlus,
    },
    [DevelopmentRequestStatus.IN_ANALYSIS]: {
        label: 'En Análisis',
        value: DevelopmentRequestStatus.IN_ANALYSIS,
        bg: 'bg-blue-500',
        icon: Search,
    },
    [DevelopmentRequestStatus.APPROVED]: {
        label: 'Aprobado',
        value: DevelopmentRequestStatus.APPROVED,
        bg: 'bg-teal-500',
        icon: CheckCircle2,
    },
    [DevelopmentRequestStatus.IN_DEVELOPMENT]: {
        label: 'En Desarrollo',
        value: DevelopmentRequestStatus.IN_DEVELOPMENT,
        bg: 'bg-yellow-500',
        icon: Code2,
    },
    [DevelopmentRequestStatus.IN_TESTING]: {
        label: 'En Pruebas',
        value: DevelopmentRequestStatus.IN_TESTING,
        bg: 'bg-purple-500',
        icon: FlaskConical,
    },
    [DevelopmentRequestStatus.COMPLETED]: {
        label: 'Completado',
        value: DevelopmentRequestStatus.COMPLETED,
        bg: 'bg-green-500',
        icon: BadgeCheck,
    },
    [DevelopmentRequestStatus.REJECTED]: {
        label: 'Rechazado',
        value: DevelopmentRequestStatus.REJECTED,
        bg: 'bg-red-500',
        icon: XCircle,
    },
};

export const getStatusOp = (status: DevelopmentRequestStatus) => {
    return DevelopmentRequestStatusOptions[status];
};
