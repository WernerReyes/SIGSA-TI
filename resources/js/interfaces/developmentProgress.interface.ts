import { type User } from "./user.interface";

export interface DevelopmentProgress {
    id: number;
    percentage: number;
    notes?: string;
    development_request_id: number;
    performed_by_id: number;
    performed_by: User,
    created_at: Date;
    updated_at: Date;
}

