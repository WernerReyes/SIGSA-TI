export interface Brand {
    id: number;
    name: string;
    type_id: number | null;
    type?: {
        id: number;
        name: string;
    };
    created_at: Date;
    updated_at: Date;
}
