import { CheckCircle, Trash2, UserCheck, Wrench } from 'lucide-vue-next';
import { type Component } from 'vue';
import { type AssetAssignment } from './assetAssignment.interface';
import { type AssetHistory } from './assetHistory.interface';
import { type AssetType } from './assetType.interface';


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
    invoice_url?: string;
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
    last_page: number;
    links: {
        url: string;
        label: string;
        active: boolean;
    }[];
    // first_page_url: string;
    // from: number;
    // next_page_url: string | null;
    // path: string;
    // per_page: number;
    // prev_page_url: string | null;
    // to: number;
    // current_page_url: string;
}

export interface AssetStats {
    total: number;
    statuses: Record<AssetStatus, number>;
    not_expired_warranty: number;
    expired_warranty: number;
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
    icon: Component;
};
export const assetStatusOptions: Record<AssetStatus, AssetStatusOption> = {
    [AssetStatus.AVAILABLE]: {
        label: 'Disponible',
        value: AssetStatus.AVAILABLE,
        bg: 'bg-green-500',
        icon: CheckCircle,
    },
    [AssetStatus.ASSIGNED]: {
        label: 'Asignado',
        value: AssetStatus.ASSIGNED,
        bg: 'bg-blue-500',
        icon: UserCheck,
    },
    [AssetStatus.IN_REPAIR]: {
        label: 'En Reparación',
        value: AssetStatus.IN_REPAIR,
        bg: 'bg-yellow-500',
        icon: Wrench,
    },
    [AssetStatus.DECOMMISSIONED]: {
        label: 'Dado de Baja',
        value: AssetStatus.DECOMMISSIONED,
        bg: 'bg-red-500',
        icon: Trash2,
    },
};

export const statusOp = (status: AssetStatus): AssetStatusOption => {
    return assetStatusOptions[status];
};
