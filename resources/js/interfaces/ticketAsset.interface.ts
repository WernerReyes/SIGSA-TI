export interface TicketAsset {
    id: number;
    ticket_id: number;
    asset_id: number;
    asset_assignment_id: number;
    action: 'ASSIGNED' | 'UNASSIGNED';
    performed_by: number;
    notes?: string;
}

