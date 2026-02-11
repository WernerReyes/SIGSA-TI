import { getEmptyEnumOption } from '@/lib/utils';
import { EnumOption } from '@/types';
import {
    Archive,
    ArrowDown,
    ArrowUp,
    Bug,
    CheckCircle,
    Code2,
    Flame,
    FolderOpen,
    Key,
    LifeBuoy,
    LoaderCircle,
    Minus,
    MonitorSmartphone,
} from 'lucide-vue-next';
import { Component } from 'vue';
import { AssetAssignment } from './assetAssignment.interface';
import type { TicketHistory } from './ticketHistory.interface';
import type { User } from './user.interface';

export enum TicketStatus {
    OPEN = 'OPEN',
    IN_PROGRESS = 'IN_PROGRESS',
    RESOLVED = 'RESOLVED',
    CLOSED = 'CLOSED',
}

export enum TicketImpact {
    LOW = 'LOW',
    MEDIUM = 'MEDIUM',
    HIGH = 'HIGH',
}

export enum TicketUrgency {
    LOW = 'LOW',
    MEDIUM = 'MEDIUM',
    HIGH = 'HIGH',
}

export enum TicketPriority {
    LOW = 'LOW',
    MEDIUM = 'MEDIUM',
    HIGH = 'HIGH',
    URGENT = 'URGENT',
}

export enum TicketType {
    INCIDENT = 'INCIDENT',
    SERVICE_REQUEST = 'SERVICE_REQUEST',
}

export enum TicketRequestType {
    SOFTWARE = 'SOFTWARE',
    ACCESS = 'ACCESS',
    EQUIPMENT = 'EQUIPMENT',
}

export enum TicketCategory {
    SOFTWARE = 'SOFTWARE',
    ACCESS = 'ACCESS',
    EQUIPMENT = 'EQUIPMENT',
}

export interface Ticket {
    id: number;
    title: string;
    description: string;

    impact: TicketImpact;
    urgency: TicketUrgency;
    status: TicketStatus;
    priority: TicketPriority;
    type: TicketType;
    requester: User;
    // technician?: User;
    category: TicketCategory;

    image_urls?: string[];



    // request_type: TicketRequestType;
    // created_at: Date;
    // updated_at: Date;
    // opened_at?: Date;
    // asset_events?: TicketAsset[];
    current_asset_assignment?: AssetAssignment;
    histories?: TicketHistory[];
    closed_at?: Date;
    requester_id: number;
    responsible_id?: number;
    responsible?: User;


    sla_response_due_at?: Date;
    sla_resolution_due_at?: Date;

    first_response_at?: Date;
    resolved_at?: Date;

    created_at: Date;
    updated_at: Date;
}

export type TicketStatusOption = {
    label: string;
    value: TicketStatus;
    bg: string;
    icon: Component;
};
export const ticketStatusOptions: Record<TicketStatus, TicketStatusOption> = {
    [TicketStatus.OPEN]: {
        label: 'Abierto',
        value: TicketStatus.OPEN,
        bg: 'bg-blue-500',
        icon: FolderOpen,
    },
    [TicketStatus.IN_PROGRESS]: {
        label: 'En espera del Proveedor',
        value: TicketStatus.IN_PROGRESS,
        bg: 'bg-yellow-500',
        icon: LoaderCircle,
    },
    [TicketStatus.RESOLVED]: {
        label: 'Resuelto',
        value: TicketStatus.RESOLVED,
        bg: 'bg-green-500',
        icon: CheckCircle,
    },
    [TicketStatus.CLOSED]: {
        label: 'Cerrado',
        value: TicketStatus.CLOSED,
        bg: 'bg-gray-500',
        icon: Archive,
    },
};

export const statusOp = (
    status?: TicketStatus,
): TicketStatusOption | undefined => {
    if (!status) return undefined;
    return ticketStatusOptions[status];
};

type TicketTypeOption = {
    label: string;
    value: TicketType;
    bg: string;
    icon: Component;
};

export const ticketTypeOptions: Record<TicketType, TicketTypeOption> = {
    [TicketType.INCIDENT]: {
        label: 'Incidente',
        value: TicketType.INCIDENT,
        bg: 'bg-blue-500',
        icon: Bug,
    },
    [TicketType.SERVICE_REQUEST]: {
        label: 'Solicitud de Servicio',
        value: TicketType.SERVICE_REQUEST,
        bg: 'bg-green-500',
        icon: LifeBuoy,
    },
};

export const typeOp = (type?: TicketType): TicketTypeOption | undefined => {
    if (!type) return undefined;
    return ticketTypeOptions[type];
};

type TicketPriorityOption = {
    label: string;
    value: TicketPriority;
    bg: string;
    // search: string;
    icon: Component;
};

export const ticketPriorityOptions: Record<
    TicketPriority,
    TicketPriorityOption
> = {
    [TicketPriority.LOW]: {
        label: 'Baja',
        value: TicketPriority.LOW,
        bg: 'bg-green-500',
        // search: 'baja',
        icon: ArrowDown,
    },
    [TicketPriority.MEDIUM]: {
        label: 'Media',
        value: TicketPriority.MEDIUM,
        bg: 'bg-sky-500',
        // search: 'media',
        icon: Minus,
    },
    [TicketPriority.HIGH]: {
        label: 'Alta',
        value: TicketPriority.HIGH,
        bg: 'bg-amber-500',
        // search: 'alta',
        icon: ArrowUp,
    },
    [TicketPriority.URGENT]: {
        label: 'Crítica',
        value: TicketPriority.URGENT,
        bg: 'bg-red-500',
        // search: 'critica',
        icon: Flame,
    },
};

export const priorityOp = (
    priority?: TicketPriority,
): TicketPriorityOption | undefined => {
    if (!priority) return undefined;
    return ticketPriorityOptions[priority];
};

type TicketRequestTypeOption = {
    label: string;
    value: TicketRequestType;
    bg: string;
    icon: Component;
};

export const ticketRequestTypeOptions: Record<
    TicketRequestType,
    TicketRequestTypeOption
> = {
    [TicketRequestType.EQUIPMENT]: {
        label: 'Equipo',
        value: TicketRequestType.EQUIPMENT,
        bg: 'bg-blue-500',
        icon: MonitorSmartphone,
    },
    [TicketRequestType.SOFTWARE]: {
        label: 'Software',
        value: TicketRequestType.SOFTWARE,
        bg: 'bg-green-500',
        icon: Code2,
    },
    [TicketRequestType.ACCESS]: {
        label: 'Acceso',
        value: TicketRequestType.ACCESS,
        bg: 'bg-yellow-500',
        icon: Key,
    },
};

export const ticketCategoryOptions: Record<
    TicketCategory,
    EnumOption<TicketCategory>
> = {
    [TicketCategory.EQUIPMENT]: {
        label: 'Equipo',
        value: TicketCategory.EQUIPMENT,
        bg: 'bg-blue-500',
        icon: MonitorSmartphone,
    },
    [TicketCategory.SOFTWARE]: {
        label: 'Software',
        value: TicketCategory.SOFTWARE,
        bg: 'bg-green-500',
        icon: Code2,
    },
    [TicketCategory.ACCESS]: {
        label: 'Acceso',
        value: TicketCategory.ACCESS,
        bg: 'bg-yellow-500',
        icon: Key,
    },
};

export const requestTypeOp = (
    requestType?: TicketRequestType,
): TicketRequestTypeOption | undefined => {
    if (!requestType) return undefined;
    return ticketRequestTypeOptions[requestType];
};

export const getTicketOp = <T>(
    type: 'type' | 'status' | 'priority' | 'category',
    op?: T | null,
): EnumOption<T | undefined | null> => {
    if (!op) return getEmptyEnumOption(op);
    switch (type) {
        case 'type':
            return ticketTypeOptions[op as TicketType] as EnumOption<
                T | undefined | null
            >;

        case 'status':
            return ticketStatusOptions[op as TicketStatus] as EnumOption<
                T | undefined | null
            >;

        case 'priority':
            return ticketPriorityOptions[op as TicketPriority] as EnumOption<
                T | undefined | null
            >;

        case 'category':
            return ticketCategoryOptions[op as TicketCategory] as EnumOption<
                T | undefined | null
            >;

        default:
            return getEmptyEnumOption(op);
    }
};
