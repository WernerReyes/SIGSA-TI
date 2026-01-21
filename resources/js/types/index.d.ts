import { User } from '@/interfaces/user.interface';
import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

// export interface User {
//     id: number;
//     name: string;
//     email: string;
//     avatar?: string;
//     email_verified_at: string | null;
//     created_at: string;
//     updated_at: string;
// }

export type BreadcrumbItemType = BreadcrumbItem;
// export interface User {
//     staff_id: number;
//     full_name: string;
//     dept_id: number;
//     role_id: number;
//     username: string;
//     firstname: string | null;
//     lastname: string | null;
//     passwd: string | null;
//     password: string | null;
//     backend: string | null;
//     email: string | null;
//     phone: string;
//     phone_ext: string | null;
//     mobile: string;
//     signature: string;
//     lang: string | null;
//     timezone: string | null;
//     locale: string | null;
//     notes: string | null;
//     isactive: boolean;
//     isadmin: boolean;
//     isvisible: boolean;
//     onvacation: boolean;
//     assigned_only: boolean;
//     show_assigned_tickets: boolean;
//     change_passwd: boolean;
//     max_page_size: number;
//     auto_refresh_rate: number;
//     default_signature_type: 'none' | 'mine' | 'dept';
//     default_paper_size: 'Letter' | 'Legal' | 'Ledger' | 'A4' | 'A3';
//     extra: string | null;
//     permissions: string | null;
//     created: string;
//     lastlogin: string | null;
//     passwdreset: string | null;
//     updated: string;
//     dni: string | null;
//     direccion: string | null;
//     id_departamento: number | null;
//     id_provincia: number | null;
//     id_distrito: number | null;
//     zona_tecnico: string | null;
//     id_empresa: number | null;
//     fecha_ingreso: string | null;
//     id_cargo: number | null;
//     id_area: number | null;
//     activo: boolean;
// }

export type Variant =
    | 'neutral'
    | 'Stone'
    | 'Slate'
    | 'Gray'
    | 'Red'
    | 'Orange'
    | 'Amber'
    | 'Yellow'
    | 'Lime'
    | 'Green'
    | 'Emerald'
    | 'Teal'
    | 'Cyan'
    | 'Sky'
    | 'Blue'
    | 'Indigo'
    | 'Violet'
    | 'Purple'
    | 'Fuchsia'
    | 'Pink'
    | 'Rose';

export interface Paginated<T> {
    current_page: number;
    data: T[];
    next_page_url: string | null;
    per_page: number;
    prev_page_url: string | null;
    total: number;
    path: string;
    to: number;
    from: number;
    last_page: number;
    links: {
        url: string;
        label: string;
        active: boolean;
    }[];
}
