import {type DeliveryRecord } from './deliveryRecord.interface';
import { type User } from './user.interface';

export interface AssetAssignment {
    id: number;
    asset_id: number;
    assigned_to_id: number;
    assigned_at: string;
    returned_at?: string | null;
    
    responsible_id?: number | null;

    comment?: string;
    return_comment?: string | null;


    assigned_to?: User;
    delivery_document?: DeliveryRecord | null;
    return_document?: DeliveryRecord | null;
    
}
