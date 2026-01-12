import { Cpu, Headphones, Laptop, Monitor, Smartphone } from 'lucide-vue-next';
import { Component } from 'vue';

export interface AssetType {
    id: number;
    name: TypeName;
    created_at: Date;
    updated_at: Date;
}

export enum TypeName {
    LAPTOP = 'Laptop',
    PC = 'PC',
    CELL_PHONE = 'Celular',
    ACCESSORY = 'Accesorio',
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
    [TypeName.CELL_PHONE]: {
        value: TypeName.CELL_PHONE,
        icon: Smartphone,
    },
    [TypeName.ACCESSORY]: {
        value: TypeName.ACCESSORY,
        icon: Headphones,
    },
};

export const assetTypeOp = (type: TypeName): TypeNameOption => {
    return assetTypeOptions[type];
};
