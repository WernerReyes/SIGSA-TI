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
                        <Button variant="outline" @click="openModelEditor = true">
                            <Boxes />
                            Nuevo modelo
                        </Button>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-4 shadow-sm text-sm text-muted-foreground">
                    La asociacion ahora se define directamente al crear/editar cada modelo, seleccionando su marca y su
                    tipo.
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
            v-model:open="openModelEditor" />

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
import { computed, ref } from 'vue';
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

</script>
