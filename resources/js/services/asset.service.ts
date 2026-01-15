import { TypeName } from '@/interfaces/assetType.interface';
import type { Page, PageProps } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

export const getAssetDetails = (
    id: number,
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `/assets/${id}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['asset', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};

export const getAssetHistories = (
    id: number,
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `/assets/${id}/histories`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['asset', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};

export const getAssetAccessories = (
    onSuccess?: (page: Page<PageProps>) => void,
) => {
    router.get(
        `/assets/accessories`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ['assetAccessories', 'flash'],
            onSuccess: (page) => {
                onSuccess?.(page as Page<PageProps>);
            },
        },
    );
};

export const downloadAssignmentDocument = (
    assignmentId: number,
    type: TypeName,
) => {
    switch (type) {
        case TypeName.LAPTOP:
        case TypeName.PC:
            window.location.href = `/assets/generate-laptop-assignment-doc/${assignmentId}`;
            break;
        case TypeName.CELL_PHONE:
            window.location.href = `/assets/generate-phone-assignment-doc/${assignmentId}`;
            break;
        case TypeName.ACCESSORY:
            window.location.href = `/assets/generate-accessory-assignment-doc/${assignmentId}`;
            break;
        default:
            console.error(
                'Tipo de activo no soportado para descargar el documento de asignación.',
            );
    }
};

export const downloadReturnDocument = (assignmentId: number) => {
    window.location.href = `/assets/generate-return-doc/${assignmentId}`;
};
