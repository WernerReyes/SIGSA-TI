<script setup lang="ts">
import { useApp } from '@/composables/useApp';
import { useAppearance } from '@/composables/useAppearance';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { onMounted } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
const { appearance } = useAppearance();

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const { echo } = useApp();


onMounted(() => {
    echo.channel().notification((notification: {
        message: string,
    }) => {
        toast.success(notification.message);
    });
});


withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Toaster position="top-right" :theme="appearance" :closeButton="true" closeButtonPosition="top-right" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
