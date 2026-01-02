import type { User } from '@/types';

export interface Department {
    id: number;
    pid?: number;
    tpl_id: number;
    sla_id: number;
    schedule_id: number;
    email_id: number;
    autoresp_email_id: number;
    manager_id: number;
    flags: number;
    name: string;
    signature: string;
    ispublic: boolean;
    group_membership: boolean;
    ticket_auto_response: boolean;
    message_auto_response: boolean;
    path: string;
    users: Array<User>;
    updated: Date;
    created: Date;
}

export enum DepartmentAllowed {
    SYSTEM_TI = 11,
}
