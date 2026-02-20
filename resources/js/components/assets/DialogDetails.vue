<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false;

        }
    }">
        <DialogContent class="sm:max-w-4xl">
            <DialogHeader class="space-y-3 pb-4">
                <div
                    class="flex items-start gap-4 p-4 rounded-xl bg-linear-to-br from-muted/40 via-background to-background border">
                    <div
                        class="size-16 rounded-xl bg-linear-to-br from-primary/20 to-primary/5 flex items-center justify-center ring-2 ring-primary/20 shadow-sm">
                        <Laptop class="size-8 text-primary" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-mono text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded-md">AST-{{
                                asset?.id }}</p>
                            <Badge variant="outline" class="text-xs">
                                <template v-if="asset?.is_new">
                                    <Sparkles />
                                    Nuevo
                                </template>
                                <template v-else>
                                    <History />
                                    Usado
                                </template>

                            </Badge>
                        </div>
                        <h2 class="font-bold tracking-tight text-2xl">{{ asset?.name }}</h2>
                        <p class="text-sm text-muted-foreground mt-1">{{ asset?.brand }} {{ asset?.model }}</p>
                    </div>
                </div>
            </DialogHeader>


            <ScrollArea class="dialog-content">
                <Tabs default-value="history" class="mt-6">
                    <TabsList class="grid w-full grid-cols-3 h-auto p-1">
                        <TabsTrigger value="info" class="gap-2 py-2.5">
                            <FileText class="size-4" />
                            Información
                        </TabsTrigger>
                        <TabsTrigger value="history" class="gap-2 py-2.5">
                            <MonitorSmartphone class="size-4" />
                            Asignaciones
                        </TabsTrigger>
                        <TabsTrigger value="repairs" class="gap-2 py-2.5">
                            <Wrench class="size-4" />
                            Reparaciones
                        </TabsTrigger>
                    </TabsList>
                    <TabsContent value="repairs" class="mt-6">
                        <div class="space-y-4">
                            <Empty v-if="!asset?.reparations || asset.reparations.length === 0" class="py-8">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Wrench />
                                    </EmptyMedia>
                                    <EmptyTitle>Sin reparaciones</EmptyTitle>
                                    <EmptyDescription>
                                        No hay reparaciones registradas para este equipo.
                                    </EmptyDescription>
                                </EmptyHeader>
                            </Empty>
                            <div v-else>
                                <div v-for="repair in asset.reparations" :key="repair.id" class="rounded-lg border bg-yellow-50 dark:bg-yellow-950/10 border-yellow-200 dark:border-yellow-800 mb-2 px-4 py-2 flex md:flex-row flex-col items-center md:items-stretch gap-4 w-full">
                                    <div class="flex flex-col items-center justify-center md:mr-4 mr-0 md:w-24 w-full">
                                        <Wrench class="size-6 text-yellow-600 dark:text-yellow-400 mb-1" />
                                        <span class="text-xs font-mono text-muted-foreground">{{ format(parseISO(repair.date), 'dd-MM-yyyy') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0 w-full flex">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-0.5">Descripción</p>
                                            <p class="text-sm font-semibold truncate">{{ repair.description }}</p>
                                        </div>
                                        <div v-if="repair.image_paths && repair.image_paths.length > 0" class="flex md:flex-row flex-col gap-2 mt-2">
                                            <img v-for="img in repair.image_paths" :key="img" :src="getImageUrl(img)" class="w-20 h-20 object-cover rounded border cursor-pointer hover:ring-2 hover:ring-yellow-400 transition" :alt="'Imagen reparación ' + repair.id"
                                              @click="() => {
                                                console.log(repair.image_paths.map(getImageUrl));
                                                  images = repair.image_paths.map(getImageUrl);
                                                //   const imageUrl = getImageUrl(img);
                                                //   if (imageUrl) {
                                                //       viewDocument(imageUrl);
                                                //   } else {
                                                //       toast.error('No se pudo cargar la imagen.');
                                                //   }
                                              }"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent value="info" class="mt-6 overflow-hidden">
                        <div class="grid md:grid-cols-2 gap-4">

                            <div
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                    <FileText class="size-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Serial
                                    </p>
                                    <p class="text-sm font-mono font-semibold mt-1">{{ asset?.serial_number || 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                                    <User class="size-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Asignado a</p>
                                    <p v-if="asset?.current_assignment" class="text-sm font-semibold mt-1">{{
                                        asset?.current_assignment?.assigned_to?.full_name }}</p>
                                    <Badge v-else variant="secondary" class="mt-1">Sin asignar</Badge>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition"
                                v-if="asset?.current_assignment">
                                <div
                                    class="size-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                    <Monitor class="size-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Departamento</p>
                                    <p class="text-sm font-semibold mt-1">{{
                                        asset?.current_assignment?.assigned_to?.department?.name }}
                                    </p>
                                </div>
                            </div>

                            <!-- <div class="space-y-3"> -->

                            <div v-if="asset?.purchase_date"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                    <Calendar class="size-5 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Fecha
                                        de Compra</p>
                                    <p v-if="asset?.purchase_date" class="text-sm font-semibold mt-1">{{ format(
                                        parseISO(asset?.purchase_date.split('T')[0])
                                        , 'dd-MM-yyyy') }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.warranty_expiration"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div class="size-10 rounded-lg flex items-center justify-center shrink-0"
                                    :class="isWarrantyValid(asset?.warranty_expiration || '') ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-rose-100 dark:bg-rose-900/30'">
                                    <Shield
                                        :class="isWarrantyValid(asset?.warranty_expiration || '') ? 'size-5 text-emerald-600 dark:text-emerald-400' : 'size-5 text-rose-600 dark:text-rose-400'" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Garantía</p>
                                    <Badge v-if="asset?.warranty_expiration" class="mt-1"
                                        :class="isWarrantyValid(asset.warranty_expiration) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300'">
                                        {{
                                            isWarrantyValid(asset.warranty_expiration)
                                                ? `✓ Válida hasta ${format(
                                                    parseISO(asset?.warranty_expiration.split('T')[0])
                                                    , 'dd-MM-yyyy')}`
                                                : `✗ Expirada el ${format(
                                                    parseISO(asset?.warranty_expiration.split('T')[0])
                                                    , 'dd-MM-yyyy')}`
                                        }}
                                    </Badge>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                    <Shield class="size-5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Estado
                                    </p>
                                    <Badge v-if="asset?.status" class="mt-1" :class="statusOp(asset.status)?.bg">{{
                                        statusOp(asset.status)?.label }}</Badge>
                                </div>
                            </div>

                            <div v-if="asset?.type?.name"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                                    <component :is="assetTypeOp(asset.type.name)?.icon"
                                        class="size-5 text-cyan-600 dark:text-cyan-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Tipo
                                    </p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.type.name }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.color"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                                    <Palette class="size-5 text-pink-600 dark:text-pink-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Color
                                    </p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.color }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.processor"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                                    <Cpu class="size-5 text-violet-600 dark:text-violet-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Procesador</p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.processor }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.ram"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center shrink-0">
                                    <MemoryStick class="size-5 text-sky-600 dark:text-sky-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">RAM
                                    </p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.ram }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.storage"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                                    <HardDrive class="size-5 text-teal-600 dark:text-teal-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Almacenamiento</p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.storage }}</p>
                                </div>
                            </div>

                            <div v-if="isCellPhone && asset?.phone"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-lime-100 dark:bg-lime-900/30 flex items-center justify-center shrink-0">
                                    <Phone class="size-5 text-lime-600 dark:text-lime-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Teléfono</p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.phone }}</p>
                                </div>
                            </div>

                            <div v-if="isCellPhone && asset?.imei"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                                    <Smartphone class="size-5 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">IMEI
                                    </p>
                                    <p class="text-sm font-semibold mt-1">{{ asset.imei }}</p>
                                </div>
                            </div>

                            <div v-if="asset?.invoice_url"
                                class="flex items-start gap-3 p-4 rounded-lg border bg-card hover:bg-muted/50 transition">
                                <div
                                    class="size-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                    <Receipt class="size-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                        Factura</p>
                                    <Button variant="link" class="px-0 h-auto text-sm font-semibold mt-1"
                                        @click="viewDocument(asset.invoice_url)">
                                        Ver documento
                                    </Button>
                                </div>
                            </div>

                            <!-- </div> -->
                        </div>
                    </TabsContent>

                    <TabsContent value="history" class="mt-6">

                        <div class="space-y-4">

                            <Empty v-if="assignments.length === 0" class="py-8">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <MonitorSmartphone />
                                    </EmptyMedia>
                                    <EmptyTitle>Sin asignaciones</EmptyTitle>
                                    <EmptyDescription>
                                        No hay asignaciones registradas para este equipo.
                                    </EmptyDescription>
                                </EmptyHeader>


                            </Empty>
                            <div v-else v-for="assignment in assignments" :key="assignment.id"
                                class="rounded-xl border transition hover:shadow-md p-4" :class="{
                                    'bg-linear-to-br from-emerald-50 to-green-50 dark:from-emerald-950/20 dark:to-green-950/20 border-emerald-200 dark:border-emerald-800': !assignment.returned_at,
                                    'bg-muted/30 border-muted': assignment.returned_at
                                }">
                                <div class="flex max-md:flex-col items-start justify-between gap-4 ">
                                    <div class="flex items-start gap-4 flex-1">

                                        <div
                                            :class="!assignment.returned_at ? 'w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-900/50 mt-1' : 'w-3 h-3 rounded-full bg-muted-foreground mt-1'">
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold flex items-center gap-2">
                                                <User class="size-4" />
                                                {{ assignment?.assigned_to?.full_name }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <p class="text-xs text-muted-foreground flex items-center gap-1">
                                                    <Calendar class="size-3" />
                                                    {{ format(assignment.assigned_at, 'dd/MM/yyyy') }}
                                                </p>
                                                <span v-if="assignment.returned_at"
                                                    class="text-xs text-muted-foreground">
                                                    → {{ format(assignment.returned_at, 'dd/MM/yyyy HH:mm') }}
                                                </span>
                                                <Badge v-else class="bg-emerald-600 text-white text-xs">✓ Actualmente
                                                    asignado
                                                </Badge>
                                            </div>


                                        </div>
                                    </div>


                                    <div class="flex max-md:justify-end max-md:w-full  items-start gap-2 shrink-0">


                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="outline" size="sm" :disabled="!assignment"
                                                    class="gap-2">
                                                    <FileText class="size-4" />
                                                    Entrega
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent class="w-44" align="end">

                                                <DropdownMenuItem :disabled="!assignment.delivery_document" @click="() => {
                                                    viewDocument(assignment.delivery_document?.file_url)
                                                }">
                                                    <Eye />
                                                    Ver
                                                </DropdownMenuItem>

                                                <DropdownMenuItem @click="() => {
                                                    openUploadDialog = true; type = DeliveryRecordType.ASSIGNMENT,
                                                        assignmentId = assignment.id, url = assignment.delivery_document?.file_url || ''
                                                }">
                                                    <Upload />
                                                    Cargar
                                                </DropdownMenuItem>

                                                <DropdownMenuItem @click="() => {
                                                    if (!asset?.type) return;
                                                    downloadAssignmentDocument(assignment.id, asset?.type);
                                                }">
                                                    <Download />
                                                    Activo

                                                    <component
                                                        :is="asset?.type?.name ? assetTypeOp(asset.type.name as TypeName)?.icon : MonitorSmartphone"
                                                        class="size-4" />
                                                </DropdownMenuItem>

                                            </DropdownMenuContent>
                                        </DropdownMenu>


                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="outline" size="sm" :disabled="!assignment.returned_at"
                                                    class="gap-2">
                                                    <FileText class="size-4" />
                                                    Devolución
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent class="w-44" align="end">
                                                <DropdownMenuItem :disabled="!assignment.return_document" @click="() => {
                                                    viewDocument(assignment.return_document?.file_url)
                                                }">
                                                    <Eye />
                                                    Ver
                                                </DropdownMenuItem>

                                                <DropdownMenuItem @click="() => {
                                                    openUploadDialog = true, type = DeliveryRecordType.DEVOLUTION,
                                                        assignmentId = assignment.id, url = assignment.return_document?.file_url || ''
                                                }">
                                                    <Upload />
                                                    Cargar
                                                </DropdownMenuItem>


                                                <DropdownMenuItem
                                                    @click="() => downloadReturnAssignmentDocument(assignment.id)">
                                                    <Download />
                                                    Activo
                                                    <MonitorSmartphone />
                                                </DropdownMenuItem>

                                            </DropdownMenuContent>
                                        </DropdownMenu>


                                    </div>
                                </div>


                                <Accordion v-if="assignment.children_assignments?.length" type="single" collapsible
                                    class="mt-3 w-full">
                                    <AccordionItem :value="`children-${assignment.id}`" class="border-0">
                                        <AccordionTrigger class="py-1.5 hover:no-underline">
                                            <div class="flex items-center gap-2 text-xs font-semibold">
                                                <component :is="assetTypeOp(TypeName.ACCESSORY)?.icon" class="size-3" />
                                                Accesorios ({{ assignment.children_assignments.length }})
                                            </div>
                                        </AccordionTrigger>
                                        <AccordionContent class="pt-2">
                                            <div
                                                class="mt-2 grid gap-2 border-l-2 border-dashed border-slate-200 dark:border-slate-900/50 pl-3">
                                                <div v-for="child in assignment.children_assignments" :key="child.id"
                                                    class="rounded-lg border bg-muted/30 px-2 py-2">
                                                    <p class="text-xs font-semibold truncate wrap-break-word">{{
                                                        child.asset?.full_name
                                                        || 'Accesorio' }}</p>

                                                </div>
                                            </div>
                                        </AccordionContent>
                                    </AccordionItem>
                                </Accordion>
                            </div>


                        </div>



                    </TabsContent>

                </Tabs>
            </ScrollArea>


                
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="openUploadDialog">

        <DialogContent class="sm:max-w-3xl max-h-screen overflow-y-auto space-y-4">
            <DialogHeader class="space-y-3 pb-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <Upload class="size-5 text-primary" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Cargar Documento</h2>
                        <p class="text-sm text-muted-foreground">Sube el archivo firmado</p>
                    </div>
                </div>
            </DialogHeader>

            <FileUpload :current-url="url" v-model:reset="resetUpload" @error="(msg) => toast.error(msg)"
                accept="application/pdf,image/*" :asset-id="asset?.id" @update:file="handleUploadSignedDocument($event)"
                @close="openUploadDialog = false" />

            <!-- Upload form content goes here -->

        </DialogContent>

    </Dialog>

    <Carousel  type="dialog" :items="images" @close="images = []" >
        <template #item="{ item }">
           <div class="w-full h-11/12! flex items-center justify-center">

               <img :src="item" class="m-auto max-h-200 object-contain" />
            </div>
        </template>
    </Carousel>
</template>

<script setup lang="ts">

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle
} from '@/components/ui/empty';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger
} from '@/components/ui/tabs';
import { useAsset } from '@/composables/useAsset';
import { type Asset, statusOp } from '@/interfaces/asset.interface';
import { AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { assetTypeOp } from '@/interfaces/assetType.interface';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { router } from '@inertiajs/vue3';
import { format, isAfter, isSameDay, parseISO, startOfDay } from 'date-fns';
import {
    Calendar, Download, Eye, FileText,
    History,
    Cpu, HardDrive, Laptop, MemoryStick, Monitor, MonitorSmartphone, Palette, Phone, Receipt, Shield, Smartphone,
    Sparkles,
    Upload, User
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import FileUpload from '../FileUpload.vue';
import { TypeName } from '@/interfaces/assetType.interface';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { Wrench } from 'lucide-vue-next';
import { getImageUrl } from '../../lib/utils';
import Carousel from '../Carousel.vue';


const asset = defineModel<Asset | null>('asset');
const open = defineModel<boolean>('open');
    const images = ref<string[]>([]);

const { downloadAssignmentDocument, downloadReturnAssignmentDocument } = useAsset();

const openUploadDialog = ref(false);
const type = ref<DeliveryRecordType>(DeliveryRecordType.ASSIGNMENT);
const assignmentId = ref<number | null>(null);
const resetUpload = ref(false);
const url = ref<string>('');


const assignments = computed({
    get: () => asset.value?.assignments || [],
    set: (val) => {
        if (asset.value) {
            asset.value.assignments = val;
        }
    }
});

const isCellPhone = computed(() => {
    return asset.value?.type?.name === TypeName.CELL_PHONE || asset.value?.type_id === 3;
});

const isWarrantyValid = (warrantyEndDate: string): boolean => {
    const endDate = startOfDay(new Date(warrantyEndDate));
    const today = startOfDay(new Date());
    return isAfter(endDate, today) || isSameDay(endDate, today);
};

const viewDocument = (url?: string) => {
    window.open(url || '', '_blank');
}

const handleUploadSignedDocument = (file: File) => {
    router.post(`/assets/delivery-records/${assignmentId.value}`, {
        file: file,
        type: type.value,
    }, {
        only: [],
        onFlash(flash) {
            const fileUrl = flash.file_url as string;
            url.value = fileUrl;

            assignments.value = assignments.value.map(assignment => {
                if (assignment.id === assignmentId.value) {
                    if (type.value === DeliveryRecordType.ASSIGNMENT) {
                        return {
                            ...assignment,
                            delivery_document: {
                                id: assignment.delivery_document?.id || 0,
                                ...assignment.delivery_document,
                                file_url: fileUrl
                            }
                        } as AssetAssignment;
                    } else if (type.value === DeliveryRecordType.DEVOLUTION) {
                        return {
                            ...assignment,
                            return_document: {
                                id: assignment.return_document?.id || 0,
                                ...assignment.return_document,
                                file_url: fileUrl
                            }
                        } as AssetAssignment;
                    }
                }
                return assignment;
            });
        },

        onFinish: () => {
            resetUpload.value = true;
        }

    });

};


</script>
