import type {
    TicketCategory,
    TicketPriority,
    TicketStatus,
    TicketType,
} from './ticket.interface';
import type { BasicUserInfo } from './user.interface';

export interface TicketDashboardDistributionItem {
    value: string;
    label: string;
    count: number;
    percentage: number;
}

export interface TicketDashboardTicket {
    id: number;
    title: string;
    status: TicketStatus;
    priority: TicketPriority;
    type: TicketType;
    category: TicketCategory | null;
    requester_id: number;
    responsible_id: number | null;
    requester: BasicUserInfo;
    responsible: BasicUserInfo | null;
    created_at: string;
}

export interface TicketDashboardData {
    filters: {
        start_date: string | null;
        end_date: string | null;
        responsible_id: number | null;
        requester_id: number | null;
        status: TicketStatus | null;
        type: TicketType | null;
        category: TicketCategory | null;
    };
    summary: {
        total: number;
        active: number;
        resolved: number;
        closed: number;
        unassigned: number;
        sla_breached: number;
        sla_compliance_rate: number;
        average_resolution_minutes: number;
    };
    by_status: Array<TicketDashboardDistributionItem & { value: TicketStatus }>;
    by_priority: Array<
        TicketDashboardDistributionItem & { value: TicketPriority }
    >;
    by_type: Array<TicketDashboardDistributionItem & { value: TicketType }>;
    by_category: Array<
        TicketDashboardDistributionItem & {
            value: TicketCategory | 'UNCATEGORIZED';
        }
    >;
    daily_trend: Array<{
        date: string;
        created: number;
        resolved: number;
    }>;
    technicians: Array<{
        staff_id: number;
        name: string;
        total: number;
        resolved: number;
        active: number;
        sla_breached: number;
        resolution_rate: number;
    }>;
    tickets: TicketDashboardTicket[];
    recent_tickets: TicketDashboardTicket[];
}

export interface TicketDashboardFilterOptions {
    responsibles: BasicUserInfo[];
    statuses: Array<{ value: TicketStatus; label: string }>;
    types: Array<{ value: TicketType; label: string }>;
    categories: Array<{ value: TicketCategory; label: string }>;
}
