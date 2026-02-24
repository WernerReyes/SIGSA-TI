<template>
    <Head title="Modelos de Activos" />
    <AppLayout :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <DialogModel v-model:current-model="currentModel" v-model:open="openEditor" :models="models" />
                </div>

                <div class="space-y-4">
                    <ModelsTable :models="models" @edit="onEditModel" @delete="onDeleteModel" />
                </div>
            </div>
        </SettingsLayout>

        <AlertDialog title="Confirmar eliminación"
            description="¿Estás seguro de que deseas eliminar este modelo? Esta acción no se puede deshacer."
            @confirm="handleDeleteModel" @cancel="currentModel = null; openDeleteDialog = false"
            v-model:open="openDeleteDialog" />
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import AlertDialog from '@/components/AlertDialog.vue';
import { type BreadcrumbItem } from '@/types';
import { AssetModel } from '@/interfaces/assetModel.interface';
import ModelsTable from '@/components/models/Table.vue';
import DialogModel from '@/components/models/DialogModel.vue';

defineProps<{
    models: AssetModel[];
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Modelos',
        href: '#',
    },
];

const currentModel = ref<AssetModel | null>(null);
const openEditor = ref(false);
const openDeleteDialog = ref(false);

function onEditModel(model: AssetModel) {
    currentModel.value = model;
    openEditor.value = true;
}

function onDeleteModel(model: AssetModel) {
    currentModel.value = model;
    openDeleteDialog.value = true;
}

function handleDeleteModel() {
    if (!currentModel.value) return;

    router.delete('/settings/models/' + currentModel.value.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            openDeleteDialog.value = false;
            router.replaceProp('models', (models: AssetModel[]) => models.filter(model => model.id !== currentModel.value?.id));
            currentModel.value = null;
        },
    });
}
</script>
