import { type AssetStatus } from './asset.interface';
import { type Asset } from './asset.interface';
import { type TicketPriority } from './ticket.interface';

export interface DashboardStats {
    open_tickets: {
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

export interface DashboardTicketsByPriority extends Record<
    TicketPriority,
    number
> {}

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

export interface RRHHDashboard {
    stats: {
        total: number;
        smartphones: number;
        chargers: number;
        decommissioned: number;
        expired_warranty: number;
        assigned: number;
        under_maintenance: number;
    };
    assetsByBrand: Array<{
        brand: string;
        total: number;
    }>;
    assetsByStatus: Record<AssetStatus, {
        smartphones: number;
        chargers: number;
        total: number;
       
    }>;
    smartphonesWarrantyStatus: Record<'in_warranty' | 'expiring_soon' | 'expired' | 'unknown', number>;
    assignedProfiRate: number;
    monthlyAssignments: Array<{
        month: number;
        label: string;
        smartphones: number;
        chargers: number;
    }>;
    monthlyAcquisitions: Array<{
        month: number;
        label: string;
        smartphones: number;
        chargers: number;
    }>;
    assetsByDepartment: Array<{
        department: string;
        smartphones: number;
        chargers: number;
    }>;
    recentAssets: Asset[];
    brands: Array<{
        id: number;
        name: string;
    }>;
    activeFilters: {
        search: string | null;
        brand: number | null;
        status: AssetStatus | null;
        warranty: 'in_warranty' | 'expiring_soon' | 'expired' | 'unknown' | null;
        start_date: string | null;
        end_date: string | null;
    };

}
