<template>

    <Head title="Matriz de Accesos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 lg:p-6">
            <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-muted-foreground">Gestión de identidad</p>
                    <h1 class="text-2xl font-bold leading-tight">Matriz de Accesos</h1>
                    <p class="text-sm text-muted-foreground">Control centralizado de usuarios y credenciales para ERP,
                        Correo, VPN y otras plataformas.</p>
                </div>
                <div class="flex gap-2">

                    <Button class="gap-2" size="sm" @click="openServiceDialog = true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-monitor-cloud-icon lucide-monitor-cloud">
                            <path d="M11 13a3 3 0 1 1 2.83-4H14a2 2 0 0 1 0 4z" />
                            <path d="M12 17v4" />
                            <path d="M8 21h8" />
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                        </svg>
                        Registrar servicio
                    </Button>
                </div>
            </header>

           
            <AccessStats :services="services" />

            <AccessTable :rows="services"
                @update:service="currentService = $event">
            </AccessTable>

           

            <ServiceDialog v-model:open="openServiceDialog" :service="currentService" />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import AccessTable from '@/components/access/Table.vue';
import AccessStats from '@/components/access/Stats.vue';
import AccessHistoryPanel from '@/components/access/HistoryPanel.vue';
import ServiceDialog from '@/components/access/ServiceDialog.vue';
import { KeyRound, Mail, Server, ShieldCheck, UserMinus, UserPlus, XCircle, CheckCircle2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

import { Service } from '@/interfaces/service.interface';


defineProps<{
    services: Service[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Accesos',
        href: '#'
    },
];

const currentService = ref<Service | null>(null);
const openServiceDialog = ref(false);


</script>