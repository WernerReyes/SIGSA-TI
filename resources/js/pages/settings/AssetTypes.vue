<template>
    <Head title="Tipos de Activos" />
    <AppLayout :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <Button v-if="isFromTI" @click="onEditType(null)">
                            <MonitorSmartphone />
                            Nuevo tipo
                        </Button>
                        <Button variant="outline" @click="openBrandEditor = true">
                            <Tag />
                            Nueva marca
                        </Button>
                        <Button variant="outline" @click="openModelIndependent">
                            <Boxes />
                            Nuevo modelo
                        </Button>
                    </div>
                </div>


                <div class="space-y-4">
                    <TypesTable
                        :types="types"
                        @edit="onEditType"
                        @delete="onDeleteType"
                        @edit-brand="onEditBrand"
                        @delete-brand="onDeleteBrand"
                        @delete-model="onDeleteModel"
                        @edit-model="onEditModel"
                    />
                </div>
            </div>
        </SettingsLayout>

        <DialogType v-model:current-type="currentType" v-model:open="openEditor" />
        <DialogBrand :brands="brands" :types="types" v-model:current-brand="currentBrand" v-model:open="openBrandEditor" />
        <DialogModel
            :models="models"
            :brands="brands"
            :types="types"
            v-model:current-model="currentModel"
            v-model:open="openModelEditor"
            :initial-brand-id="modelFlowContext ? assignmentBrandId : null"
            :initial-type-id="modelFlowContext ? assignmentTypeId : null"
            :lock-context="modelFlowContext"
        />

        <AlertDialog
            title="Confirmar eliminación"
            :description="deleteInfo?.description"
            @confirm="() => { deleteInfo?.confirm(); deleteInfo = null; }"
            @cancel="() => { deleteInfo?.cancel(); deleteInfo = null; }"
            v-model:open="openDeleteDialog"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import TypesTable from '@/components/types/Table.vue';
import DialogType from '@/components/types/DialogType.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import AlertDialog from '@/components/AlertDialog.vue';
import { AssetType } from '@/interfaces/assetType.interface';
import { type Brand } from '@/interfaces/brand.interface';
import { AssetModel } from '@/interfaces/assetModel.interface';
import DialogBrand from '@/components/brands/DialogBrand.vue';
import DialogModel from '@/components/models/DialogModel.vue';
import SelectFilters from '@/components/SelectFilters.vue';
import { Button } from '@/components/ui/button';
import { Boxes, Tag, MonitorSmartphone, Network } from 'lucide-vue-next';
import { useApp } from '@/composables/useApp';

const props = defineProps<{
    types: AssetType[];
    brands: Brand[];
    models: AssetModel[];
}>();

const { isFromTI } = useApp();

const types = computed(() => props.types ?? []);
const brands = computed(() => props.brands ?? []);
const models = computed(() => props.models ?? []);

const currentType = ref<AssetType | null>(null);
const currentBrand = ref<Brand | null>(null);
const currentModel = ref<AssetModel | null>(null);

const openEditor = ref(false);
const openBrandEditor = ref(false);
const openModelEditor = ref(false);
const openDeleteDialog = ref(false);
const modelFlowContext = ref(false);

const assignmentTypeId = ref<number | null>(null);
const assignmentBrandId = ref<number | null>(null);
const assignmentModelId = ref<number | null>(null);

const assignableBrands = computed(() => {
    if (!assignmentTypeId.value) return [];
    return brands.value.filter(brand => brand.type_id === assignmentTypeId.value);
});
const assignableModels = computed(() => {
    if (!assignmentBrandId.value) return [];
    return models.value.filter(model => model.brand_id === assignmentBrandId.value);
});

watch(assignmentTypeId, () => {
    assignmentBrandId.value = null;
    assignmentModelId.value = null;
});

watch(assignmentBrandId, () => {
    assignmentModelId.value = null;
});

const deleteInfo = ref<{ description: string; confirm: () => void; cancel: () => void } | null>(null);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Tipo de Activo',
        href: '#',
    },
];

function onEditType(type: AssetType | null) {
    currentType.value = type;
    openEditor.value = true;
}

function onEditBrand(brand: Brand | null) {
    currentBrand.value = brand;
    openBrandEditor.value = true;
}

function onEditModel(model: AssetModel | null) {
    modelFlowContext.value = false;
    currentModel.value = model;
    openModelEditor.value = true;
}

function onDeleteType(type: AssetType) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar este tipo de activo? Esta acción no se puede deshacer.',
        confirm: () => handleDeleteType(),
        cancel: () => {
            currentType.value = null;
        },
    };
    currentType.value = type;
    openDeleteDialog.value = true;
}

function onDeleteBrand(brand: Brand) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar esta marca? Esta acción no se puede deshacer.',
        confirm: () => handleDeleteBrand(brand),
        cancel: () => {
            currentBrand.value = null;
        },
    };
    currentBrand.value = brand;
    openDeleteDialog.value = true;
}

function onDeleteModel(model: AssetModel) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar este modelo? Esta acción no se puede deshacer.',
        confirm: () => handleDeleteModel(model),
        cancel: () => {
            currentModel.value = null;
        },
    };
    currentModel.value = model;
    openDeleteDialog.value = true;
}

function handleDeleteType() {
    if (!currentType.value) return;
    router.delete('/settings/asset-types/' + currentType.value.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            openDeleteDialog.value = false;
            router.replaceProp('types', (items: AssetType[]) => items.filter(type => type.id !== currentType.value?.id));
            currentType.value = null;
        },
    });
}

function handleDeleteBrand(brand: Brand) {
    router.delete('/settings/brands/' + brand.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('brands', (items: Brand[]) => items.filter(item => item.id !== brand.id));
            currentBrand.value = null;
        },
    });
}

function handleDeleteModel(model: AssetModel) {
    router.delete('/settings/models/' + model.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('models', (items: AssetModel[]) => items.filter(item => item.id !== model.id));
            currentModel.value = null;
        },
    });
}

function openNewModelFromFlow() {
    if (!assignmentTypeId.value || !assignmentBrandId.value) return;
    modelFlowContext.value = true;
    currentModel.value = null;
    openModelEditor.value = true;
}

function openModelIndependent() {
    modelFlowContext.value = false;
    currentModel.value = null;
    openModelEditor.value = true;
}
</script>
