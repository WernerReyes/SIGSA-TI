import { type TicketPriority } from "./ticket.interface";

export interface DashboardStats {
    open_tickets:{
        total: number;
        unassigned: number;
        trend_percentage: number;
        trend_direction: 'up' | 'down';
    };

    sla_breaches: {
        out_sla: number;
        severity: TicketPriority;
        message: string;
    };

    assets_at_risk: {
        total: number;
        new_assets: number;
        message: string;
    };

    active_projects: {
        total: number;
        in_development: number;
        trend_percentage: number;
        trend_direction: 'up' | 'down';
    };
}

export interface DashboardTicketsByPriority extends Record<TicketPriority, number> {}

export interface DashboardSLACompliance {
    range: {
        from: string;
        to: string;
    };
    daily: Array<{
        date: string;
        complied: number;
        breached: number;
        compliance_rate: number;
        breach_rate: number;
    }>;
}