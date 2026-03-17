<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-4xl p-0 gap-0 overflow-hidden">
            <DialogHeader class="px-4 py-3 border-b bg-muted/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        <Mail class="size-4 text-primary" />
                        Mensaje nuevo
                    </div>

                    <VeeField name="document_type" v-slot="{ errors, value }">
                        <div class="flex items-center w-4/12 gap-4">
                            <div class="flex items-center gap-1">
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="ghost"
                                    :class="value === DeliveryRecordType.ASSIGNMENT ? 'bg-primary/10 text-primary' : ''"
                                    :disabled="!canSendAssignmentDoc || availableDocs.length === 1 || isSendingEmail"
                                    @click="setDocumentType(DeliveryRecordType.ASSIGNMENT)"
                                >
                               <ArrowBigRightDash />
                                    Entrega
                                </Button>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="ghost"
                                    :class="value === DeliveryRecordType.DEVOLUTION ? 'bg-primary/10 text-primary' : ''"
                                    :disabled="!canSendReturnDoc || availableDocs.length === 1 || isSendingEmail"
                                    @click="setDocumentType(DeliveryRecordType.DEVOLUTION)"
                                >
                                <ArrowBigLeftDash />
                                    Devolución
                                </Button>
                            </div>
                            <FieldError v-if="errors.length" class="text-xs">{{ errors[0] }}</FieldError>
                        </div>
                    </VeeField>
                </div>
            </DialogHeader>

            <form class="flex flex-col" @submit.prevent="handleSendEmail">
                <div class="px-4 py-2 border-b">
                    <VeeField name="email_to" v-slot="{ componentField, errors }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-muted-foreground w-12">Para</span>
                            <Input
                                v-bind="componentField"
                                type="email"
                                placeholder="correo@empresa.com"
                                class="shadow-none focus-visible:ring-0"
                                :disabled="isSendingEmail"
                            />
                        </div>
                        <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                    </VeeField>
                </div>

                <div class="px-4 py-2 border-b">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-muted-foreground w-12">Asunto</span>
                        <p class="text-sm font-medium">{{ emailSubject }}</p>
                    </div>
                </div>

                <ScrollArea class="h-95 px-4 py-3">
                    <div class="space-y-4">
                        <VeeField name="greeting" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Textarea
                                    v-bind="componentField"
                                    rows="2"
                                    :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0"
                                />
                                <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                            </div>
                        </VeeField>

                        <VeeField name="before_equipment" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Textarea
                                    v-bind="componentField"
                                    rows="2"
                                    :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0"
                                />
                                <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                            </div>
                        </VeeField>

                        <div class="rounded-md border bg-muted/30 px-3 py-2 ml-5">
                            <p class="text-xs text-muted-foreground mb-1 flex items-center gap-1">
                                <Lock class="size-3" />
                                Sección fija (no editable)
                            </p>
                            <p class="text-sm leading-relaxed whitespace-pre-line">{{ fixedEquipmentLine }}</p>
                        </div>

                        <VeeField name="after_equipment" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Textarea
                                    v-bind="componentField"
                                    rows="3"
                                    :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0"
                                />
                                <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                            </div>
                        </VeeField>

                        <div class="space-y-2 pt-2">
                            <label class="text-sm font-medium">Imágenes extra (opcional)</label>
                            <MultiUploader
                                :max-files="3"
                                :max-size-mb="2"
                                accept="image/*"
                                preview-mode="gallery"
                                @update:file="(files) => { extraImages = files; }"
                                @error="(msg) => toast.error(msg)"
                            />
                        </div>
                    </div>
                </ScrollArea>

                <DialogFooter class="px-4 py-3 border-t bg-muted/10">
                    <Button type="button" variant="outline" @click="open = false" :disabled="isSendingEmail">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="isSendingEmail || Object.keys(errors).length > 0">
                        Enviar
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { FieldError } from '@/components/ui/field';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Textarea } from '@/components/ui/textarea';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowBigLeftDash, ArrowBigRightDash, Lock, Mail } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { format, parseISO } from 'date-fns';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import z from 'zod';
import MultiUploader from '../MultiUploader.vue';

const open = defineModel<boolean>('open', { default: false });

const props = defineProps<{
    assignment: AssetAssignment | null;
}>();

const emit = defineEmits<{
    (e: 'sent'): void;
}>();

const isSendingEmail = ref(false);
const extraImages = ref<File[]>([]);

const getDeliveryUrl = (assignment: AssetAssignment): string => {
    return assignment.delivery_document?.file_url || assignment.parent_assignment?.delivery_document?.file_url || '';
};

const getReturnUrl = (assignment: AssetAssignment): string => {
    if (!assignment.returned_at) return '';
    return assignment.return_document?.file_url || assignment.parent_assignment?.return_document?.file_url || '';
};

const availableDocumentTypes = (assignment: AssetAssignment): DeliveryRecordType[] => {
    const types: DeliveryRecordType[] = [];

    if (getDeliveryUrl(assignment)) {
        types.push(DeliveryRecordType.ASSIGNMENT);
    }

    if (getReturnUrl(assignment)) {
        types.push(DeliveryRecordType.DEVOLUTION);
    }

    return types;
};

const availableDocs = computed(() => {
    if (!props.assignment) return [] as DeliveryRecordType[];
    return availableDocumentTypes(props.assignment);
});

const canSendAssignmentDoc = computed(() => availableDocs.value.includes(DeliveryRecordType.ASSIGNMENT));
const canSendReturnDoc = computed(() => availableDocs.value.includes(DeliveryRecordType.DEVOLUTION));

const getAssetName = (assignment: AssetAssignment): string => {
    // const relatedAsset = assignment.parent_assignment?.asset || assignment.asset;
    let relatedAsset = assignment.asset;
    if (assignment.parent_assignment && !assignment.parent_assignment.returned_at) {
        relatedAsset = assignment.parent_assignment.asset;
    }

    return relatedAsset?.full_name || `AST-${relatedAsset?.id || assignment.asset_id}`;
};

const getSerial = (assignment: AssetAssignment): string => {
    const relatedAsset = assignment.parent_assignment?.asset || assignment.asset;
    return relatedAsset?.serial_number || 'N/A';
};

const getAssignedUserName = (assignment: AssetAssignment): string => {
    return assignment.assigned_to?.full_name || 'colaborador asignado';
};

const getEventDate = (assignment: AssetAssignment, documentType: DeliveryRecordType): string => {
    const rawDate = documentType === DeliveryRecordType.DEVOLUTION
        ? assignment.returned_at
        : assignment.assigned_at;

    if (!rawDate) return 'fecha no registrada';

    try {
        return format(parseISO(rawDate), 'dd/MM/yyyy');
    } catch {
        return rawDate;
    }
};

const getAccessoriesNames = (assignment: AssetAssignment): string[] => {
    // const baseAssignment = assignment.parent_assignment || assignment;
    let baseAssignment = assignment;
    if (assignment.parent_assignment && !assignment.parent_assignment.returned_at) {
        baseAssignment = assignment.parent_assignment;
    }

    if (baseAssignment.returned_at && values.document_type === DeliveryRecordType.DEVOLUTION) {
        // En caso de devolución, mostrar solo los accesorios devueltos con el equipo
        return (baseAssignment.children_assignments || [])
            .filter(child => child.returned_at) // Filtrar solo los accesorios que han sido devueltos
            .map(child => child.asset?.full_name || `AST-${child.asset_id}`)
            .filter((name, index, arr) => !!name && arr.indexOf(name) === index);
    }


    return (baseAssignment.children_assignments || [])
        .map((child) => child.asset?.full_name || `AST-${child.asset_id}`)
        .filter((name, index, arr) => !!name && arr.indexOf(name) === index);
};

const indentBlock = (text: string, spaces = 4): string => {
    const indent = ' '.repeat(spaces);
    return text
        .split('\n')
        .map((line) => `${indent}${line}`)
        .join('\n');
};

const getDefaultTextParts = (assignment: AssetAssignment, documentType: DeliveryRecordType) => {
    const assetName = getAssetName(assignment);
    const accessories = getAccessoriesNames(assignment);
    const dateLabel = getEventDate(assignment, documentType);
    const assignedTo = getAssignedUserName(assignment);

    const base = {
        greeting: 'Estimada Sr. Cecilia,',
        detailsIntro: documentType === DeliveryRecordType.ASSIGNMENT
            ? 'A continuación, se detallan los datos correspondientes al activo entregado:'
            : 'A continuación, se detallan los datos correspondientes al activo devuelto:',
        assetTitle: documentType === DeliveryRecordType.ASSIGNMENT ? 'Activo entregado' : 'Activo devuelto',
        accessoriesTitle: 'Accesorios incluidos',
        accessories,
        signatureLabel: 'Atentamente,',
        signatureArea: 'Área de Sistemas\nDepartamento de Tecnología',
    };

    if (documentType === DeliveryRecordType.DEVOLUTION) {
        const relatedAsset = assignment.parent_assignment?.asset || assignment.asset;
        const isCellphone = relatedAsset?.type?.name === TypeName.CELL_PHONE || relatedAsset?.type_id === 3;

        if (isCellphone) {
            return {
                ...base,
                introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido devuelto satisfactoriamente por el colaborador ${assignedTo} el día ${dateLabel}.`,
                closingParagraph: 'Se deja constancia de que el equipo y los accesorios mencionados han sido devueltos en correctas condiciones y se procederá a entregar para su custodia.',
                fixedEquipment: `el equipo ${assetName} ha sido devuelto`,
            };
        }

        return {
            ...base,
            introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido devuelto satisfactoriamente por el colaborador ${assignedTo} el día ${dateLabel}.`,
            closingParagraph: 'Se deja constancia de que el equipo y los accesorios mencionados han sido devueltos en correctas condiciones.',
            fixedEquipment: `el equipo ${assetName} ha sido devuelto`,
        };
    }

    return {
        ...base,
        introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido entregado satisfactoriamente al colaborador ${assignedTo} el día ${dateLabel}.`,
        closingParagraph: 'Se deja constancia de que el equipo y los accesorios mencionados han sido entregados en correctas condiciones.',
        fixedEquipment: `el equipo ${assetName} ha sido entregado`,
    };
};

const formSchema = toTypedSchema(z.object({
    document_type: z.nativeEnum(DeliveryRecordType, {
        message: 'Debes seleccionar un documento para enviar.',
    }),
    email_to: z.string({
        message: 'El correo destino es obligatorio.',
    }).email('Debes ingresar un correo válido.'),
    greeting: z.string({ message: 'El saludo es obligatorio.' }).min(1, 'El saludo es obligatorio.').max(500, 'Texto demasiado largo.'),
    before_equipment: z.string({ message: 'El párrafo principal es obligatorio.' }).min(1, 'El párrafo principal es obligatorio.').max(3000, 'Texto demasiado largo.'),
    after_equipment: z.string({ message: 'El cierre es obligatorio.' }).min(1, 'El cierre es obligatorio.').max(3000, 'Texto demasiado largo.'),
}));

const { errors, values, setFieldValue, setValues, resetForm, handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues: {
        document_type: DeliveryRecordType.ASSIGNMENT,
        email_to: '',
        greeting: '',
        before_equipment: '',
        after_equipment: '',
    },
});

const fixedEquipmentLine = computed(() => {
    if (!props.assignment || !values.document_type) return '';
    return getDefaultTextParts(props.assignment, values.document_type as DeliveryRecordType).fixedEquipment;
});

const emailSubject = computed(() => {
    if (!props.assignment || !values.document_type) return 'Constancia de documento';
    const typeLabel = values.document_type === DeliveryRecordType.ASSIGNMENT ? 'entrega' : 'devolución';
    return `Constancia de ${typeLabel} - ${getAssetName(props.assignment)}`;
});

const buildComposedMessage = (formValues: typeof values) => {
    return [
        formValues.greeting,
        '',
        formValues.before_equipment,
        indentBlock(fixedEquipmentLine.value, 4),
        indentBlock(formValues.after_equipment || '', 4),
    ].join('\n');
};

const buildMessageSections = (formValues: typeof values) => {
    if (!props.assignment) return null;

    const currentDefaults = getDefaultTextParts(
        props.assignment,
        formValues.document_type as DeliveryRecordType,
    );

    return {
        greeting: formValues.greeting,
        intro_paragraph: formValues.before_equipment,
        details_intro: currentDefaults.detailsIntro,
        asset_title: currentDefaults.assetTitle,
        asset_name: getAssetName(props.assignment),
        serial: getSerial(props.assignment),
        accessories_title: currentDefaults.accessoriesTitle,
        accessories: currentDefaults.accessories,
        closing_paragraph: formValues.after_equipment,
        signature_label: currentDefaults.signatureLabel,
        signature_area: currentDefaults.signatureArea,
    };
};

const setDocumentType = (documentType: DeliveryRecordType) => {
    if (!props.assignment) return;
    setFieldValue('document_type', documentType);

    const defaults = getDefaultTextParts(props.assignment, documentType);
    setFieldValue('greeting', defaults.greeting);
    setFieldValue('before_equipment', defaults.introParagraph);
    setFieldValue('after_equipment', defaults.closingParagraph);
};

watch([open, () => props.assignment], ([isOpen, assignment]) => {
    if (!isOpen || !assignment) {
        if (!isOpen) {
            resetForm();
            extraImages.value = [];
            isSendingEmail.value = false;
        }
        return;
    }

    const types = availableDocumentTypes(assignment);

    if (!types.length) {
        toast.error('No hay documento de entrega o devolución cargado para enviar.');
        open.value = false;
        return;
    }

    const selectedType = types.length === 1 ? types[0] : DeliveryRecordType.ASSIGNMENT;
    const defaults = getDefaultTextParts(assignment, selectedType);

    setValues({
        document_type: selectedType,
        email_to: '',
        greeting: defaults.greeting,
        before_equipment: defaults.introParagraph,
        after_equipment: defaults.closingParagraph,
    });
});

const handleSendEmail = handleSubmit((formValues) => {
    if (!props.assignment) {
        toast.error('No se pudo determinar la asignación para enviar el correo.');
        return;
    }

    isSendingEmail.value = true;

    router.post(`/assets/delivery-records/${props.assignment.id}/send-email`, {
        document_type: formValues.document_type,
        email_to: formValues.email_to,
        message: buildComposedMessage(formValues),
        message_sections: buildMessageSections(formValues),
        extra_images: extraImages.value,
    }, {
        forceFormData: true,
        only: [],
        onFlash(flash) {
            if (flash.error) {
                toast.error(String(flash.error));
                return;
            }

            toast.success('Correo enviado correctamente.');
            emit('sent');
            open.value = false;
        },
        onFinish: () => {
            isSendingEmail.value = false;
        },
    });
});

watch(() => values.document_type, (newType) => {
    if (!newType || !props.assignment) return;

    const defaults = getDefaultTextParts(props.assignment, newType as DeliveryRecordType);
    setFieldValue('greeting', defaults.greeting);
    setFieldValue('before_equipment', defaults.introParagraph);
    setFieldValue('after_equipment', defaults.closingParagraph);
});
</script>
