import {
    MonitorCheck,
    MonitorX,
    RefreshCcw,
    SquarePen,
    SquarePlus,
    TicketCheck,
    TicketMinus,
    UserCheck,
} from 'lucide-vue-next';
import { type Component } from 'vue';
import type { User } from './user.interface';

export enum TicketHistoryAction {
    CREATED = 'CREATED',
    UPDATED = 'UPDATED',
    STATUS_CHANGED = 'STATUS_CHANGED',
    RESPONSIBLE_CHANGED = 'RESPONSIBLE_CHANGED',
    // PRIORITY_CHANGED = 'PRIORITY_CHANGED',


    ASSET_ASSIGNED = 'ASSET_ASSIGNED',
    ASSET_RETURNED = 'ASSET_RETURNED',
}

//  case CREATED = 'CREATED';
//     case UPDATED = 'UPDATED';
//     case STATUS_CHANGED = 'STATUS_CHANGED';

//     case RESPONSIBLE_CHANGED = 'RESPONSIBLE_CHANGED';
//     // case PRIORITY_CHANGED = 'PRIORITY_CHANGED';
//     case ASSET_ASSIGNED = 'ASSET_ASSIGNED';
//     case ASSET_RETURNED = 'ASSET_RETURNED';
export interface TicketHistory {
    id: number;
    action: TicketHistoryAction;
    description: string;
    ticket_id: number;
    performed_by: number;
    performer?: User;
    performed_at: Date;
}
export type TicketHistoryActionOption = {
    value: TicketHistoryAction;
    label: string;
    bg: string;
    icon: Component;
};

export const ticketHistoryActionOptions: Record<
    TicketHistoryAction,
    TicketHistoryActionOption
> = {
    [TicketHistoryAction.CREATED]: {
        value: TicketHistoryAction.CREATED,
        label: 'Creación',
        bg: 'bg-green-500',
        icon: SquarePlus,
    },
    [TicketHistoryAction.UPDATED]: {
        value: TicketHistoryAction.UPDATED,
        label: 'Actualización',
        bg: 'bg-blue-500',
        icon: SquarePen,
    },
    [TicketHistoryAction.STATUS_CHANGED]: {
        value: TicketHistoryAction.STATUS_CHANGED,
        label: 'Estado',
        bg: 'bg-yellow-500',
        icon: RefreshCcw,
    },

    [TicketHistoryAction.RESPONSIBLE_CHANGED]: {
        value: TicketHistoryAction.RESPONSIBLE_CHANGED,
        label: 'Responsable',
        bg: 'bg-purple-500',
        icon: UserCheck,
    },
    
    [TicketHistoryAction.ASSET_ASSIGNED]: {
        value: TicketHistoryAction.ASSET_ASSIGNED,
        label: 'Asignación',
        bg: 'bg-indigo-500',
        icon: MonitorCheck,
    },

    [TicketHistoryAction.ASSET_RETURNED]: {
        value: TicketHistoryAction.ASSET_RETURNED,
        label: 'Devolución',
        bg: 'bg-red-500',
        icon: MonitorX,
    },

};

export const actionOp = (action: TicketHistoryAction) =>
    ticketHistoryActionOptions[action];
