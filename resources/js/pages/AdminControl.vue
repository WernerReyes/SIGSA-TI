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
                    <Button class="gap-2" size="sm" @click="showUpsertContract = true">
                        <Plus class="h-4 w-4" />
                        Nuevo Contrato
                    </Button>
                </div>
            </header>


            <AlertsPanel v-model:notifications="notifications" />
            <!-- <StatsOverview /> -->

            <Tabs default-value="contracts" class="w-full">
                <TabsList class="grid w-full max-w-md grid-cols-2">
                    <TabsTrigger value="contracts">Contratos</TabsTrigger>
                    <TabsTrigger value="events">Bitácora de eventos</TabsTrigger>
                </TabsList>

                <TabsContent value="contracts" class="mt-4 space-y-4">
                    <ContractsTable :contracts="contracts"
                        @edit-contract="selectedContract = $event, showUpsertContract = true"
                        @view-details="selectedContractDetail = $event, showContractDetails = true"
                        @renew-contract="selectedRenewContract = $event, showRenewContract = true" />
                </TabsContent>

                <TabsContent value="events" class="mt-4 space-y-4">
                    <EventsLog />
                </TabsContent>
            </Tabs>
        </div>



        <UpsertContractDialog v-model:open="showUpsertContract" v-model:selected-contract="selectedContract" />
        <ContractDetailsDialog v-model:open="showContractDetails" v-model:selected-contract="selectedContractDetail"
            :notifications="notifications" />
        <RenewContractDialog v-model:open="showRenewContract" v-model:selected-contract="selectedRenewContract" />
    </AppLayout>
</template>

<script setup lang="ts">
import AlertsPanel from '@/components/admin-control/AlertsPanel.vue';
import ContractDetailsDialog from '@/components/admin-control/ContractDetailsDialog.vue';
import ContractsTable from '@/components/admin-control/ContractsTable.vue';
import EventsLog from '@/components/admin-control/EventsLog.vue';
import RenewContractDialog from '@/components/admin-control/RenewContractDialog.vue';
import UpsertContractDialog from '@/components/admin-control/UpsertContractDialog.vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useApp } from '@/composables/useApp';
import { type Contract, type NotificationContract } from '@/interfaces/contract.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { NotificationEntity } from '../interfaces/notification.interface';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administrativo y Control',
        href: '#',
    },
];

const props = defineProps<{
    contracts: Contract[];
    notifications: NotificationContract[];
}>();

const notifications = ref(props.notifications);


const { userAuth, echo } = useApp();

const selectedContract = ref<Contract | null>(null);
const selectedContractDetail = ref<Contract | null>(null);
const selectedRenewContract = ref<Contract | null>(null);

const showUpsertContract = ref(false);
const showContractDetails = ref(false);
const showRenewContract = ref(false);



onMounted(() => {
    echo.channel().notification((notification: {
        message: string,
        short: string,
        contract: Contract
    }) => {
        // if (!props.contracts.some(n => n.id === notification.contract.id)) {
            notifications.value = [{
                id: new Date().getTime().toString(),
                type: NotificationEntity.CONTRACT,
                notifiable_type: '',
                notifiable_id: userAuth.value.staff_id,
                entity_id: notification.contract.id,
                data: JSON.stringify({
                    message: notification.short
                }),
                read_at: null,
                created_at: new Date(),
                updated_at: new Date(),
                contract: notification.contract
            }, ...notifications.value]
        // }


    });
});

</script>
