import { type AssetAssignment } from "./assetAssignment.interface";
import { type AssetHistory } from "./assetHistory.interface";


export interface AssetType {
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
}

export interface Asset {
    id: number;
    type_id: number;
    color?: string;
    model: string;
    serial_number: string;
    processor: string;
    ram: string;
    storage: string;
    purchase_date: string;
    warranty_expiration: string;
    status: AssetStatus;
    brand: string;
    created_at: Date;
    updated_at: Date;
    name: string;
    description: string;
    type?: AssetType;
    is_new: boolean;
    // assigned_to_id?: number | null;
    current_assignment?: AssetAssignment;
    assignments?: AssetAssignment[];
    histories?: AssetHistory[];
}

export interface AssetsPaginated {
    current_page: number;
    data: Asset[];
    next_page_url: string | null;
    per_page: number;
    prev_page_url: string | null;
    total: number;
    path: string;
    to: number;
    links: {
        url: string;
        label: string;
        active: boolean;
    }[]
    // first_page_url: string;
    // from: number;
    // next_page_url: string | null;
    // path: string;
    // per_page: number;
    // prev_page_url: string | null;
    // to: number;
    // current_page_url: string;
}

export enum AssetStatus {
    AVAILABLE = 'AVAILABLE',
    ASSIGNED = 'ASSIGNED',
    IN_REPAIR = 'IN_REPAIR',
    DECOMMISSIONED = 'DECOMMISSIONED',
}
export type AssetStatusOption = {
    label: string;
    value: AssetStatus;
    bg: string;
};
export const assetStatusOptions: Record<AssetStatus, AssetStatusOption> = {
    [AssetStatus.AVAILABLE]: {
        label: 'Disponible',
        value: AssetStatus.AVAILABLE,
        bg: 'bg-green-500',
    },
    [AssetStatus.ASSIGNED]: {
        label: 'Asignado',
        value: AssetStatus.ASSIGNED,
        bg: 'bg-blue-500',
    },
    [AssetStatus.IN_REPAIR]: {
        label: 'En Reparación',
        value: AssetStatus.IN_REPAIR,
        bg: 'bg-yellow-500',
    },
    [AssetStatus.DECOMMISSIONED]: {
        label: 'Dado de Baja',
        value: AssetStatus.DECOMMISSIONED,
        bg: 'bg-red-500',
    },
};

export const statusOp = (status: AssetStatus): AssetStatusOption => {
    return assetStatusOptions[status];
}