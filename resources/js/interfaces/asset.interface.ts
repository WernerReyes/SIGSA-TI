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
