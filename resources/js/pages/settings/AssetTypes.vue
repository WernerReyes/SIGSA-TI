<template>

    <Head title="Tipos de Activos" />
    <AppLayout :breadcrumbs="breadcrumbItems">


        <SettingsLayout>
            <div class="space-y-6">

                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <DialogType includeButton v-model:current-type="currentType" v-model:open="openEditor" />
                </div>
                <div class="space-y-4">
                    <TypesTable :types="types" @edit="onEditType" @delete="onDeleteType" />
                </div>

            </div>
        </SettingsLayout>

        <AlertDialog title="Confirmar eliminación"
            description="¿Estás seguro de que deseas eliminar este tipo de activo? Esta acción no se puede deshacer."
            @confirm="handleDeleteType" @cancel="currentType = null; openDeleteDialog = false"
            v-model:open="openDeleteDialog"></AlertDialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import TypesTable from '@/components/types/Table.vue';
import DialogType from '@/components/types/DialogType.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import AlertDialog from '@/components/AlertDialog.vue';
import { AssetType } from '@/interfaces/assetType.interface';

const props = defineProps({
    types: Array,
});

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Tipo de Activo',
        href: "#",
    },
];


const currentType = ref<AssetType | null>(null);
const openEditor = ref(false);
const openDeleteDialog = ref(false);

function onEditType(type: AssetType) {
    currentType.value = type;
    openEditor.value = true;
}

function onDeleteType(type: AssetType) {
    currentType.value = type;
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
                router.replaceProp('assetTypes', (types: AssetType[]) => {
                    return types.filter(type => type.id !== currentType.value?.id);
                });
                currentType.value = null;
            }
        });
    }
};  
</script>
