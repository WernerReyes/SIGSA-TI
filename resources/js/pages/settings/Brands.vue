<template>
    <Head title="Marcas de Activos" />
    <AppLayout :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <DialogBrand v-model:current-brand="currentBrand" v-model:open="openEditor" :brands="brands" />
                </div>

                <div class="space-y-4">
                    <BrandsTable :brands="brands" @edit="onEditBrand" @delete="onDeleteBrand" />
                </div>
            </div>
        </SettingsLayout>

        <AlertDialog title="Confirmar eliminación"
            description="¿Estás seguro de que deseas eliminar esta marca? Esta acción no se puede deshacer."
            @confirm="handleDeleteBrand" @cancel="currentBrand = null; openDeleteDialog = false"
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
import { Brand } from '@/interfaces/brand.interface';
import BrandsTable from '@/components/brands/Table.vue';
import DialogBrand from '@/components/brands/DialogBrand.vue';

defineProps<{
    brands: Brand[];
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Marcas',
        href: '#',
    },
];

const currentBrand = ref<Brand | null>(null);
const openEditor = ref(false);
const openDeleteDialog = ref(false);

function onEditBrand(brand: Brand) {
    currentBrand.value = brand;
    openEditor.value = true;
}

function onDeleteBrand(brand: Brand) {
    currentBrand.value = brand;
    openDeleteDialog.value = true;
}

function handleDeleteBrand() {
    if (!currentBrand.value) return;

    router.delete('/settings/brands/' + currentBrand.value.id, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            openDeleteDialog.value = false;
            router.replaceProp('brands', (brands: Brand[]) => brands.filter(brand => brand.id !== currentBrand.value?.id));
            currentBrand.value = null;
        },
    });
}
</script>
