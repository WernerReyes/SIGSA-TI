<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: "#",
    },
];
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Dashboard</h1>
                    <p class="text-muted-foreground mt-1">Resumen general del estado de TI</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="p-6 rounded-xl border shadow-card hover:shadow-card-hover transition-all duration-200 animate-fade-in bg-card">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-muted-foreground">Tickets Abiertos</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-3xl font-bold text-foreground">47</p><span
                                        class="text-sm font-medium text-critical">↓ 8%</span>
                                </div>
                                <p class="text-xs text-muted-foreground">12 sin asignar</p>
                            </div>
                            <div class="p-3 rounded-lg bg-primary/10 text-primary"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-ticket w-6 h-6">
                                    <path
                                        d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z">
                                    </path>
                                    <path d="M13 5v2"></path>
                                    <path d="M13 17v2"></path>
                                    <path d="M13 11v2"></path>
                                </svg></div>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-xl border shadow-card hover:shadow-card-hover transition-all duration-200 animate-fade-in bg-critical/10 border-critical/20">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-muted-foreground">Fuera de SLA</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-3xl font-bold text-foreground">5</p>
                                </div>
                                <p class="text-xs text-muted-foreground">Requieren atención inmediata</p>
                            </div>
                            <div class="p-3 rounded-lg bg-critical/20 text-critical"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-triangle-alert w-6 h-6">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3">
                                    </path>
                                    <path d="M12 9v4"></path>
                                    <path d="M12 17h.01"></path>
                                </svg></div>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-xl border shadow-card hover:shadow-card-hover transition-all duration-200 animate-fade-in bg-warning/10 border-warning/20">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-muted-foreground">Activos en Riesgo</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-3xl font-bold text-foreground">12</p>
                                </div>
                                <p class="text-xs text-muted-foreground">Garantías próximas a vencer</p>
                            </div>
                            <div class="p-3 rounded-lg bg-warning/20 text-warning"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-monitor w-6 h-6">
                                    <rect width="20" height="14" x="2" y="3" rx="2"></rect>
                                    <line x1="8" x2="16" y1="21" y2="21"></line>
                                    <line x1="12" x2="12" y1="17" y2="21"></line>
                                </svg></div>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-xl border shadow-card hover:shadow-card-hover transition-all duration-200 animate-fade-in bg-success/10 border-success/20">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-muted-foreground">Proyectos Activos</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-3xl font-bold text-foreground">8</p><span
                                        class="text-sm font-medium text-success">↑ 15%</span>
                                </div>
                                <p class="text-xs text-muted-foreground">3 en fase de desarrollo</p>
                            </div>
                            <div class="p-3 rounded-lg bg-success/20 text-success"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-code-xml w-6 h-6">
                                    <path d="m18 16 4-4-4-4"></path>
                                    <path d="m6 8-4 4 4 4"></path>
                                    <path d="m14.5 4-5 16"></path>
                                </svg></div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-card rounded-xl border shadow-card p-6 animate-fade-in">
                        <h3 class="font-semibold text-lg mb-4">Tickets por Prioridad</h3>
                        <div class="h-64">
                            <div class="recharts-responsive-container"
                                style="width: 100%; height: 100%; min-width: 0px;">
                                <div class="recharts-wrapper"
                                    style="position: relative; cursor: default; width: 100%; height: 100%; max-height: 256px; max-width: 811px;">
                                    <svg cx="50%" cy="50%" class="recharts-surface" width="811" height="256"
                                        viewBox="0 0 811 256" style="width: 100%; height: 100%;">
                                        <title></title>
                                        <desc></desc>
                                        <defs>
                                            <clipPath id="recharts1-clip">
                                                <rect x="5" y="5" height="210" width="801"></rect>
                                            </clipPath>
                                        </defs>
                                        <g class="recharts-layer recharts-pie" tabindex="0">
                                            <g class="recharts-layer">
                                                <g class="recharts-layer recharts-pie-sector" tabindex="-1">
                                                    <path cx="405.5" cy="110" name="Crítico" stroke="#fff"
                                                        fill="hsl(0, 84%, 60%)" color="hsl(0, 84%, 60%)" tabindex="-1"
                                                        class="recharts-sector" d="M 485.5,110
    A 80,80,0,
    0,0,
    476.4479678775485,73.03534317694331
  L 458.71097590816134,82.27650738270749
            A 60,60,0,
            0,1,
            465.5,110 Z" role="img"></path>
                                                </g>
                                                <g class="recharts-layer recharts-pie-sector" tabindex="-1">
                                                    <path cx="405.5" cy="110" name="Alto" stroke="#fff"
                                                        fill="hsl(25, 95%, 53%)" color="hsl(25, 95%, 53%)" tabindex="-1"
                                                        class="recharts-sector" d="M 473.696618080493,68.17630716467788
    A 80,80,0,
    0,0,
    415.08322350693834,30.576062630866346
  L 412.68741763020375,50.43204697314976
            A 60,60,0,
            0,1,
            456.6474635603698,78.6322303735084 Z" role="img"></path>
                                                </g>
                                                <g class="recharts-layer recharts-pie-sector" tabindex="-1">
                                                    <path cx="405.5" cy="110" name="Medio" stroke="#fff"
                                                        fill="hsl(38, 92%, 50%)" color="hsl(38, 92%, 50%)" tabindex="-1"
                                                        class="recharts-sector" d="M 409.51954545438156,30.101043471518608
    A 80,80,0,
    0,0,
    329.0777310302125,133.65664400352372
  L 348.1832982726594,127.74248300264279
            A 60,60,0,
            0,1,
            408.51465909078615,50.07578260363895 Z" role="img"></path>
                                                </g>
                                                <g class="recharts-layer recharts-pie-sector" tabindex="-1">
                                                    <path cx="405.5" cy="110" name="Bajo" stroke="#fff"
                                                        fill="hsl(142, 76%, 36%)" color="hsl(142, 76%, 36%)"
                                                        tabindex="-1" class="recharts-sector" d="M 330.914095902759,138.92996560656738
    A 80,80,0,
    0,0,
    485.3051240207859,115.58051789953005
  L 465.35384301558946,114.18538842464754
            A 60,60,0,
            0,1,
            349.56057192706925,131.69747420492553 Z" role="img"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <div class="recharts-legend-wrapper"
                                        style="position: absolute; width: 801px; height: 36px; left: 5px; bottom: 5px;">
                                        <ul class="recharts-default-legend"
                                            style="padding: 0px; margin: 0px; text-align: center;">
                                            <li class="recharts-legend-item legend-item-0"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(0, 84%, 60%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(239, 67, 67);"><span
                                                        class="text-sm text-foreground">Crítico</span></span></li>
                                            <li class="recharts-legend-item legend-item-1"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(25, 95%, 53%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(249, 116, 21);"><span
                                                        class="text-sm text-foreground">Alto</span></span></li>
                                            <li class="recharts-legend-item legend-item-2"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(38, 92%, 50%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(245, 159, 10);"><span
                                                        class="text-sm text-foreground">Medio</span></span></li>
                                            <li class="recharts-legend-item legend-item-3"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(142, 76%, 36%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(22, 162, 73);"><span
                                                        class="text-sm text-foreground">Bajo</span></span></li>
                                        </ul>
                                    </div>
                                    <div tabindex="-1"
                                        class="recharts-tooltip-wrapper recharts-tooltip-wrapper-right recharts-tooltip-wrapper-bottom"
                                        style="visibility: hidden; pointer-events: none; position: absolute; top: 0px; left: 0px; transform: translate(10px, 10px);">
                                        <div class="recharts-default-tooltip"
                                            style="margin: 0px; padding: 10px; background-color: hsl(var(--card)); border: 1px solid hsl(var(--border)); white-space: nowrap; border-radius: 8px;">
                                            <p class="recharts-tooltip-label" style="margin: 0px;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-card rounded-xl border shadow-card p-6 animate-fade-in">
                        <h3 class="font-semibold text-lg mb-4">Cumplimiento de SLA (Última Semana)</h3>
                        <div class="h-64">
                            <div class="recharts-responsive-container"
                                style="width: 100%; height: 100%; min-width: 0px;">
                                <div class="recharts-wrapper"
                                    style="position: relative; cursor: default; width: 100%; height: 100%; max-height: 256px; max-width: 811px;">
                                    <svg class="recharts-surface" width="811" height="256" viewBox="0 0 811 256"
                                        style="width: 100%; height: 100%;">
                                        <title></title>
                                        <desc></desc>
                                        <defs>
                                            <clipPath id="recharts3-clip">
                                                <rect x="65" y="5" height="192" width="741"></rect>
                                            </clipPath>
                                        </defs>
                                        <g class="recharts-cartesian-grid">
                                            <g class="recharts-cartesian-grid-horizontal">
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="197" x2="806"
                                                    y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="149" x2="806"
                                                    y2="149"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="101" x2="806"
                                                    y2="101"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="53" x2="806"
                                                    y2="53"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="5" x2="806"
                                                    y2="5"></line>
                                            </g>
                                            <g class="recharts-cartesian-grid-vertical">
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="117.92857142857143" y1="5"
                                                    x2="117.92857142857143" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="223.78571428571428" y1="5"
                                                    x2="223.78571428571428" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="329.64285714285717" y1="5"
                                                    x2="329.64285714285717" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="435.5" y1="5" x2="435.5"
                                                    y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="541.3571428571429" y1="5"
                                                    x2="541.3571428571429" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="647.2142857142858" y1="5"
                                                    x2="647.2142857142858" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="753.0714285714286" y1="5"
                                                    x2="753.0714285714286" y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="65" y1="5" x2="65"
                                                    y2="197"></line>
                                                <line stroke-dasharray="3 3" stroke="hsl(var(--border))" fill="none"
                                                    x="65" y="5" width="741" height="192" x1="806" y1="5" x2="806"
                                                    y2="197"></line>
                                            </g>
                                        </g>
                                        <g class="recharts-layer recharts-cartesian-axis recharts-xAxis xAxis">
                                            <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                class="recharts-cartesian-axis-line" stroke="hsl(var(--border))"
                                                fill="none" x1="65" y1="197" x2="806" y2="197"></line>
                                            <g class="recharts-cartesian-axis-ticks">
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="117.92857142857143" y1="203"
                                                        x2="117.92857142857143" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="117.92857142857143" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="117.92857142857143" dy="0.71em">Lun</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="223.78571428571428" y1="203"
                                                        x2="223.78571428571428" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="223.78571428571428" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="223.78571428571428" dy="0.71em">Mar</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="329.64285714285717" y1="203"
                                                        x2="329.64285714285717" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="329.64285714285717" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="329.64285714285717" dy="0.71em">Mié</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="435.5" y1="203" x2="435.5" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="435.5" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="435.5" dy="0.71em">Jue</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="541.3571428571429" y1="203"
                                                        x2="541.3571428571429" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="541.3571428571429" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="541.3571428571429" dy="0.71em">Vie</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="647.2142857142858" y1="203"
                                                        x2="647.2142857142858" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="647.2142857142858" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="647.2142857142858" dy="0.71em">Sáb</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="bottom" width="741" height="30" x="65" y="197"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="753.0714285714286" y1="203"
                                                        x2="753.0714285714286" y2="197"></line><text
                                                        orientation="bottom" width="741" height="30" stroke="none"
                                                        font-size="12" x="753.0714285714286" y="205"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="middle" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="753.0714285714286" dy="0.71em">Dom</tspan>
                                                    </text>
                                                </g>
                                            </g>
                                        </g>
                                        <g class="recharts-layer recharts-cartesian-axis recharts-yAxis yAxis">
                                            <line orientation="left" width="60" height="192" x="5" y="5"
                                                class="recharts-cartesian-axis-line" stroke="hsl(var(--border))"
                                                fill="none" x1="65" y1="5" x2="65" y2="197"></line>
                                            <g class="recharts-cartesian-axis-ticks">
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="left" width="60" height="192" x="5" y="5"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="59" y1="197" x2="65" y2="197"></line><text
                                                        orientation="left" width="60" height="192" stroke="none"
                                                        font-size="12" x="57" y="197"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="end" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="57" dy="0.355em">0</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="left" width="60" height="192" x="5" y="5"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="59" y1="149" x2="65" y2="149"></line><text
                                                        orientation="left" width="60" height="192" stroke="none"
                                                        font-size="12" x="57" y="149"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="end" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="57" dy="0.355em">25</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="left" width="60" height="192" x="5" y="5"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="59" y1="101" x2="65" y2="101"></line><text
                                                        orientation="left" width="60" height="192" stroke="none"
                                                        font-size="12" x="57" y="101"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="end" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="57" dy="0.355em">50</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="left" width="60" height="192" x="5" y="5"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="59" y1="53" x2="65" y2="53"></line><text
                                                        orientation="left" width="60" height="192" stroke="none"
                                                        font-size="12" x="57" y="53"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="end" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="57" dy="0.355em">75</tspan>
                                                    </text>
                                                </g>
                                                <g class="recharts-layer recharts-cartesian-axis-tick">
                                                    <line orientation="left" width="60" height="192" x="5" y="5"
                                                        class="recharts-cartesian-axis-tick-line" stroke="#666"
                                                        fill="none" x1="59" y1="5" x2="65" y2="5"></line><text
                                                        orientation="left" width="60" height="192" stroke="none"
                                                        font-size="12" x="57" y="9"
                                                        class="recharts-text recharts-cartesian-axis-tick-value"
                                                        text-anchor="end" fill="hsl(var(--muted-foreground))">
                                                        <tspan x="57" dy="0.355em">100</tspan>
                                                    </text>
                                                </g>
                                            </g>
                                        </g>
                                        <g class="recharts-layer recharts-bar">
                                            <g class="recharts-layer recharts-bar-rectangles">
                                                <g class="recharts-layer">
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="75.58571428571429" y="33.800000000000004" width="84"
                                                            height="163.2" radius="0,0,0,0" fill="hsl(142, 76%, 36%)"
                                                            name="Lun" class="recharts-rectangle"
                                                            d="M75.58571428571429,33.800000000000004L 159.5857142857143,33.800000000000004L 159.5857142857143,197L 75.58571428571429,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="181.44285714285715" y="20.359999999999992" width="84"
                                                            height="176.64000000000001" radius="0,0,0,0"
                                                            fill="hsl(142, 76%, 36%)" name="Mar"
                                                            class="recharts-rectangle"
                                                            d="M181.44285714285715,20.359999999999992L 265.4428571428572,20.359999999999992L 265.4428571428572,197L 181.44285714285715,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="287.3" y="47.239999999999995" width="84"
                                                            height="149.76" radius="0,0,0,0" fill="hsl(142, 76%, 36%)"
                                                            name="Mié" class="recharts-rectangle"
                                                            d="M287.3,47.239999999999995L 371.3,47.239999999999995L 371.3,197L 287.3,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="393.15714285714284" y="28.04" width="84"
                                                            height="168.96" radius="0,0,0,0" fill="hsl(142, 76%, 36%)"
                                                            name="Jue" class="recharts-rectangle"
                                                            d="M393.15714285714284,28.04L 477.15714285714284,28.04L 477.15714285714284,197L 393.15714285714284,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="499.01428571428573" y="14.600000000000009" width="84"
                                                            height="182.39999999999998" radius="0,0,0,0"
                                                            fill="hsl(142, 76%, 36%)" name="Vie"
                                                            class="recharts-rectangle"
                                                            d="M499.01428571428573,14.600000000000009L 583.0142857142857,14.600000000000009L 583.0142857142857,197L 499.01428571428573,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="604.8714285714286" y="24.199999999999996" width="84"
                                                            height="172.8" radius="0,0,0,0" fill="hsl(142, 76%, 36%)"
                                                            name="Sáb" class="recharts-rectangle"
                                                            d="M604.8714285714286,24.199999999999996L 688.8714285714286,24.199999999999996L 688.8714285714286,197L 604.8714285714286,197Z">
                                                        </path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="710.7285714285714" y="29.96" width="84" height="167.04"
                                                            radius="0,0,0,0" fill="hsl(142, 76%, 36%)" name="Dom"
                                                            class="recharts-rectangle"
                                                            d="M710.7285714285714,29.96L 794.7285714285714,29.96L 794.7285714285714,197L 710.7285714285714,197Z">
                                                        </path>
                                                    </g>
                                                </g>
                                            </g>
                                            <g class="recharts-layer"></g>
                                        </g>
                                        <g class="recharts-layer recharts-bar">
                                            <g class="recharts-layer recharts-bar-rectangles">
                                                <g class="recharts-layer">
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="75.58571428571429" y="5" width="84"
                                                            height="28.800000000000004" radius="4,4,0,0"
                                                            fill="hsl(0, 84%, 60%)" name="Lun"
                                                            class="recharts-rectangle" d="M75.58571428571429,9A 4,4,0,0,1,79.58571428571429,5L 155.5857142857143,5A 4,4,0,0,1,
        159.5857142857143,9L 159.5857142857143,33.800000000000004L 75.58571428571429,33.800000000000004Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="181.44285714285715" y="5" width="84"
                                                            height="15.359999999999992" radius="4,4,0,0"
                                                            fill="hsl(0, 84%, 60%)" name="Mar"
                                                            class="recharts-rectangle" d="M181.44285714285715,9A 4,4,0,0,1,185.44285714285715,5L 261.4428571428572,5A 4,4,0,0,1,
        265.4428571428572,9L 265.4428571428572,20.359999999999992L 181.44285714285715,20.359999999999992Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="287.3" y="5" width="84" height="42.239999999999995"
                                                            radius="4,4,0,0" fill="hsl(0, 84%, 60%)" name="Mié"
                                                            class="recharts-rectangle" d="M287.3,9A 4,4,0,0,1,291.3,5L 367.3,5A 4,4,0,0,1,
        371.3,9L 371.3,47.239999999999995L 287.3,47.239999999999995Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="393.15714285714284" y="5" width="84" height="23.04"
                                                            radius="4,4,0,0" fill="hsl(0, 84%, 60%)" name="Jue"
                                                            class="recharts-rectangle" d="M393.15714285714284,9A 4,4,0,0,1,397.15714285714284,5L 473.15714285714284,5A 4,4,0,0,1,
        477.15714285714284,9L 477.15714285714284,28.04L 393.15714285714284,28.04Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="499.01428571428573" y="5" width="84"
                                                            height="9.600000000000009" radius="4,4,0,0"
                                                            fill="hsl(0, 84%, 60%)" name="Vie"
                                                            class="recharts-rectangle" d="M499.01428571428573,9A 4,4,0,0,1,503.01428571428573,5L 579.0142857142857,5A 4,4,0,0,1,
        583.0142857142857,9L 583.0142857142857,14.600000000000009L 499.01428571428573,14.600000000000009Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="604.8714285714286" y="5" width="84"
                                                            height="19.199999999999996" radius="4,4,0,0"
                                                            fill="hsl(0, 84%, 60%)" name="Sáb"
                                                            class="recharts-rectangle" d="M604.8714285714286,9A 4,4,0,0,1,608.8714285714286,5L 684.8714285714286,5A 4,4,0,0,1,
        688.8714285714286,9L 688.8714285714286,24.199999999999996L 604.8714285714286,24.199999999999996Z"></path>
                                                    </g>
                                                    <g class="recharts-layer recharts-bar-rectangle">
                                                        <path x="710.7285714285714" y="5" width="84" height="24.96"
                                                            radius="4,4,0,0" fill="hsl(0, 84%, 60%)" name="Dom"
                                                            class="recharts-rectangle" d="M710.7285714285714,9A 4,4,0,0,1,714.7285714285714,5L 790.7285714285714,5A 4,4,0,0,1,
        794.7285714285714,9L 794.7285714285714,29.96L 710.7285714285714,29.96Z"></path>
                                                    </g>
                                                </g>
                                            </g>
                                            <g class="recharts-layer"></g>
                                        </g>
                                    </svg>
                                    <div class="recharts-legend-wrapper"
                                        style="position: absolute; width: 801px; height: auto; left: 5px; bottom: 5px;">
                                        <ul class="recharts-default-legend"
                                            style="padding: 0px; margin: 0px; text-align: center;">
                                            <li class="recharts-legend-item legend-item-0"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(142, 76%, 36%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(22, 162, 73);"><span
                                                        class="text-sm text-foreground capitalize">cumplido</span></span>
                                            </li>
                                            <li class="recharts-legend-item legend-item-1"
                                                style="display: inline-block; margin-right: 10px;"><svg
                                                    class="recharts-surface" width="14" height="14" viewBox="0 0 32 32"
                                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                    <title></title>
                                                    <desc></desc>
                                                    <path stroke="none" fill="hsl(0, 84%, 60%)" d="M0,4h32v24h-32z"
                                                        class="recharts-legend-icon"></path>
                                                </svg><span class="recharts-legend-item-text"
                                                    style="color: rgb(239, 67, 67);"><span
                                                        class="text-sm text-foreground capitalize">incumplido</span></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div tabindex="-1"
                                        class="recharts-tooltip-wrapper recharts-tooltip-wrapper-right recharts-tooltip-wrapper-bottom"
                                        style="visibility: hidden; pointer-events: none; position: absolute; top: 0px; left: 0px; transform: translate(233.786px, 31px);">
                                        <div class="recharts-default-tooltip"
                                            style="margin: 0px; padding: 10px; background-color: hsl(var(--card)); border: 1px solid hsl(var(--border)); white-space: nowrap; border-radius: 8px;">
                                            <p class="recharts-tooltip-label" style="margin: 0px;">Mar</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="bg-card rounded-xl border shadow-card p-6 animate-fade-in">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg">Tickets Recientes</h3><a href="/tickets"
                                    class="text-sm text-primary hover:underline">Ver todos</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-border">
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                ID</th>
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                Título</th>
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                Prioridad</th>
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                Estado</th>
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                Asignado</th>
                                            <th
                                                class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider py-3 px-2">
                                                Creado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-3 px-2"><span
                                                    class="font-mono text-sm text-primary">TK-1234</span></td>
                                            <td class="py-3 px-2"><span
                                                    class="text-sm font-medium truncate max-w-[200px] block">Error
                                                    crítico en servidor de producción</span></td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-critical/10 text-critical border-critical/20">
                                                    Crítico</div>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-primary/10 text-primary">
                                                    En progreso</div>
                                            </td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">María
                                                    López</span></td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Hace 15
                                                    min</span></td>
                                        </tr>
                                        <tr class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-3 px-2"><span
                                                    class="font-mono text-sm text-primary">TK-1233</span></td>
                                            <td class="py-3 px-2"><span
                                                    class="text-sm font-medium truncate max-w-[200px] block">Solicitud
                                                    de acceso a VPN</span></td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-warning/10 text-warning border-warning/20">
                                                    Medio</div>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-info/10 text-info">
                                                    Abierto</div>
                                            </td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Sin
                                                    asignar</span></td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Hace 1
                                                    hora</span></td>
                                        </tr>
                                        <tr class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-3 px-2"><span
                                                    class="font-mono text-sm text-primary">TK-1232</span></td>
                                            <td class="py-3 px-2"><span
                                                    class="text-sm font-medium truncate max-w-[200px] block">Falla en
                                                    impresora del piso 3</span></td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-success/10 text-success border-success/20">
                                                    Bajo</div>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-warning/10 text-warning">
                                                    Pendiente</div>
                                            </td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Juan
                                                    Pérez</span></td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Hace 2
                                                    horas</span></td>
                                        </tr>
                                        <tr class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-3 px-2"><span
                                                    class="font-mono text-sm text-primary">TK-1231</span></td>
                                            <td class="py-3 px-2"><span
                                                    class="text-sm font-medium truncate max-w-[200px] block">Actualización
                                                    de licencia Office</span></td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-orange-500/10 text-orange-600 border-orange-500/20">
                                                    Alto</div>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-info/10 text-info">
                                                    Abierto</div>
                                            </td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Carlos
                                                    Ruiz</span></td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Hace 3
                                                    horas</span></td>
                                        </tr>
                                        <tr class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-3 px-2"><span
                                                    class="font-mono text-sm text-primary">TK-1230</span></td>
                                            <td class="py-3 px-2"><span
                                                    class="text-sm font-medium truncate max-w-[200px] block">Configuración
                                                    de nuevo equipo</span></td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-xs bg-warning/10 text-warning border-warning/20">
                                                    Medio</div>
                                            </td>
                                            <td class="py-3 px-2">
                                                <div
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-success/10 text-success">
                                                    Resuelto</div>
                                            </td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Ana
                                                    Martínez</span></td>
                                            <td class="py-3 px-2"><span class="text-sm text-muted-foreground">Hace 4
                                                    horas</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-card rounded-xl border shadow-card p-6 animate-fade-in">
                            <h3 class="font-semibold text-lg mb-4">Alertas Activas</h3>
                            <div class="space-y-3">
                                <div
                                    class="p-4 rounded-lg border-l-4 transition-colors hover:bg-muted/50 border-l-warning bg-warning/5">
                                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-triangle-alert w-5 h-5 mt-0.5 flex-shrink-0 text-warning">
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3">
                                            </path>
                                            <path d="M12 9v4"></path>
                                            <path d="M12 17h.01"></path>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm">Contrato Microsoft por vencer</p>
                                            <p class="text-xs text-muted-foreground mt-1">El contrato #CT-2024-001 vence
                                                en 15 días</p>
                                            <p class="text-xs text-muted-foreground mt-2">Hace 2 horas</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="p-4 rounded-lg border-l-4 transition-colors hover:bg-muted/50 border-l-warning bg-warning/5">
                                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-triangle-alert w-5 h-5 mt-0.5 flex-shrink-0 text-warning">
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3">
                                            </path>
                                            <path d="M12 9v4"></path>
                                            <path d="M12 17h.01"></path>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm">3 equipos con garantía próxima a expirar</p>
                                            <p class="text-xs text-muted-foreground mt-1">Laptops Dell XPS del área de
                                                Finanzas</p>
                                            <p class="text-xs text-muted-foreground mt-2">Hace 5 horas</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="p-4 rounded-lg border-l-4 transition-colors hover:bg-muted/50 border-l-critical bg-critical/5">
                                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-shield w-5 h-5 mt-0.5 flex-shrink-0 text-critical">
                                            <path
                                                d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                            </path>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm">Servidor DB-01 con alto uso de CPU</p>
                                            <p class="text-xs text-muted-foreground mt-1">Uso sostenido por encima del
                                                90%</p>
                                            <p class="text-xs text-muted-foreground mt-2">Hace 30 min</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="p-4 rounded-lg border-l-4 transition-colors hover:bg-muted/50 border-l-info bg-info/5">
                                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock w-5 h-5 mt-0.5 flex-shrink-0 text-info">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm">Backup completado exitosamente</p>
                                            <p class="text-xs text-muted-foreground mt-1">Respaldo nocturno finalizado
                                                sin errores</p>
                                            <p class="text-xs text-muted-foreground mt-2">Hace 8 horas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
