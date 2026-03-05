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
                        <Button variant="outline" :disabled="!selectedTypeId" @click="openBrandEditor = true">
                            <Tag />
                            Nueva marca
                        </Button>
                        <Button variant="outline" :disabled="!selectedTypeId || !selectedBrandForModelId" @click="openNewModelFromFlow">
                            <Boxes />
                            Nuevo modelo (opcional)
                        </Button>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-4 shadow-sm text-sm text-muted-foreground">
                    Flujo recomendado: 1) crea/selecciona tipo, 2) asocia marcas al tipo, 3) crea modelos para una marca (opcional).
                </div>

                <div class="rounded-xl border bg-card p-4 shadow-sm space-y-3">
                    <h3 class="text-sm font-semibold">Paso 1 y 2: Tipo y Marcas</h3>

                    <div class="grid gap-2 md:grid-cols-2">
                        <SelectFilters full-width :items="types" item-value="id" item-label="name"
                            label="Selecciona tipo" data-key="types" :icon="MonitorSmartphone" :show-refresh="false"
                            :show-selected-focus="false" :selected-as-label="true"
                            :default-value="selectedTypeId ?? undefined"
                            @select="(value) => selectedTypeId = value" />

                        <SelectFilters full-width :multiple="true" :items="brands" item-value="id" item-label="name"
                            label="Selecciona marcas" data-key="brands" :icon="Tag" :show-refresh="false"
                            :show-selected-focus="false" :selected-as-label="true" :default-value="selectedBrandIds"
                            @select="(values) => selectedBrandIds = values" :max-label-length="1" />
                    </div>

                    <Button class="w-full" :disabled="!selectedTypeId" @click="syncTypeBrands">
                        Guardar marcas del tipo
                    </Button>
                </div>

                <div class="rounded-xl border bg-card p-4 shadow-sm space-y-3">
                    <h3 class="text-sm font-semibold">Paso 3 (Opcional): Modelo</h3>
                    <p class="text-xs text-muted-foreground">
                        Selecciona una marca ya asociada al tipo para crear un modelo especifico. Puedes omitir este paso.
                    </p>

                    <SelectFilters full-width :items="brandsForSelectedType" item-value="id" item-label="name"
                        label="Marca para el modelo" data-key="brands" :icon="Tag" :show-refresh="false"
                        :show-selected-focus="false" :selected-as-label="true" :default-value="selectedBrandForModelId ?? undefined"
                        @select="(value) => selectedBrandForModelId = value" />

                    <Button class="w-full" variant="outline" :disabled="!selectedTypeId || !selectedBrandForModelId"
                        @click="openNewModelFromFlow">
                        Crear modelo (opcional)
                    </Button>
                </div>

                <div class="space-y-4">
                    <TypesTable :types="types" @edit="onEditType" @delete="onDeleteType" @edit-brand="(brand) => {
                        currentBrand = brand;
                        openBrandEditor = true;
                    }" 

                    @delete-brand="onDeleteBrand" @delete-model="onDeleteModel"
                    

                    @edit-model="(model) => {
                        currentModel = model;
                        openModelEditor = true;
                    }" />
                </div>

            </div>
        </SettingsLayout>

        <DialogType v-model:current-type="currentType" v-model:open="openEditor" />
        <DialogBrand :brands="brands" v-model:current-brand="currentBrand" v-model:open="openBrandEditor" />
        <DialogModel :models="models" :brands="brands" :types="types" v-model:current-model="currentModel"
            v-model:open="openModelEditor" :initial-brand-id="selectedBrandForModelId"
            :initial-type-id="selectedTypeId" :lock-context="true" />

        <AlertDialog title="Confirmar eliminación" :description="deleteInfo?.description" @confirm="() => {
            deleteInfo?.confirm();
            deleteInfo = null;
        }" @cancel="() => {
                deleteInfo?.cancel();
                deleteInfo = null;
            }" v-model:open="openDeleteDialog"></AlertDialog>
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
import { Boxes, Tag, MonitorSmartphone } from 'lucide-vue-next';
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

const selectedTypeId = ref<number | null>(null);
const selectedBrandIds = ref<number[]>([]);
const selectedBrandForModelId = ref<number | null>(null);

const brandsForSelectedType = computed(() => {
    return brands.value.filter(brand => selectedBrandIds.value.includes(brand.id));
});

watch(selectedTypeId, (typeId) => {
    const selectedType = types.value.find(type => type.id === typeId);
    selectedBrandIds.value = selectedType?.brand_ids ?? [];
    selectedBrandForModelId.value = null;
});

watch(selectedBrandIds, (brandIds) => {
    if (!selectedBrandForModelId.value) return;
    if (!brandIds.includes(selectedBrandForModelId.value)) {
        selectedBrandForModelId.value = null;
    }
});

const deleteInfo = ref<{ description: string, confirm: () => void, cancel: () => void } | null>(null);




const openDeleteDialog = ref(false);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Tipo de Activo',
        href: "#",
    },
];


function onEditType(type: AssetType | null) {
    currentType.value = type;
    openEditor.value = true;
}

function onDeleteType(type: AssetType) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar este tipo de activo? Esta acción no se puede deshacer.',
        confirm: () => {
            handleDeleteType();
        },
        cancel: () => {
            currentType.value = null;
        }
    };
    currentType.value = type;
    openDeleteDialog.value = true;
}

function onDeleteBrand(brand: Brand) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar esta marca? Esta acción no se puede deshacer.',
        confirm: () => {
            handleDeleteBrand(brand);
        },
        cancel: () => {
            currentBrand.value = null;
        }
    };
    currentBrand.value = brand;
    openDeleteDialog.value = true;
}

function onDeleteModel(model: AssetModel) {
    deleteInfo.value = {
        description: '¿Estás seguro de que deseas eliminar este modelo? Esta acción no se puede deshacer.',
        confirm: () => {
            handleDeleteModel(model);
        },
        cancel: () => {
            currentModel.value = null;
        }
    };
    currentModel.value = model;
    openDeleteDialog.value = true;
}


const handleDeleteType = () => {
    if (currentType.value) {
        router.delete('/settings/asset-types/' + currentType.value.id, {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                openDeleteDialog.value = false;
                router.replaceProp('types', (types: AssetType[]) => {
                    return types.filter(type => type.id !== currentType.value?.id);
                });
                currentType.value = null;
            }
        });
    }
};

const handleDeleteBrand = (brand: Brand) => {
    router.delete('/settings/brands/' + brand.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('brands', (brands: Brand[]) => {
                return brands.filter(b => b.id !== brand.id);
            });

            currentBrand.value = null;
        }
    });
};

const handleDeleteModel = (model: AssetModel) => {
    router.delete('/settings/models/' + model.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('models', (models: AssetModel[]) => {
                return models.filter(m => m.id !== model.id);
            });
            currentModel.value = null;
        }
    });
};

function syncTypeBrands() {
    if (!selectedTypeId.value) return;

    router.put(`/settings/asset-types/${selectedTypeId.value}/brands`, {
        brand_ids: selectedBrandIds.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            const relationPayload = (flash as { typeBrands?: { id: number; brand_ids: number[] } }).typeBrands;
            if (flash.error || !relationPayload) return;
            router.replaceProp('types', (items: AssetType[]) =>
                items.map(type => type.id === relationPayload.id
                    ? { ...type, brand_ids: relationPayload.brand_ids }
                    : type),
            );
        },
    });
}

function openNewModelFromFlow() {
    if (!selectedTypeId.value || !selectedBrandForModelId.value) return;
    currentModel.value = null;
    openModelEditor.value = true;
}

</script>
