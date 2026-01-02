<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-6/12 max-h-screen overflow-y-auto">
            <DialogHeader>
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-xl bg-primary/10 flex items-center justify-center">
                        <Laptop class="size-7" />
                    </div>
                    <div>
                        <p class="font-mono text-sm text-muted-foreground">AST-{{ asset?.id }}

                            <Badge>{{

                                asset?.is_new ? 'Nuevo' : 'Usado'
                            }}</Badge>

                        </p>
                        <h2 id="radix-:r1i:" class="font-semibold tracking-tight text-xl mt-1">{{ asset?.name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ asset?.brand }} {{ asset?.model }}</p>
                    </div>
                </div>
            </DialogHeader>


            <Tabs default-value="info" class="mt-3">
                <TabsList>
                    <TabsTrigger value="info">
                        Información
                    </TabsTrigger>
                    <TabsTrigger value="history">
                        Historial
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="info" class="mt-4">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">

                                <FileText class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Serial</p>
                                    <p class="text-sm font-mono">{{ asset?.serial_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">

                                <User class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Asignado a</p>
                                    <p v-if="asset?.assignment" class="text-sm font-medium">{{
                                        asset?.assignment?.assigned_to?.full_name }}</p>
                                    <Badge v-else variant="secondary">Sin asignar</Badge>
                                </div>
                            </div>
                            <div class="flex items-center gap-3" v-if="asset?.assignment">

                                <Monitor class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Departamento</p>
                                    <p class="text-sm">{{ asset?.assignment?.assigned_to?.department?.name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">

                                <Calendar class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Fecha de Compra</p>
                                    <p v-if="asset?.purchase_date" class="text-sm">{{ format(
                                        parseISO(asset?.purchase_date.split('T')[0])
                                        , 'dd-MM-yyyy') }}</p>
                                </div>
                            </div>


                            <div class="flex items-center gap-3">

                                <Shield class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Garantía hasta</p>

                                    <Badge v-if="asset?.warranty_expiration"
                                        :class="isWarrantyValid(asset.warranty_expiration) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{
                                            isWarrantyValid(asset.warranty_expiration)
                                                ? `Válida hasta ${format(
                                                    parseISO(asset?.warranty_expiration.split('T')[0])
                                                    , 'dd-MM-yyyy')}`
                                                : `Expirada el ${format(
                                                    parseISO(asset?.warranty_expiration.split('T')[0])
                                                    , 'dd-MM-yyyy')}`
                                        }}
                                    </Badge>

                                </div>
                            </div>

                            <div class="flex items-center gap-3">

                                <Shield class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Estado</p>
                                    <Badge v-if="asset?.status" :class="statusOp(asset.status)?.bg">{{
                                        statusOp(asset.status)?.label }}</Badge>
                                </div>
                            </div>

                        </div>
                    </div>
                </TabsContent>

                <TabsContent value="history" class="mt-4">
                    <div class="space-y-3">
                        <div class="flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Mantenimiento preventivo</p>
                                <p class="text-xs text-muted-foreground">2024-01-10</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Actualización de RAM</p>
                                <p class="text-xs text-muted-foreground">2023-11-15</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Asignado a Juan Pérez</p>
                                <p class="text-xs text-muted-foreground">2023-09-01</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Equipo registrado</p>
                                <p class="text-xs text-muted-foreground">2023-06-15</p>
                            </div>
                        </div>
                    </div>
                </TabsContent>

            </Tabs>





            <DialogFooter>


            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->
</template>

<script setup lang="ts">

import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger
} from '@/components/ui/tabs';
import { Asset, statusOp } from '@/interfaces/asset.interface';
import { format, isAfter, parseISO } from 'date-fns';
import { Calendar, FileText, Laptop, Monitor, Shield, User } from 'lucide-vue-next';



const asset = defineModel<Asset | null>('asset');

const open = defineModel<boolean>('open');


const isWarrantyValid = (warrantyEndDate: string): boolean => {
    // const warrantyEndDate = asset.warranty_expiration;

    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly)
}

</script>
