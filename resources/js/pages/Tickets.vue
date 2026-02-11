
<template>

    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Button class="w-fit ml-auto shadow-md hover:shadow-lg transition-all gap-2" @click="open = true">
                <Tag class="size-4"  />

                Nuevo Ticket
            </Button>
            <!-- <Dialog v-model="toggleModel" :departments="departments" /> -->

            <!-- <Demo /> -->

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
            <div class="relative min-h-screen flex-1 rounded-xl md:min-h-min">
                <!-- <PlaceholderPattern /> -->
                <Table :tickets="tickets" />

                <UpsertDialog  v-model:open="open"  />

                <!-- <Table2 /> -->
            </div>
        </div>
    </AppLayout>
</template>


<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { dashboard } from '@/routes';
import Dialog from '@/components/tickets/Dialog.vue';
import Table from '@/components/tickets/Table.vue';
import { Department } from '@/interfaces/department.interace';
import { Ticket } from '@/interfaces/ticket.interface';
import { type User } from '@/interfaces/user.interface';
import { Paginated, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Demo from '@/components/tickets/Demo.vue';
import UpsertDialog from '@/components/tickets/UpsertDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import { Tag } from 'lucide-vue-next';

defineProps<{
    departments: Department[];
    tickets: Paginated<Ticket>;
    TIUsers: User[];

}>();

const open = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: "#"
    },

];
</script>
