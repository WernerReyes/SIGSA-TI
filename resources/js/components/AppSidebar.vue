<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useApp } from '@/composables/useApp';
import { type NavItem } from '@/types';
import { CodeXml, FileText, Laptop, LayoutGrid, MonitorCog, Tag } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';



const { isFromTI, isFromRRHH } = useApp();


const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // if (!isFromRRHH.value) { // SYSTEM_TI
        items.push({
            title: 'Dashboard',
            href:  isFromRRHH.value ? "/dashboard-rrhh" : "/dashboard",
            icon: LayoutGrid,
        });
    // }

    if (isFromTI.value) { // SYSTEM_TI or RRHH
        items.push({
            title: 'Tickets',
            href: "/tickets",
            icon: Tag,
        });

    }

    if (isFromTI.value || isFromRRHH.value) { // SYSTEM_TI or RRHH
        items.push({
            title: 'Activos TI',
            href: "/assets",
            icon: Laptop,
        });

    }

    if (isFromTI.value) { // SYSTEM_TI
        items.push({
            title: 'Accesos',
            href: "/access",
            icon: MonitorCog,
        }, {
            title: 'Contratos',
            href: "/admin-control",
            icon: FileText,
        });

    }

    if (isFromTI.value) {
        items.push({
            title: 'Desarrollos',
            href: "/developments",
            icon: CodeXml
        });
    }



    return items;
});


</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton class="h-16 md:h-full" as-child>

                        <AppLogo />


                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>

            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
