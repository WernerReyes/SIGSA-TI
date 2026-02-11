import { type TicketPriority } from "./ticket.interface";

export interface SlaPolicy  {
    priority: TicketPriority;
    label: string;
    response_time_minutes: number;
    resolution_time_minutes: number;
};