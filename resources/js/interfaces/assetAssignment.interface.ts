import { type User } from "./user.interface";

export interface AssetAssignment {
    id: number;
    asset_id: number;
    assigned_to_id: number;
    assigned_at: string;
    current_owner: boolean;
    comment?: string;
    assigned_to?: User;
}
