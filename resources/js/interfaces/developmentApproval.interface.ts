import { CheckCircle, CircleAlert, XCircle } from 'lucide-vue-next';
import type { Component } from 'vue';
import type { User } from './user.interface';

export interface DevelopmentApproval {
    id: number;
    level: DevelopmentApprovalLevel;
    status: DevelopmentApprovalStatus;
    comments?: string;
    development_request_id: number;
    approved_by_id: number;
    created_at: Date;
    updated_at: Date;
    approved_by?: User;
}

export enum DevelopmentApprovalLevel {
    TECHNICAL = 'TECHNICAL',
    MANAGEMENT = 'MANAGEMENT',
}

export enum DevelopmentApprovalStatus {
    APPROVED = 'APPROVED',
    REJECTED = 'REJECTED',
}

interface DevelopmentApprovalStatusOption {
    label: string;
    value: DevelopmentApprovalStatus;
    bg: string;
    icon: Component;
    color: string;
}

export const developmentApprovalStatusOptions: Record<
    DevelopmentApprovalStatus,
    DevelopmentApprovalStatusOption
> = {
    [DevelopmentApprovalStatus.APPROVED]: {
        label: 'Aprobado',
        value: DevelopmentApprovalStatus.APPROVED,
        bg: 'bg-green-500',
        icon: CheckCircle,
        color: 'text-green-500',
    },
    [DevelopmentApprovalStatus.REJECTED]: {
        label: 'Rechazado',
        value: DevelopmentApprovalStatus.REJECTED,
        bg: 'bg-red-500',
        icon: XCircle,
        color: 'text-red-500',
    },
};

export const getStatusOp = (status?: DevelopmentApprovalStatus) => {
    return status ? developmentApprovalStatusOptions[status] : {
        label: 'Pendiente',
        value: undefined,
        bg: 'bg-gray-500',
        icon: CircleAlert,
        color: 'text-gray-500',
    };
};
