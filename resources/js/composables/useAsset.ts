import { AssetType, TypeName } from '@/interfaces/assetType.interface';

const PREFIX = '/assets';

export const useAsset = () => {
    const downloadAssignmentDocument = (
        assigmentId: number,
        type: AssetType,
    ) => {
        let endpoint = '';
        console.log(
            'Downloading document for assignment ID:',
            assigmentId,
            'Type:',
            type,
        );

        if (type.is_accessory) {
            endpoint = `${PREFIX}/generate-accessory-assignment-doc/${assigmentId}`;
        } else {
            if (
                type.name === TypeName.CELL_PHONE ||
                type.name === TypeName.TABLET
            ) {
                endpoint = `${PREFIX}/generate-phone-assignment-doc/${assigmentId}`;
                return;
            }

            endpoint = `${PREFIX}/generate-laptop-assignment-doc/${assigmentId}`;
        }

        // switch (type) {
        //     case TypeName.LAPTOP:
        //     case TypeName.PC:
        //     case TypeName.MINI_PC:
        //         endpoint = `${PREFIX}/generate-laptop-assignment-doc/${assigmentId}`;
        //         break;

        //     case TypeName.CELL_PHONE:
        //     case TypeName.TABLET:
        //         endpoint = `${PREFIX}/generate-phone-assignment-doc/${assigmentId}`;
        //         break;

        //     default:
        //         endpoint = `${PREFIX}/generate-accessory-assignment-doc/${assigmentId}`;
        // }
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
