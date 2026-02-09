<script setup lang="ts">
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { useAppearance } from '@/composables/useAppearance';
import { useEchoModel } from '@laravel/echo-vue';
import { onMounted } from 'vue';
import { useApp } from '@/composables/useApp';
const { appearance } = useAppearance();

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const { userAuth, echo } = useApp();


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
