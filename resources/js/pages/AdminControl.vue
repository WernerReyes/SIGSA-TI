<template>
    <Head title="Administrativo y Control" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 lg:p-6">
            <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-muted-foreground">
                        Módulo Administrativo y Control
                    </p>
                    <h1 class="text-2xl font-bold leading-tight">Gestión de Contratos y Eventos</h1>
                    <p class="text-sm text-muted-foreground">
                        Administra vencimientos, garantías, licencias y bitácoras operativas.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Button class="gap-2" size="sm" @click="showCreateContract = true">
                        <Plus class="h-4 w-4" />
                        Nuevo Contrato
                    </Button>
                </div>
            </header>

            <AlertsPanel />
            <StatsOverview />

            <Tabs default-value="contracts" class="w-full">
                <TabsList class="grid w-full max-w-md grid-cols-2">
                    <TabsTrigger value="contracts">Contratos</TabsTrigger>
                    <TabsTrigger value="events">Bitácora de eventos</TabsTrigger>
                </TabsList>

                <TabsContent value="contracts" class="mt-4 space-y-4">
                    <ContractsTable />
                </TabsContent>

                <TabsContent value="events" class="mt-4 space-y-4">
                    <EventsLog />
                </TabsContent>
            </Tabs>
        </div>

        <CreateContractDialog v-model:open="showCreateContract" />
    </AppLayout>
</template>

<script setup lang="ts">
import AlertsPanel from '@/components/admin-control/AlertsPanel.vue';
import ContractsTable from '@/components/admin-control/ContractsTable.vue';
import CreateContractDialog from '@/components/admin-control/CreateContractDialog.vue';
import EventsLog from '@/components/admin-control/EventsLog.vue';
import StatsOverview from '@/components/admin-control/StatsOverview.vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administrativo y Control',
        href: '#',
    },
];

const showCreateContract = ref(false);
</script>
