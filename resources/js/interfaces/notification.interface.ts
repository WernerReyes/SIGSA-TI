export enum NotificationEntity {
    CONTRACT = 'CONTRACT',
}

export interface Notification {
    id: string;
    type: NotificationEntity;
    notifiable_type: string;
    entity_id: number;
    notifiable_id: number;
    data: string;
    read_at: string | null;
    created_at: Date;
    updated_at: Date;
}
