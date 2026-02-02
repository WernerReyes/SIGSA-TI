export interface DevelopmentProgress {
    id: number;
    percentage: number;
    notes?: string;
    development_request_id: number;
    performed_by: number;
    created_at: Date;
    updated_at: Date;
}

