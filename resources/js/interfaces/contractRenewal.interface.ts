import { type BasicUserInfo } from './user.interface';

export interface ContractRenewal {
    id: number;
    contract_id: number;
    old_end_date: string;
    new_end_date: string;
    renewed_by_id: number | null; //* If is null, it means it was an automatic renewal
    renewed_by?: BasicUserInfo | null;
    notes: string | null;
    created_at: Date;
    updated_at: Date;
}
