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
import { Code, CodeXml, FileText, Laptop, LayoutGrid, MonitorCog, Tag } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';



const { isFromTI } = useApp();


const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: "/dashboard",
            icon: LayoutGrid,
        },
        {
            title: 'Tickets',
            href: "/tickets",
            icon: Tag,
        },
    ];

    if (isFromTI.value) { // SYSTEM_TI
        items.push({
            title: 'Activos TI',
            href: "/assets",
            icon: Laptop,
        }, {
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
