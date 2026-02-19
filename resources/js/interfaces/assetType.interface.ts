import { Cable, Cpu, HelpCircle, Keyboard, Laptop, Monitor, Mouse, Plug, PlugZap, Smartphone, Tablet } from 'lucide-vue-next';
import { Component } from 'vue';

export interface AssetType {
    id: number;
    name: TypeName;
    is_accessory: boolean;
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

    PATCHCORD = 'Cable de Red',
    LAPTOP_CHARGER = 'Cargador de Laptop',
    CELL_PHONE_CHARGER = 'Cargador de Celular',
    OTHER = 'Otro',
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
    [TypeName.PATCHCORD]: {
        value: TypeName.PATCHCORD,
        icon:  Cable,
    },
    [TypeName.LAPTOP_CHARGER]: {
        value: TypeName.LAPTOP_CHARGER,
        icon: Plug,
    },
    [TypeName.CELL_PHONE_CHARGER]: {
        value: TypeName.CELL_PHONE_CHARGER,
        icon: PlugZap,
    },
    [TypeName.OTHER]: {
        value: TypeName.OTHER,
        icon: HelpCircle,
    }
};

export const assetTypeOp = (type: TypeName): TypeNameOption => {
    return assetTypeOptions[type];
};
