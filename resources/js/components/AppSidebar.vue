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
// import { dashboard } from '@/routes';
import { DepartmentAllowed } from '@/interfaces/department.interace';
import { type User } from '@/interfaces/user.interface';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Laptop, LayoutGrid, Tag } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';


const user = computed<User>(() => usePage()?.props?.auth?.user as User);



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

    if (user.value?.dept_id === DepartmentAllowed.SYSTEM_TI) { // SYSTEM_TI
        items.push({
            title: 'Activos TI',
            href: "/assets",
            icon: Laptop,
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
