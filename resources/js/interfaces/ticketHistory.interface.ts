import {
    RefreshCcw,
    SquarePen,
    SquarePlus,
    TicketCheck,
} from 'lucide-vue-next';
import { type Component } from 'vue';
import type { User } from './user.interface';

export enum TicketHistoryAction {
    CREATED = 'CREATED',
    UPDATED = 'UPDATED',
    STATUS_CHANGED = 'STATUS_CHANGED',
    PRIORITY_CHANGED = 'PRIORITY_CHANGED',
    ASSIGNED = 'ASSIGNED',
}

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
        label: 'Cambio de Estado',
        bg: 'bg-yellow-500',
        icon: RefreshCcw,
    },
    [TicketHistoryAction.PRIORITY_CHANGED]: {
        value: TicketHistoryAction.PRIORITY_CHANGED,
        label: 'Cambio de Prioridad',
        bg: 'bg-purple-500',
        icon: RefreshCcw,
    },
    [TicketHistoryAction.ASSIGNED]: {
        value: TicketHistoryAction.ASSIGNED,
        label: 'Asignación',
        bg: 'bg-indigo-500',
        icon: TicketCheck,
    },
};

export const actionOp = (action: TicketHistoryAction) =>
    ticketHistoryActionOptions[action];
