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
    requestType: TicketRequestType;
    createdAt: Date;
    updatedAt: Date;
    openedAt?: Date;
    closedAt?: Date;
    requesterId: number;
    technicianId?: number;
}

type TicketTypeOption = {
    label: string;
    value: TicketType;
};

export const ticketTypeOptions: Record<string, TicketTypeOption> = {
    [TicketType.INCIDENT]: { label: 'Incidente', value: TicketType.INCIDENT },
    [TicketType.SERVICE_REQUEST]: {
        label: 'Solicitud de Servicio',
        value: TicketType.SERVICE_REQUEST,
    },
};
