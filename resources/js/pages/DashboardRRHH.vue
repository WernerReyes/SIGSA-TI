<script setup lang="ts">
import AssetAssignProfitRateDonut from '@/components/dashboard-rrhh/AssetAssignProfitRateDonut.vue';
import AssetsByStatusBarChart from '@/components/dashboard-rrhh/AssetsByStatusBarChart.vue';
import AssetsByWarrantyCircle from '@/components/dashboard-rrhh/AssetsByWarrantyCircle.vue';
import MonthlyAssetAcquisitions from '@/components/dashboard-rrhh/MonthlyAssetAcquisitions.vue';
import RRHHAssetsTable from '@/components/dashboard-rrhh/RRHHAssetsTable.vue';
import SmartphonesByBrandDonut from '@/components/dashboard-rrhh/SmartphonesByBrandDonut.vue';
import SmartphonesByDeparmentChart from '@/components/dashboard-rrhh/SmartphonesByDeparmentChart.vue';
import Stats from '@/components/dashboard-rrhh/Stats.vue';
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';




const { stats,
    assetsByBrand,
    assetsByStatus, smartphonesWarrantyStatus, assignedProfiRate, monthlyAssignments, monthlyAcquisitions, assetsByDepartment, recentAssets } = defineProps<RRHHDashboard>();

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
            <Stats :stats="stats" />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <SmartphonesByBrandDonut :assets-by-brand="assetsByBrand" />
                <AssetsByStatusBarChart :assets-by-status="assetsByStatus" />
                <AssetsByWarrantyCircle :smartphones-warranty-status="smartphonesWarrantyStatus" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <AssetAssignProfitRateDonut :assigned-profi-rate="assignedProfiRate" />
                <MonthlyAssetAcquisitions :title="'Asignaciones Mensuales'" :monthly-assignments="monthlyAssignments" />
                <MonthlyAssetAcquisitions :title="'Adquisiciones Mensuales'"
                    :monthly-assignments="monthlyAcquisitions" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <SmartphonesByDeparmentChart :assets-by-department="assetsByDepartment" />
                <!-- <SmartphonesByBrandDonut /> -->
                <div class="col-span-1 lg:col-span-2">

                    <RRHHAssetsTable :recent-assets="recentAssets" />
                </div>
            </div>
            <!-- 
      
             -->

        </div>
    </AppLayout>
</template>
