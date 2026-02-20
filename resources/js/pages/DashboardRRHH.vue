<script setup lang="ts">
import AssetAssignProfitRateDonut from '@/components/dashboard-rrhh/AssetAssignProfitRateDonut.vue';
import AssetsByStatusBarChart from '@/components/dashboard-rrhh/AssetsByStatusBarChart.vue';
import AssetsByWarrantyCircle from '@/components/dashboard-rrhh/AssetsByWarrantyCircle.vue';
import MonthlyAssetAcquisitions from '@/components/dashboard-rrhh/MonthlyAssetAcquisitions.vue';
import SmartphonesByBrandDonut from '@/components/dashboard-rrhh/SmartphonesByBrandDonut.vue';
import SmartphonesByDeparmentChart from '@/components/dashboard-rrhh/SmartphonesByDeparmentChart.vue';
import Stats from '@/components/dashboard-rrhh/Stats.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard RRHH',
        href: '#',
    },
];
</script>

<template>

    <Head title="Dashboard RRHH" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
            <Stats />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <SmartphonesByBrandDonut />
                <AssetsByStatusBarChart />
            </div>

             <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <AssetsByWarrantyCircle />
                <AssetAssignProfitRateDonut />
                <MonthlyAssetAcquisitions />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <SmartphonesByDeparmentChart />
                <!-- <SmartphonesByBrandDonut /> -->
            </div>
                <!-- 
      
             -->

        </div>
    </AppLayout>
</template>
