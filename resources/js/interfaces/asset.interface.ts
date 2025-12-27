

export interface AssetType {
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
}

export interface Asset {
    id_assets: number;
    type_id: number;
    model: string;
    serial_number: number;
    purchase_date: Date;
    warranty_end: Date;
    status: AssetStatus;
    brand: string;
    created_at: Date;
    updated_at: Date;
    name: string;
    description: string;
    type?: AssetType;
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

