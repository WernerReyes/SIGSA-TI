import type { User } from "./user.interface";

export interface TicketHistory {
    id: number;
    action: string;
    ticket_id: number;
    performed_by: number;
    performer?: User;
    performed_at: Date;
}