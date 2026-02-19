<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useApp } from '@/composables/useApp';
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Contrast, MonitorSmartphone, Shield } from 'lucide-vue-next';
import { computed } from 'vue';

const { isFromTI } = useApp();

const sidebarNavItems = computed<NavItem[]>(() => {
    const items = [{
        title: 'Apariencia',
        icon: Contrast,
        href: "/settings/appearance",
    }]

    if (isFromTI.value) {
        items.unshift({
            title: 'SLA',
            icon: Shield,
            href: "/settings/sla",
        }, {
            title: 'Tipos de activo',
            icon: MonitorSmartphone,
            href: "/settings/asset-types",
        });
    }
    return items;
});


const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Configuración" description="Administra tu perfil y la configuración de tu cuenta" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button v-for="item in sidebarNavItems" :key="toUrl(item.href)" variant="ghost" :class="[
                        'w-full justify-start',
                        { 'bg-muted': urlIsActive(item.href, currentPath) },
                    ]" as-child>
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-4xl">
                <section class="w-full space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
