export interface AssetModel {
    id: number;
    name: string;
    brand_id: number | null;
    asset_type_id: number | null;
    brand?: {
        id: number;
        name: string;
    };
    type?: {
        id: number;
        name: string;
    };
    created_at: Date;
    updated_at: Date;
}
