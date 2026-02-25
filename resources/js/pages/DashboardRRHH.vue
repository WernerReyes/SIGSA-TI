<script setup lang="ts">
import AssetAssignProfitRateDonut from '@/components/dashboard-rrhh/AssetAssignProfitRateDonut.vue';
import AssetsByStatusBarChart from '@/components/dashboard-rrhh/AssetsByStatusBarChart.vue';
import AssetsByWarrantyCircle from '@/components/dashboard-rrhh/AssetsByWarrantyCircle.vue';
import MonthlyAssetAcquisitions from '@/components/dashboard-rrhh/MonthlyAssetAcquisitions.vue';
import RRHHAssetsTable from '@/components/dashboard-rrhh/RRHHAssetsTable.vue';
import SmartphonesByBrandDonut from '@/components/dashboard-rrhh/SmartphonesByBrandDonut.vue';
import SmartphonesByDeparmentChart from '@/components/dashboard-rrhh/SmartphonesByDeparmentChart.vue';
import Stats from '@/components/dashboard-rrhh/Stats.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { RRHHDashboard } from '@/interfaces/dashboard.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { getLocalTimeZone, parseDate } from '@internationalized/date';
import { Head, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { ChevronDownIcon, Filter, Search } from 'lucide-vue-next';
import { type DateRange } from 'reka-ui';
import { computed, ref, watch } from 'vue';

const { stats,
    assetsByBrand,
    assetsByStatus,
    smartphonesWarrantyStatus,
    assignedProfiRate,
    monthlyAssignments,
    monthlyAcquisitions,
    assetsByDepartment,
    recentAssets,
    brands,
    activeFilters,
} = defineProps<RRHHDashboard>();

const search = ref(activeFilters.search ?? '');
const selectedBrand = ref(activeFilters.brand ? String(activeFilters.brand) : 'all');
const selectedStatus = ref(activeFilters.status ?? 'all');
const selectedWarranty = ref(activeFilters.warranty ?? 'all');

const initialStart = activeFilters.start_date ? parseDate(activeFilters.start_date) : undefined;
const initialEnd = activeFilters.end_date
    ? parseDate(activeFilters.end_date)
    : initialStart;

const dateRange = ref<DateRange | undefined>(initialStart ? {
    start: initialStart,
    end: initialEnd,
} : undefined);

const formattedDateRange = computed(() => {
    if (!dateRange.value?.start) {
        return 'Seleccionar rango';
    }

    const start = dateRange.value.start.toDate(getLocalTimeZone()).toLocaleDateString();

    if (!dateRange.value.end) {
        return start;
    }

    const end = dateRange.value.end.toDate(getLocalTimeZone()).toLocaleDateString();
    return `${start} - ${end}`;
});

const applyFilters = () => {
    const startDate = dateRange.value?.start
        ? format(dateRange.value.start.toDate(getLocalTimeZone()), 'yyyy-MM-dd')
        : undefined;
    const endDate = dateRange.value?.end
        ? format(dateRange.value.end.toDate(getLocalTimeZone()), 'yyyy-MM-dd')
        : undefined;

    router.get('/dashboard-rrhh', {
        search: search.value || undefined,
        brand: selectedBrand.value !== 'all' ? selectedBrand.value : undefined,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        warranty: selectedWarranty.value !== 'all' ? selectedWarranty.value : undefined,
        start_date: startDate,
        end_date: endDate,
    }, {
        only: ['stats', 'assetsByBrand', 'assetsByStatus', 'smartphonesWarrantyStatus', 'assignedProfiRate', 'monthlyAssignments', 'monthlyAcquisitions', 'assetsByDepartment', 'recentAssets', 'brands', 'activeFilters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    if (searchDebounce) {
        clearTimeout(searchDebounce);
        searchDebounce = null;
    }

    if (!search.value && selectedBrand.value === 'all' && selectedStatus.value === 'all' && selectedWarranty.value === 'all' && !dateRange.value) {
        return;
    }

    search.value = '';
    selectedBrand.value = 'all';
    selectedStatus.value = 'all';
    selectedWarranty.value = 'all';
    dateRange.value = undefined;

    router.get('/dashboard-rrhh', {}, {
        only: ['stats', 'assetsByBrand', 'assetsByStatus', 'smartphonesWarrantyStatus', 'assignedProfiRate', 'monthlyAssignments', 'monthlyAcquisitions', 'assetsByDepartment', 'recentAssets', 'brands', 'activeFilters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

let searchDebounce: ReturnType<typeof setTimeout> | null = null;

watch(search, () => {
    if (searchDebounce) {
        clearTimeout(searchDebounce);
    }

    searchDebounce = setTimeout(() => {
        applyFilters();
    }, 350);
});

watch([selectedBrand, selectedStatus, selectedWarranty], () => {
    applyFilters();
});

watch(dateRange, () => {
    applyFilters();
}, { deep: true });

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
            <div class="p-6 pt-4 pb-4 border rounded-xl bg-card">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <Filter class="w-4 h-4" />
                        <span class="text-sm font-medium">Filtros</span>
                    </div>

                    <div class="relative flex-1 min-w-50 max-w-xs">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="search" class="pl-9 h-9" placeholder="Buscar por nombre, ID o asignado..." />
                    </div>

                    <Select v-model="selectedBrand">
                        <SelectTrigger class="w-37.5 h-9">
                            <SelectValue placeholder="Todas las marcas" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todas las marcas</SelectItem>
                            <SelectItem v-for="brand in brands" :key="brand.id" :value="String(brand.id)">
                                {{ brand.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="selectedStatus">
                        <SelectTrigger class="w-37.5 h-9">
                            <SelectValue placeholder="Todos los estados" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todos los estados</SelectItem>
                            <SelectItem value="AVAILABLE">Disponible</SelectItem>
                            <SelectItem value="ASSIGNED">Asignado</SelectItem>
                            <SelectItem value="IN_REPAIR">En reparación</SelectItem>
                            <SelectItem value="DECOMMISSIONED">Baja</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="selectedWarranty">
                        <SelectTrigger class="w-42.5 h-9">
                            <SelectValue placeholder="Todas las garantías" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todas las garantías</SelectItem>
                            <SelectItem value="in_warranty">En garantía</SelectItem>
                            <SelectItem value="expiring_soon">Por vencer</SelectItem>
                            <SelectItem value="expired">Vencida</SelectItem>
                            <SelectItem value="unknown">Sin dato</SelectItem>
                        </SelectContent>
                    </Select>

                    <Popover>
                        <PopoverTrigger as-child>
                            <Button id="date" variant="outline" class="w-48 justify-between font-normal h-9">
                                {{ formattedDateRange }}
                                <ChevronDownIcon class="w-4 h-4" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                            <RangeCalendar v-model="dateRange as any" locales="es" class="rounded-md border shadow-sm"
                                disable-days-outside-current-view />
                        </PopoverContent>
                    </Popover>

                    <Button variant="outline" class="h-9" @click="clearFilters">
                        Limpiar filtros
                    </Button>
                </div>
            </div>

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
