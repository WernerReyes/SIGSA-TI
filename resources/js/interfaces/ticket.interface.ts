import type { User } from './user.interface';

export enum TicketStatus {
    OPEN = 'OPEN',
    IN_PROGRESS = 'IN_PROGRESS',
    RESOLVED = 'RESOLVED',
    CLOSED = 'CLOSED',
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
    HARDWARE = 'HARDWARE',
    SOFTWARE = 'SOFTWARE',
    NETWORK = 'NETWORK',
    OTHER = 'OTHER',
}

export interface Ticket {
    id: number;
    title: string;
    description: string;
    status: TicketStatus;
    priority: TicketPriority;
    type: TicketType;
    requester: User;
    technician?: User;
    request_type: TicketRequestType;
    created_at: Date;
    updated_at: Date;
    opened_at?: Date;
    closed_at?: Date;
    requester_id: number;
    technician_id?: number;
}

type TicketStatusOption = {
    label: string;
    value: TicketStatus;
    bg: string;

}
export const ticketStatusOptions: Record<TicketStatus, TicketStatusOption> = {
    [TicketStatus.OPEN]: { label: 'Abierto', value: TicketStatus.OPEN, bg: 'bg-blue-500' },
    [TicketStatus.IN_PROGRESS]: { label: 'En Progreso', value: TicketStatus.IN_PROGRESS, bg: 'bg-yellow-500' },
    [TicketStatus.RESOLVED]: { label: 'Resuelto', value: TicketStatus.RESOLVED, bg: 'bg-green-500' },
    [TicketStatus.CLOSED]: { label: 'Cerrado', value: TicketStatus.CLOSED, bg: 'bg-gray-500' },
};



type TicketTypeOption = {
    label: string;
    value: TicketType;
    bg: string;
};

export const ticketTypeOptions: Record<TicketType, TicketTypeOption> = {
    [TicketType.INCIDENT]: { label: 'Incidente', value: TicketType.INCIDENT, bg: 'bg-blue-500' },
    [TicketType.SERVICE_REQUEST]: {
        label: 'Solicitud de Servicio',
        value: TicketType.SERVICE_REQUEST,
        bg: 'bg-green-500'
    },
};


type TicketPriorityOption = {
    label: string;
    value: TicketPriority;
    bg: string;
    search: string ;
};

export const ticketPriorityOptions: Record<TicketPriority, TicketPriorityOption> = {
    [TicketPriority.LOW]: { label: 'Baja', value: TicketPriority.LOW, bg: 'bg-green-500', search: 'baja' },
    [TicketPriority.MEDIUM]: { label: 'Media', value: TicketPriority.MEDIUM, bg : 'bg-yellow-500', search: 'media' },
    [TicketPriority.HIGH]: { label: 'Alta', value: TicketPriority.HIGH, bg: 'bg-orange-500', search: 'alta' },
    [TicketPriority.URGENT]: { label: 'Crítica', value: TicketPriority.URGENT, bg: 'bg-red-500', search: 'critica' },
};

type TicketRequestTypeOption = {
    label: string;
    value: TicketRequestType;
    bg: string;
};

export const ticketRequestTypeOptions: Record<TicketRequestType, TicketRequestTypeOption> = {
    [TicketRequestType.HARDWARE]: { label: 'Hardware', value: TicketRequestType.HARDWARE, bg: 'bg-blue-500' },
    [TicketRequestType.SOFTWARE]: { label: 'Software', value: TicketRequestType.SOFTWARE, bg: 'bg-green-500' },
    [TicketRequestType.NETWORK]: { label: 'Red', value: TicketRequestType.NETWORK, bg: 'bg-yellow-500' },
    [TicketRequestType.OTHER]: { label: 'Otro', value: TicketRequestType.OTHER, bg: 'bg-gray-500' },
};