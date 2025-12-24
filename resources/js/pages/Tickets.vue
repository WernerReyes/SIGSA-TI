<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { dashboard } from '@/routes';
import Dialog from '@/components/tickets/Dialog.vue';
import Table from '@/components/tickets/Table.vue';
import { type User } from '@/interfaces/user.interface';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { Department } from '@/interfaces/department.interace';
import { Ticket } from '@/interfaces/ticket.interface';
import Table2 from '@/components/tickets/Table2.vue';

defineProps<{
    departments: Department[];
    tickets: Ticket[];

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

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Dialog v-model="toggleModel" :departments="departments" />

            <!-- <div class="grid auto-rows-min gap-4 md:grid-cols-3">
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
            </div> -->
            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <!-- <PlaceholderPattern /> -->
                <Table :tickets="tickets" />

                <!-- <Table2 /> -->
            </div>
        </div>
    </AppLayout>
</template>
