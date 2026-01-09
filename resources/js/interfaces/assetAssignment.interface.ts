import { type Component } from 'vue';
import {type DeliveryRecord } from './deliveryRecord.interface';
import { type User } from './user.interface';
import { RefreshCcw, UserX, Wrench } from 'lucide-vue-next';

export interface AssetAssignment {
    id: number;
    asset_id: number;
    assigned_to_id: number;
    assigned_at: string;
    returned_at?: string | null;
    
    responsible_id?: number | null;

    accesory_id?: number | null;

    comment?: string;
    return_comment?: string | null;

    return_reason?: ReturnReason | null;

    assigned_to?: User;
    delivery_document?: DeliveryRecord | null;
    return_document?: DeliveryRecord | null;
    
}

export enum ReturnReason {
   
    TERMINATION_EMPLOYMENT = 'TERMINATION_EMPLOYMENT',
    TECHNICAL_ISSUES = 'TECHNICAL_ISSUES',
    EQUIPMENT_RENOVATION = 'EQUIPMENT_RENOVATION',
}

export interface ReturnReasonOption {
    label: string;
    value: ReturnReason;
    icon: Component;
}

export const returnReasonOptions: Record<ReturnReason, ReturnReasonOption> = {
    [ReturnReason.TERMINATION_EMPLOYMENT]: {
        label: 'Cese laboral',
        value: ReturnReason.TERMINATION_EMPLOYMENT,
        icon: UserX
    },
    [ReturnReason.TECHNICAL_ISSUES]: {
        label: 'Servicio técnico',
        value: ReturnReason.TECHNICAL_ISSUES,
        icon: Wrench
    },
    [ReturnReason.EQUIPMENT_RENOVATION]: {
        label: 'Renovación de equipo',
        value: ReturnReason.EQUIPMENT_RENOVATION,
        icon: RefreshCcw
    },
}


export const returnReasonOp = (reason: ReturnReason): ReturnReasonOption => {
    return returnReasonOptions[reason];
}