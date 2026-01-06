export interface DeliveryRecord {
    id: number;
    asset_id: number;
    file_path: string;
    file_url: string;
    type: DeliveryRecordType;
    // uploaded_at: string;
    created_at: Date;
    updated_at: Date;
}

export enum DeliveryRecordType {
    ASSIGNMENT = 'ASSIGNMENT',
    DEVOLUTION = 'DEVOLUTION',
}
