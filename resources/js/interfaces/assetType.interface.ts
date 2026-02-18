import { Cpu, Headphones, Laptop, Monitor, Smartphone } from 'lucide-vue-next';
import { Component } from 'vue';

export interface AssetType {
    id: number;
    name: TypeName;
    created_at: Date;
    updated_at: Date;
}

// TODO: Check the download document functions, maybe we can use a single endpoint for all types and generate the document based on the type in the backend, this way we can avoid the switch case and make it more scalable for future types.

export enum TypeName {
    LAPTOP = 'Laptop',
    PC = 'PC',
    MINI_PC = 'Mini PC',
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
    [TypeName.MINI_PC]: {
        value: TypeName.MINI_PC,
        icon: Cpu,
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
