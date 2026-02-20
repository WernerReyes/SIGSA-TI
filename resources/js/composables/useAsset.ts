import {
    AssetType,
    AssetTypeDocCategory,
} from '@/interfaces/assetType.interface';

const PREFIX = '/assets';

export const useAsset = () => {
    const downloadAssignmentDocument = (
        assigmentId: number,
        type: AssetType,
    ) => {
        let endpoint = '';

        switch (type.doc_category) {
            case AssetTypeDocCategory.LAPTOP:
                endpoint = `${PREFIX}/generate-laptop-assignment-doc/${assigmentId}`;
                break;

            case AssetTypeDocCategory.CELL_PHONE:
                endpoint = `${PREFIX}/generate-phone-assignment-doc/${assigmentId}`;
                break;

            default:
                endpoint = `${PREFIX}/generate-accessory-assignment-doc/${assigmentId}`;
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
