export interface Service {
    id: number;
    name: string;
    description?: string | null;
    url: string;
    username: string;
    password: string;
    is_active: boolean;

    created_at: Date;
    updated_at: Date;
}
