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
                        @renew-contract="selectedRenewContract = $event, showRenewContract = true" @cancel-contract="($event) => {
                            alertProps = {
                                title: 'Confirmar cancelación',
                                description: '¿Estás seguro de que deseas cancelar este contrato? Esta acción no se puede deshacer.',
                                onConfirm: () => handleCancelContract($event!),
                            };
                            showAlert = true;
                        }" @delete-contract="($event) => {
                            alertProps = {
                                title: 'Confirmar eliminación',
                                description: '¿Estás seguro de que deseas eliminar este contrato? Esta acción no se puede deshacer.',
                                onConfirm: () => handleDeleteContract($event!),
                            };
                            showAlert = true;
                        }" />
                </TabsContent>

                <TabsContent value="events" class="mt-4 space-y-4">
                    <EventsLog :events="infrastructureEvents" />
                </TabsContent>
            </Tabs>
        </div>



        <UpsertContractDialog v-model:open="showUpsertContract" v-model:selected-contract="selectedContract" />
        <ContractDetailsDialog v-model:open="showContractDetails" v-model:selected-contract="selectedContractDetail"
            :notifications="notifications" />
        <RenewContractDialog v-model:open="showRenewContract" v-model:selected-contract="selectedRenewContract" />


        <AlertDialog v-model:open="showAlert" :title="alertProps.title" :description="alertProps.description"
            @confirm="alertProps.onConfirm"
             
            />


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
import { ContractStatus, type Contract, type NotificationContract } from '@/interfaces/contract.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { NotificationEntity } from '../interfaces/notification.interface';
import AlertDialog from '@/components/AlertDialog.vue';
import { InfrastructureEvent } from '@/interfaces/infrastructureEvent.interface';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administrativo y Control',
        href: '#',
    },
];

const props = defineProps<{
    contracts: Contract[];
    notifications: NotificationContract[];
    infrastructureEvents: InfrastructureEvent[];
}>();

const notifications = ref(props.notifications);


const { userAuth, echo } = useApp();

const selectedContract = ref<Contract | null>(null);
const selectedContractDetail = ref<Contract | null>(null);
const selectedRenewContract = ref<Contract | null>(null);

const showUpsertContract = ref(false);
const showContractDetails = ref(false);
const showRenewContract = ref(false);
const showAlert = ref(false);

const alertProps = ref({
    // open: false,
    title: '',
    description: '',
    onConfirm: () => { },
    // onCancel: () => { },
});



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

const handleCancelContract = (contract: Contract) => {
    router.post(`/admin-control/${contract.id}/cancel`, {}, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('contracts', (prev: Contract[]) => {
                return prev.map(c => {
                    if (c.id === contract.id) {
                        return {
                            ...c, status: ContractStatus.CANCELED,
                            billing: {
                                ...c.billing,
                                auto_renew: false
                            },
                            expiration: null
                        };
                    }
                    return c;
                });
            });

            

        }
    });
};

const handleDeleteContract = (contract: Contract) => {
    router.delete(`/admin-control/${contract.id}`, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('contracts', (prev: Contract[]) => {
                return prev.filter(c => c.id !== contract.id);
            });

            notifications.value = notifications.value.filter(n => n.entity_id !== contract.id);

        }
    });
};

</script>
