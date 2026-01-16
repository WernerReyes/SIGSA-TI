import { TypeName } from '@/interfaces/assetType.interface';

const PREFIX = '/assets';

export const useAsset = () => {
    const downloadAssignmentDocument = (
        assigmentId: number,
        type: TypeName,
    ) => {
        let endpoint = '';
        switch (type) {
            case TypeName.LAPTOP:
            case TypeName.PC:
                endpoint = `${PREFIX}/generate-laptop-assignment-doc/${assigmentId}`;
                break;

            case TypeName.CELL_PHONE:
                endpoint = `${PREFIX}/generate-phone-assignment-doc/${assigmentId}`;
                break;
            case TypeName.ACCESSORY:
                endpoint = `${PREFIX}/generate-accessory-assignment-doc/${assigmentId}`;
                break;
            default:
                throw new Error(
                    'Tipo de activo no soportado para descargar el documento de asignación.',
                );
        }
        window.open(endpoint, '_self');
    };

    const downloadReturnAssignmentDocument = (assigmentId: number) => {
        const endpoint = `${PREFIX}/generate-return-doc/${assigmentId}`;
        window.open(endpoint, '_self');
    };

    return {
        downloadAssignmentDocument,
        downloadReturnAssignmentDocument,
    };
};
