import { EnumOption } from '@/types';

import {
    Cable,
    Cpu,
    Headphones,
    HelpCircle,
    Keyboard,
    Laptop,
    Monitor,
    Mouse,
    Plug,
    PlugZap,
    Smartphone,
    Tablet,
} from 'lucide-vue-next';
import { Component } from 'vue';

export enum AssetTypeDocCategory {
    LAPTOP = 'LAPTOP',
    ACCESSORY = 'ACCESSORY',
    CELL_PHONE = 'CELL_PHONE',
}

export interface AssetType {
    id: number;
    name: TypeName;
    is_accessory: boolean;
    is_deletable: boolean;
    doc_category: AssetTypeDocCategory;
    created_at: Date;
    updated_at: Date;
}

export enum TypeName {
    LAPTOP = 'Laptop',
    PC = 'PC',
    CELL_PHONE = 'Celular',
    MINI_PC = 'Mini PC',
    MONITOR = 'Monitor',
    TABLET = 'Tablet',

    MOUSE = 'Mouse',
    KEYBOARD = 'Teclado',
    HEADPHONES = 'Audífonos',

    PATCHCORD = 'Cable de Red',
    LAPTOP_CHARGER = 'Cargador de Laptop',
    CELL_PHONE_CHARGER = 'Cargador de Celular',
    
}

export interface TypeNameOption {
    value: TypeName;
    icon: Component;
}

export const assetTypeOptions: Record<TypeName, TypeNameOption> = {
    [TypeName.LAPTOP]: {
        value: TypeName.LAPTOP,
        icon: Laptop,
    },
    [TypeName.PC]: {
        value: TypeName.PC,
        icon: Monitor,
    },
    [TypeName.MINI_PC]: {
        value: TypeName.MINI_PC,
        icon: Cpu,
    },
    [TypeName.CELL_PHONE]: {
        value: TypeName.CELL_PHONE,
        icon: Smartphone,
    },
    [TypeName.MONITOR]: {
        value: TypeName.MONITOR,
        icon: Monitor,
    },
    [TypeName.TABLET]: {
        value: TypeName.TABLET,
        icon: Tablet,
    },
    [TypeName.MOUSE]: {
        value: TypeName.MOUSE,
        icon: Mouse,
    },
    [TypeName.KEYBOARD]: {
        value: TypeName.KEYBOARD,
        icon: Keyboard,
    },
    [TypeName.HEADPHONES]: {
        value: TypeName.HEADPHONES,
        icon: Headphones,
    },
    [TypeName.PATCHCORD]: {
        value: TypeName.PATCHCORD,
        icon: Cable,
    },
    [TypeName.LAPTOP_CHARGER]: {
        value: TypeName.LAPTOP_CHARGER,
        icon: Plug,
    },
    [TypeName.CELL_PHONE_CHARGER]: {
        value: TypeName.CELL_PHONE_CHARGER,
        icon: PlugZap,
    },
   
};

export const assetTypeOp = (type: TypeName): TypeNameOption => {
    return assetTypeOptions[type] || {
        value: type,
        icon: HelpCircle,
    };
};

const categoryOptions: Record<
    AssetTypeDocCategory,
    EnumOption<AssetTypeDocCategory>
> = {
    [AssetTypeDocCategory.LAPTOP]: {
        value: AssetTypeDocCategory.LAPTOP,
        icon: Laptop,
        label: 'Laptop',
    },
    [AssetTypeDocCategory.ACCESSORY]: {
        value: AssetTypeDocCategory.ACCESSORY,
        icon: Plug,
        label: 'Accesorio',
    },
    [AssetTypeDocCategory.CELL_PHONE]: {
        value: AssetTypeDocCategory.CELL_PHONE,
        icon: Smartphone,
        label: 'Celular',
    },
};

export const assetTypeUI = {
    category: {
        array: Object.values(AssetTypeDocCategory),
        label: (cat: AssetTypeDocCategory) => categoryOptions[cat].label,
        icon: (cat: AssetTypeDocCategory) => categoryOptions[cat].icon,
        value: (cat: AssetTypeDocCategory) => categoryOptions[cat].value,
    },
    type: {
        array: Object.values(TypeName),
        label: (type: TypeName) => assetTypeOptions[type]?.value || type,
        icon: (type: TypeName) => assetTypeOptions[type]?.icon || HelpCircle,
        value: (type: TypeName) => assetTypeOptions[type]?.value || type,
    },
};

