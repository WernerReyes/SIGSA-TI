<template>

    <Head title="Tipos de Activos" />
    <AppLayout :breadcrumbs="breadcrumbItems">


        <SettingsLayout>
            <div class="space-y-6">

                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

                    <DialogType includeButton v-model:current-type="currentType" v-model:open="openEditor" />
                </div>
                <div class="space-y-4">
                    <TypesTable :types="types" @edit="onEditType" />
                </div>

            </div>
        </SettingsLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import TypesTable from '@/components/types/Table.vue';
import DialogType from '@/components/types/DialogType.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

const props = defineProps({
    types: Array,
});

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Tipo de Activo',
        href: "#",
    },
];

const currentType = ref(null);
const openEditor = ref(false);

function onEditType(type: any) {
    currentType.value = type;
    openEditor.value = true;
}
</script>
