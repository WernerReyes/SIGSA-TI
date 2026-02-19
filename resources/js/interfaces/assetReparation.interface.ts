export interface AssetReparation {
    id: number;
    asset_id: number;
    date: string; // ISO format date string
    description: string;
    image_paths: string[]; // Array of image paths related to the reparation
}
