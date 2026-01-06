import {
    LucideProps,
    MonitorCheck,
    MonitorOff,
    RefreshCcw,
    SquarePen,
    SquarePlus,
} from 'lucide-vue-next';
import type { FunctionalComponent } from 'vue';
import { type DeliveryRecord } from './deliveryRecord.interface';
import type { User } from './user.interface';

export interface AssetHistory {
    id: number;
    asset_id: number;
    action: AssetHistoryAction;
    description: string;
    performed_by: number;
    performed_at: string;
    performer?: User;
    related_assignment_id?: number | null;
    delivery_record?: DeliveryRecord | null;
}

export enum AssetHistoryAction {
    CREATED = 'CREATED',
    UPDATED = 'UPDATED',
    STATUS_CHANGED = 'STATUS_CHANGED',
    ASSIGNED = 'ASSIGNED',
    RETURNED = 'RETURNED',
    DELIVERY_RECORD_UPLOADED = 'DELIVERY_RECORD_UPLOADED',
}

export type AssetHistoryActionOption = {
    label: string;
    value: AssetHistoryAction;
    icon: FunctionalComponent<LucideProps, any>;
    bg: string;
};

export const assetHistoryActionOptions: Record<
    AssetHistoryAction,
    AssetHistoryActionOption
> = {
    [AssetHistoryAction.CREATED]: {
        label: 'Creado',
        value: AssetHistoryAction.CREATED,
        icon: SquarePlus,
        bg: 'bg-green-500',
    },
    [AssetHistoryAction.UPDATED]: {
        label: 'Actualizado',
        value: AssetHistoryAction.UPDATED,
        icon: SquarePen,
        bg: 'bg-blue-500',
    },
    [AssetHistoryAction.STATUS_CHANGED]: {
        label: 'Estado Cambiado',
        value: AssetHistoryAction.STATUS_CHANGED,
        icon: RefreshCcw,
        bg: 'bg-yellow-500',
    },
    [AssetHistoryAction.ASSIGNED]: {
        label: 'Asignado',
        value: AssetHistoryAction.ASSIGNED,
        icon: MonitorCheck,
        bg: 'bg-purple-500',
    },
    [AssetHistoryAction.RETURNED]: {
        label: 'Devuelto',
        value: AssetHistoryAction.RETURNED,
        icon: MonitorOff,
        bg: 'bg-red-500',
    },
    [AssetHistoryAction.DELIVERY_RECORD_UPLOADED]: {
        label: 'Archivo Subido',
        value: AssetHistoryAction.DELIVERY_RECORD_UPLOADED,
        icon: MonitorCheck,
        bg: 'bg-teal-500',
    },
};

export const actionOp = (
    action: AssetHistoryAction,
): AssetHistoryActionOption => {
    return assetHistoryActionOptions[action];
};
