<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
// import { edit } from '@/routes/appearance';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de apariencia',
        href: "#",
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Configuración de apariencia" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Configuración de apariencia"
                    description="Personaliza la apariencia de tu aplicación."
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
