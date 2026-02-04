export interface ContractExpiration {
    id: number;
    contract_id: number;
    expiration_date: string;
    alert_days_before: number;
    notified: boolean;
    created_at: Date;
    updated_at: Date;
}
