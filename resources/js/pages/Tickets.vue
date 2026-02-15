<template>

    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-muted-foreground">Gestión de tickets</p>
                    <h1 class="text-2xl font-bold leading-tight">Matriz de Tickets</h1>
                    <p class="text-sm text-muted-foreground">Seguimiento centralizado de solicitudes, incidencias y tareas con detalles de estado, prioridad y asignación.</p>
                </div>
                   
              
            <div class="ml-auto flex items-center gap-2">
                <SLAHelperInfo />

                <Button class="w-fit shadow-md hover:shadow-lg transition-all gap-2" @click="open = true">
                    <Tag class="size-4" />
                    Nuevo Ticket
                </Button>
            </div>
            </div>
            

            <div class="relative min-h-screen flex-1 rounded-xl md:min-h-min">
                <!-- <PlaceholderPattern /> -->
                <Table :tickets="tickets" @update:ticket="(ticket) => {
                    selectedTicket = ticket;
                    open = true;
                }" />

                <UpsertDialog v-model:open="open" v-model:selected="selectedTicket" />

           
            </div>
        </div>
    </AppLayout>
</template>


<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Table from '@/components/tickets/Table.vue';
import UpsertDialog from '@/components/tickets/UpsertDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import { Department } from '@/interfaces/department.interace';
import { SlaPolicy } from '@/interfaces/slaPolicy.interface';
import { Ticket } from '@/interfaces/ticket.interface';
import { type User } from '@/interfaces/user.interface';
import { Paginated, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Tag } from 'lucide-vue-next';
import { ref } from 'vue';


import SLAHelperInfo from '@/components/tickets/SLAHelperInfo.vue';

defineProps<{
    slaPolicies: SlaPolicy[];
    departments: Department[];
    tickets: Paginated<Ticket>;
    TIUsers: User[];

}>();

const open = ref(false);
const selectedTicket = ref<Ticket | null>(null);



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: "#"
    },

];
</script>
