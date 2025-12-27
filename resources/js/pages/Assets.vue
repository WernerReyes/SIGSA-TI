<script setup lang="ts">
import Dialog from '@/components/assets/Dialog.vue';
import { type AssetType } from '@/interfaces/asset.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';


defineProps<{ types: AssetType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos TI',
        href: "#"
    },

];

</script>

<template>

    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        
            <Dialog :assetTypes="types" />


            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Total Activos</p>
                    <p class="text-2xl font-bold mt-1">6</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Activos</p>
                    <p class="text-2xl font-bold text-success mt-1">4</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">En Mantenimiento</p>
                    <p class="text-2xl font-bold text-warning mt-1">1</p>
                </div>
                <div class="bg-card rounded-xl border shadow-card p-4">
                    <p class="text-sm text-muted-foreground">Garantías Vencidas</p>
                    <p class="text-2xl font-bold text-critical mt-1">1</p>
                </div>
            </div>
            <div class="bg-card rounded-xl border shadow-card p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg><input
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10"
                            placeholder="Buscar por nombre, ID o serial..." value=""></div>
                    <div class="flex gap-2"><button type="button" role="combobox" aria-controls="radix-:r8v:"
                            aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed"
                            class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-[160px]"><span
                                style="pointer-events: none;">Todos los estados</span><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50"
                                aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg></button><button type="button" role="combobox" aria-controls="radix-:r90:"
                            aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed"
                            class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-[160px]"><span
                                style="pointer-events: none;">Todos los tipos</span><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50"
                                aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg></button><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-filter w-4 h-4">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg></button></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-laptop w-6 h-6 text-primary">
                                    <path
                                        d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16">
                                    </path>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-001</p>
                                <h3 class="font-semibold text-sm mt-0.5">Laptop Desarrollo</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r91:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">Dell XPS
                                15 9520</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">DXPS15-2024-001</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="font-medium">Juan Pérez</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Desarrollo</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-success/10 text-success">
                            Activo</div><span class="text-xs font-medium text-success">Garantía: Vigente</span>
                    </div>
                </div>
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-monitor w-6 h-6 text-primary">
                                    <rect width="20" height="14" x="2" y="3" rx="2"></rect>
                                    <line x1="8" x2="16" y1="21" y2="21"></line>
                                    <line x1="12" x2="12" y1="17" y2="21"></line>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-002</p>
                                <h3 class="font-semibold text-sm mt-0.5">Desktop Contabilidad</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r93:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">HP
                                ProDesk 400 G7</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">HP400G7-2023-042</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="font-medium">María López</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Finanzas</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-success/10 text-success">
                            Activo</div><span class="text-xs font-medium text-warning">Garantía: Por vencer</span>
                    </div>
                </div>
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-server w-6 h-6 text-primary">
                                    <rect width="20" height="8" x="2" y="2" rx="2" ry="2"></rect>
                                    <rect width="20" height="8" x="2" y="14" rx="2" ry="2"></rect>
                                    <line x1="6" x2="6.01" y1="6" y2="6"></line>
                                    <line x1="6" x2="6.01" y1="18" y2="18"></line>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-003</p>
                                <h3 class="font-semibold text-sm mt-0.5">Servidor Principal</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r95:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">Dell
                                PowerEdge R740</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">PE740-2021-001</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="text-muted-foreground">Sin asignar</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Infraestructura</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-success/10 text-success">
                            Activo</div><span class="text-xs font-medium text-critical">Garantía: Vencida</span>
                    </div>
                </div>
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-smartphone w-6 h-6 text-primary">
                                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect>
                                    <path d="M12 18h.01"></path>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-004</p>
                                <h3 class="font-semibold text-sm mt-0.5">iPhone Gerencia</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r97:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">Apple
                                iPhone 14 Pro</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">IP14P-2023-012</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="font-medium">Carlos García</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Gerencia</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-success/10 text-success">
                            Activo</div><span class="text-xs font-medium text-warning">Garantía: Por vencer</span>
                    </div>
                </div>
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-printer w-6 h-6 text-primary">
                                    <path
                                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"></path>
                                    <rect x="6" y="14" width="12" height="8" rx="1"></rect>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-005</p>
                                <h3 class="font-semibold text-sm mt-0.5">Impresora Piso 3</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r99:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">HP
                                LaserJet Pro M404dn</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">HPLJ404-2022-005</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="text-muted-foreground">Sin asignar</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Compartido</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-warning/10 text-warning">
                            Mantenimiento</div><span class="text-xs font-medium text-success">Garantía:
                            Vigente</span>
                    </div>
                </div>
                <div
                    class="bg-card rounded-xl border shadow-card p-5 hover:shadow-card-hover transition-all duration-200 animate-fade-in cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-laptop w-6 h-6 text-primary">
                                    <path
                                        d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16">
                                    </path>
                                </svg></div>
                            <div>
                                <p class="font-mono text-xs text-muted-foreground">AST-006</p>
                                <h3 class="font-semibold text-sm mt-0.5">Laptop Ventas</h3>
                            </div>
                        </div><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8"
                            type="button" id="radix-:r9b:" aria-haspopup="menu" aria-expanded="false"
                            data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis w-4 h-4">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">Marca/Modelo</span><span
                                class="font-medium">Lenovo
                                ThinkPad X1 Carbon</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Serial</span><span
                                class="font-mono text-xs">TPX1C-2023-008</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">Asignado a</span><span
                                class="text-muted-foreground">Sin asignar</span></div>
                        <div class="flex justify-between"><span
                                class="text-muted-foreground">Departamento</span><span>Stock</span></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex items-center justify-between">
                        <div
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 text-xs border-0 bg-info/10 text-info">
                            En stock</div><span class="text-xs font-medium text-success">Garantía: Vigente</span>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>