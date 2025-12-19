<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Table from '@/components/tickets/Table.vue';
import Button from '@/components/ui/button/Button.vue';
import { Tag } from 'lucide-vue-next';
import Dialog from '@/components/tickets/Dialog.vue';
import { ref } from 'vue';

defineProps<{
    technicians: Array<any>
}>();

const toggleModel = ref<boolean>(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: "#"
    },
];
</script>

<template>

    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        {{ technicians }}
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Dialog v-model="toggleModel" />

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />

                </div>
            </div>
            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <!-- <PlaceholderPattern /> -->
                <Table />
            </div>
        </div>
    </AppLayout>
</template>
