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
                                <Button type="button" size="sm" variant="ghost"
                                    :class="value === DeliveryRecordType.ASSIGNMENT ? 'bg-primary/10 text-primary' : ''"
                                    :disabled="!canSendAssignmentDoc || availableDocs.length === 1 || isSendingEmail"
                                    @click="setDocumentType(DeliveryRecordType.ASSIGNMENT)">
                                    <ArrowBigRightDash />
                                    Entrega
                                </Button>
                                <Button type="button" size="sm" variant="ghost"
                                    :class="value === DeliveryRecordType.DEVOLUTION ? 'bg-primary/10 text-primary' : ''"
                                    :disabled="!canSendReturnDoc || availableDocs.length === 1 || isSendingEmail"
                                    @click="setDocumentType(DeliveryRecordType.DEVOLUTION)">
                                    <ArrowBigLeftDash />
                                    Devolución
                                </Button>
                            </div>
                            <FieldError v-if="errors.length" class="text-xs">{{ errors[0] }}</FieldError>
                        </div>
                    </VeeField>
                </div>
            </DialogHeader>

            <form class="flex flex-col" @submit.prevent="submitForm">
                <div class="px-4 py-2 border-b">
                    <VeeField name="email_to" v-slot="{ errors }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-muted-foreground w-12">Para</span>
                            <div class="flex-1 relative">
                                <div class="min-h-10 rounded-md border bg-background px-2 py-1 flex flex-wrap items-center gap-1">
                                    <Badge v-for="(chip, index) in toChips" :key="`to-${chip.email}-${index}`"
                                        variant="secondary" class="h-6 px-2 text-xs gap-1">
                                        {{ chip.label }}
                                        <button type="button" class="ml-1 text-muted-foreground hover:text-foreground"
                                            @click="removeRecipient('to', index)">x</button>
                                    </Badge>

                                    <input
                                        v-model="toQuery"
                                        type="text"
                                        class="flex-1 min-w-50 border-0 bg-transparent outline-none text-sm"
                                        placeholder="correo1@empresa.com; correo2@empresa.com"
                                        :disabled="isSendingEmail"
                                        @focus="recipientFocus = 'to'; ensureUsersForSuggestions()"
                                        @blur="handleRecipientBlur"
                                        @keydown="onRecipientKeydown($event, 'to')"
                                        @paste="onRecipientPaste($event, 'to')"
                                    />
                                </div>

                                <div v-if="showSuggestions('to')" class="absolute z-50 mt-1 w-full rounded-md border bg-background shadow-md p-1 max-h-52 overflow-y-auto">
                                    <div v-if="isLoadingUserSuggestions" class="px-2 py-1.5 text-xs text-muted-foreground">
                                        Cargando sugerencias...
                                    </div>
                                    <button
                                        v-for="(user, idx) in filteredUsersTo"
                                        :key="`to-suggest-${user.staff_id}`"
                                        type="button"
                                        class="w-full text-left rounded-sm px-2 py-1.5 text-sm"
                                        :class="toSuggestionIndex === idx ? 'bg-muted' : 'hover:bg-muted'"
                                        @mouseenter="toSuggestionIndex = idx"
                                        @mousedown.prevent="selectSuggestedUser('to', user)"
                                    >
                                        <span class="font-medium">{{ user.fullName }}</span>
                                        <span class="text-muted-foreground"> - {{ user.email }}</span>
                                    </button>
                                    <div v-if="!isLoadingUserSuggestions && !filteredUsersTo.length" class="px-2 py-1.5 text-xs text-muted-foreground">
                                        Sin coincidencias
                                    </div>
                                </div>
                            </div>
                        </div>
                        <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                    </VeeField>
                </div>

                <div class="px-4 py-2 border-b">
                    <VeeField name="email_cc" v-slot="{ errors }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-muted-foreground w-12">CC</span>
                            <div class="flex-1 relative">
                                <div class="min-h-10 rounded-md border bg-background px-2 py-1 flex flex-wrap items-center gap-1">
                                    <Badge v-for="(chip, index) in ccChips" :key="`cc-${chip.email}-${index}`"
                                        variant="secondary" class="h-6 px-2 text-xs gap-1">
                                        {{ chip.label }}
                                        <button type="button" class="ml-1 text-muted-foreground hover:text-foreground"
                                            @click="removeRecipient('cc', index)">x</button>
                                    </Badge>

                                    <input
                                        v-model="ccQuery"
                                        type="text"
                                        class="flex-1 min-w-50 border-0 bg-transparent outline-none text-sm"
                                        placeholder="cc1@empresa.com; cc2@empresa.com"
                                        :disabled="isSendingEmail"
                                        @focus="recipientFocus = 'cc'; ensureUsersForSuggestions()"
                                        @blur="handleRecipientBlur"
                                        @keydown="onRecipientKeydown($event, 'cc')"
                                        @paste="onRecipientPaste($event, 'cc')"
                                    />
                                </div>

                                <div v-if="showSuggestions('cc')" class="absolute z-50 mt-1 w-full rounded-md border bg-background shadow-md p-1 max-h-52 overflow-y-auto">
                                    <div v-if="isLoadingUserSuggestions" class="px-2 py-1.5 text-xs text-muted-foreground">
                                        Cargando sugerencias...
                                    </div>
                                    <button
                                        v-for="(user, idx) in filteredUsersCc"
                                        :key="`cc-suggest-${user.staff_id}`"
                                        type="button"
                                        class="w-full text-left rounded-sm px-2 py-1.5 text-sm"
                                        :class="ccSuggestionIndex === idx ? 'bg-muted' : 'hover:bg-muted'"
                                        @mouseenter="ccSuggestionIndex = idx"
                                        @mousedown.prevent="selectSuggestedUser('cc', user)"
                                    >
                                        <span class="font-medium">{{ user.fullName }}</span>
                                        <span class="text-muted-foreground"> - {{ user.email }}</span>
                                    </button>
                                    <div v-if="!isLoadingUserSuggestions && !filteredUsersCc.length" class="px-2 py-1.5 text-xs text-muted-foreground">
                                        Sin coincidencias
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">Separar correos con ; (punto y coma)</p>
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
                                <Textarea v-bind="componentField" rows="2" :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0" />
                                <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                            </div>
                        </VeeField>

                        <VeeField name="before_equipment" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Textarea v-bind="componentField" rows="2" :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0" />
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
                                <Textarea v-bind="componentField" rows="3" :disabled="isSendingEmail"
                                    class="border-0 shadow-none resize-none focus-visible:ring-0 px-0" />
                                <FieldError v-if="errors.length">{{ errors[0] }}</FieldError>
                            </div>
                        </VeeField>

                        <div class="space-y-2 pt-2">
                            <label class="text-sm font-medium">Imágenes extra (opcional)</label>
                            <MultiUploader :max-files="3" :max-size-mb="2" accept="image/*" preview-mode="gallery"
                                @update:file="(files) => { extraImages = files; }" @error="(msg) => toast.error(msg)" />
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
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogFooter, DialogHeader } from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Textarea } from '@/components/ui/textarea';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { type AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { TypeName } from '@/interfaces/assetType.interface';
import type { User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
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

type RecipientType = 'to' | 'cc';
type RecipientChip = { email: string; label: string };

const page = usePage();
const isSendingEmail = ref(false);
const extraImages = ref<File[]>([]);
const toChips = ref<RecipientChip[]>([]);
const ccChips = ref<RecipientChip[]>([]);
const toQuery = ref('');
const ccQuery = ref('');
const recipientFocus = ref<RecipientType | null>(null);
const toSuggestionIndex = ref(0);
const ccSuggestionIndex = ref(0);
const isLoadingUserSuggestions = ref(false);

const ensureUsersForSuggestions = () => {
    if (usersWithEmail.value.length || isLoadingUserSuggestions.value) {
        return;
    }

    isLoadingUserSuggestions.value = true;
    router.reload({
        only: ['users'],
        onFinish: () => {
            isLoadingUserSuggestions.value = false;
        },
    });
};

const usersWithEmail = computed(() => {
    const users = (page.props.users as User[] | undefined) || [];

    return users
        .filter((user) => !!user.email)
        .map((user) => {
            const fullName = user.full_name || `${user.firstname || ''} ${user.lastname || ''}`.trim() || user.email || '';
            return {
                staff_id: user.staff_id,
                fullName,
                email: String(user.email || '').toLowerCase(),
            };
        });
});

const usersEmailSet = computed(() => new Set(usersWithEmail.value.map((user) => user.email)));

const isValidEmail = (email: string) => /^[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+$/.test(email);

const syncRecipientsToForm = () => {
    setFieldValue('email_to', toChips.value.map((chip) => chip.email).join(';'));
    setFieldValue('email_cc', ccChips.value.map((chip) => chip.email).join(';'));
};

const recipientQuery = (type: RecipientType) => type === 'to' ? toQuery.value : ccQuery.value;

const filteredUsersByType = (type: RecipientType) => {
    const query = recipientQuery(type).trim().toLowerCase();
    if (!query) return [];

    const selected = new Set([
        ...toChips.value.map((chip) => chip.email),
        ...ccChips.value.map((chip) => chip.email),
    ]);

    return usersWithEmail.value
        .filter((user) => !selected.has(user.email))
        .filter((user) => user.email.includes(query) || user.fullName.toLowerCase().includes(query))
        .slice(0, 8);
};

const filteredUsersTo = computed(() => filteredUsersByType('to'));
const filteredUsersCc = computed(() => filteredUsersByType('cc'));

const currentSuggestions = (type: RecipientType) => type === 'to' ? filteredUsersTo.value : filteredUsersCc.value;

const showSuggestions = (type: RecipientType) => {
    if (recipientFocus.value !== type) return false;
    if (!recipientQuery(type).trim()) return false;
    return isLoadingUserSuggestions.value || (type === 'to' ? filteredUsersTo.value : filteredUsersCc.value).length > 0;
};

const addRecipient = (type: RecipientType, emailRaw: string, label?: string): boolean => {
    const email = emailRaw.trim().toLowerCase();
    if (!email) return false;

    if (email.includes(',')) {
        toast.error('Usa ; para separar correos. No uses comas.');
        return false;
    }

    if (!isValidEmail(email)) {
        toast.error(`Correo inválido: ${email}`);
        return false;
    }

    if (usersWithEmail.value.length > 0 && !usersEmailSet.value.has(email)) {
        toast.error(`El correo ${email} no pertenece a un usuario registrado.`);
        return false;
    }

    const all = [...toChips.value, ...ccChips.value].map((chip) => chip.email);
    if (all.includes(email)) {
        return true;
    }

    const user = usersWithEmail.value.find((item) => item.email === email);
    const chip: RecipientChip = { email, label: label || user?.fullName || email };

    if (type === 'to') {
        toChips.value.push(chip);
    } else {
        ccChips.value.push(chip);
    }

    syncRecipientsToForm();
    return true;
};

const consumeRecipientQuery = (type: RecipientType) => {
    const queryRef = type === 'to' ? toQuery : ccQuery;
    const raw = queryRef.value.trim();
    if (!raw) return;

    const tokens = raw.split(';').map((token) => token.trim()).filter(Boolean);
    for (const token of tokens) {
        addRecipient(type, token);
    }

    queryRef.value = '';
};

const removeRecipient = (type: RecipientType, index: number) => {
    if (type === 'to') {
        toChips.value.splice(index, 1);
    } else {
        ccChips.value.splice(index, 1);
    }

    syncRecipientsToForm();
};

const selectSuggestedUser = (type: RecipientType, user: { fullName: string; email: string }) => {
    addRecipient(type, user.email, user.fullName);
    if (type === 'to') {
        toQuery.value = '';
        toSuggestionIndex.value = 0;
    } else {
        ccQuery.value = '';
        ccSuggestionIndex.value = 0;
    }
};

const onRecipientKeydown = (event: KeyboardEvent, type: RecipientType) => {
    const suggestions = currentSuggestions(type);
    const indexRef = type === 'to' ? toSuggestionIndex : ccSuggestionIndex;

    if (event.key === 'ArrowDown' && suggestions.length) {
        event.preventDefault();
        indexRef.value = (indexRef.value + 1) % suggestions.length;
        return;
    }

    if (event.key === 'ArrowUp' && suggestions.length) {
        event.preventDefault();
        indexRef.value = indexRef.value === 0 ? suggestions.length - 1 : indexRef.value - 1;
        return;
    }

    if ((event.key === 'Enter' || event.key === 'Tab') && suggestions.length) {
        event.preventDefault();
        const selected = suggestions[indexRef.value] || suggestions[0];
        if (selected) {
            selectSuggestedUser(type, selected);
        }
        return;
    }

    if (event.key === ';' || event.key === 'Enter' || event.key === 'Tab') {
        event.preventDefault();
        consumeRecipientQuery(type);
        return;
    }

    const queryRef = type === 'to' ? toQuery : ccQuery;
    const chipsRef = type === 'to' ? toChips : ccChips;
    if (event.key === 'Backspace' && !queryRef.value && chipsRef.value.length) {
        chipsRef.value.pop();
        syncRecipientsToForm();
    }
};

const onRecipientPaste = (event: ClipboardEvent, type: RecipientType) => {
    const pasted = event.clipboardData?.getData('text') || '';
    if (!pasted.includes(';') && !pasted.includes(',')) {
        return;
    }

    event.preventDefault();

    if (pasted.includes(',')) {
        toast.error('Usa ; para separar correos. No uses comas.');
        return;
    }

    const tokens = pasted.split(';').map((item) => item.trim()).filter(Boolean);
    for (const token of tokens) {
        addRecipient(type, token);
    }
};

const handleRecipientBlur = () => {
    setTimeout(() => {
        recipientFocus.value = null;
    }, 120);
};

watch(toQuery, () => {
    toSuggestionIndex.value = 0;
});

watch(ccQuery, () => {
    ccSuggestionIndex.value = 0;
});

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
    if (assignment.parent_assignment && assignment.returned_together) {
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
    let baseAssignment = assignment;
    if (assignment.parent_assignment) {
        baseAssignment = assignment.parent_assignment;

    }

    if (values.document_type === DeliveryRecordType.DEVOLUTION) {
        return (baseAssignment.children_assignments || [])
            .filter(child => child.returned_together)
            .map(child => child.asset?.full_name || `AST-${child.asset_id}`);
    }


    return (baseAssignment.children_assignments || [])
        .map((child) => child.asset?.full_name || `AST-${child.asset_id}`)
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
    const serial = getSerial(assignment);
    const accessories = getAccessoriesNames(assignment);
    const dateLabel = getEventDate(assignment, documentType);
    const assignedTo = getAssignedUserName(assignment);

    const blockTitle = documentType === DeliveryRecordType.ASSIGNMENT
        ? 'Activo entregado'
        : 'Activo devuelto';

    const accessoriesBlock = accessories.length > 0
        ? `\n\nAccesorios incluidos\n${accessories.map((item) => `- ${item}`).join('\n')}`
        : '';

    const fixedEquipmentBlock = `${blockTitle}\n${assetName}${serial && serial !== 'N/A' ? ` - S/N: ${serial}` : ''}${accessoriesBlock}`;

    const base = {
        greeting: 'Estimada Sra. Cecilia,',
        detailsIntro: documentType === DeliveryRecordType.ASSIGNMENT
            ? 'A continuación, se detallan los datos correspondientes al activo entregado:'
            : 'A continuación, se detallan los datos correspondientes al activo devuelto:',
        assetTitle: documentType === DeliveryRecordType.ASSIGNMENT ? 'Activo entregado' : 'Activo devuelto',
        accessoriesTitle: 'Accesorios incluidos',
        accessories,
        signatureLabel: 'Atentamente,',
        signatureArea: 'Área de Sistemas - TI',
    };

    const hasAccessories = accessories.length > 0;
      
    if (documentType === DeliveryRecordType.DEVOLUTION) {
        const relatedAsset = assignment.parent_assignment?.asset || assignment.asset;
        const isCellphone = relatedAsset?.type?.name === TypeName.CELL_PHONE || relatedAsset?.type_id === 3;


        if (isCellphone) {
            return {
                ...base,
                introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido devuelto satisfactoriamente por el colaborador ${assignedTo} el día ${dateLabel}.`,
                closingParagraph: hasAccessories
                    ? 'Se deja constancia de que el equipo y los accesorios mencionados han sido devueltos en correctas condiciones.'
                    : 'Se deja constancia de que el equipo mencionado ha sido devuelto en correctas condiciones.',
                fixedEquipment: fixedEquipmentBlock,
            };
        }

        return {
            ...base,
            introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido devuelto satisfactoriamente por el colaborador ${assignedTo} el día ${dateLabel}.`,
            closingParagraph: hasAccessories
                ? 'Se deja constancia de que el equipo y los accesorios mencionados han sido devueltos en correctas condiciones.'
                : 'Se deja constancia de que el equipo mencionado ha sido devuelto en correctas condiciones.',
            fixedEquipment: fixedEquipmentBlock,
        };
    }

    return {
        ...base,
        introParagraph: `Por medio del presente correo se deja constancia de que el siguiente activo ha sido entregado satisfactoriamente al colaborador ${assignedTo} el día ${dateLabel}.`,
        closingParagraph: hasAccessories
            ? 'Se deja constancia de que el equipo y los accesorios mencionados han sido entregados en correctas condiciones.'
            : 'Se deja constancia de que el equipo mencionado ha sido entregado en correctas condiciones.',
        fixedEquipment: fixedEquipmentBlock,
    };
};

const formSchema = toTypedSchema(z.object({
    document_type: z.nativeEnum(DeliveryRecordType, {
        message: 'Debes seleccionar un documento para enviar.',
    }),
    email_to: z.string({ message: 'El correo destino es obligatorio.' })
        .min(1, 'El correo destino es obligatorio.')
        .regex(/^\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*(;\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*)*$/, 'Usa correos válidos separados únicamente por ; (punto y coma). No uses comas.'),
    email_cc: z.string()
        .max(2000, 'El campo CC es demasiado largo.')
        .regex(/^\s*$|^\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*(;\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*)*$/, 'En CC usa correos válidos separados únicamente por ; (punto y coma). No uses comas.'),
    greeting: z.string({ message: 'El saludo es obligatorio.' }).min(1, 'El saludo es obligatorio.').max(500, 'Texto demasiado largo.'),
    before_equipment: z.string({ message: 'El párrafo principal es obligatorio.' }).min(1, 'El párrafo principal es obligatorio.').max(3000, 'Texto demasiado largo.'),
    after_equipment: z.string({ message: 'El cierre es obligatorio.' }).min(1, 'El cierre es obligatorio.').max(3000, 'Texto demasiado largo.'),
}));

const { errors, values, setFieldValue, setValues, resetForm, handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues: {
        document_type: DeliveryRecordType.ASSIGNMENT,
        email_to: '',
        email_cc: '',
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
            toChips.value = [];
            ccChips.value = [];
            toQuery.value = '';
            ccQuery.value = '';
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

    ensureUsersForSuggestions();

    const selectedType = types.length === 1 ? types[0] : DeliveryRecordType.ASSIGNMENT;
    const defaults = getDefaultTextParts(assignment, selectedType);

    setValues({
        document_type: selectedType,
        email_to: '',
        email_cc: '',
        greeting: defaults.greeting,
        before_equipment: defaults.introParagraph,
        after_equipment: defaults.closingParagraph,
    });

    toChips.value = [];
    ccChips.value = [];
    toQuery.value = '';
    ccQuery.value = '';
});

const submitForm = () => {
    consumeRecipientQuery('to');
    consumeRecipientQuery('cc');
    handleSendEmail();
};

const handleSendEmail = handleSubmit((formValues) => {
    if (!props.assignment) {
        toast.error('No se pudo determinar la asignación para enviar el correo.');
        return;
    }

    isSendingEmail.value = true;

    router.post(`/assets/delivery-records/${props.assignment.id}/send-email`, {
        document_type: formValues.document_type,
        email_to: formValues.email_to,
        email_cc: formValues.email_cc,
        message: buildComposedMessage(formValues),
        message_sections: buildMessageSections(formValues),
        extra_images: extraImages.value,
    }, {
        forceFormData: true,
        only: [],
        onFlash(flash) {
            if (flash.error) return;

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
