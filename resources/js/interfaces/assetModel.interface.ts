export interface AssetModel {
    id: number;
    name: string;
    brand_id: number | null;
    brand?: {
        id: number;
        name: string;
        type_id?: number | null;
        type?: {
            id: number;
            name: string;
        };
    };
    created_at: Date;
    updated_at: Date;
}
