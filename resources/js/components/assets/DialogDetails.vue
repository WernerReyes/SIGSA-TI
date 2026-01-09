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


            <Tabs default-value="history" class="mt-3">
                <TabsList>
                    <TabsTrigger value="info">
                        Información
                    </TabsTrigger>
                    <TabsTrigger value="history">
                        Asignaciones
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
                                    <p v-if="asset?.current_assignment" class="text-sm font-medium">{{
                                        asset?.current_assignment?.assigned_to?.full_name }}</p>
                                    <Badge v-else variant="secondary">Sin asignar</Badge>
                                </div>
                            </div>
                            <div class="flex items-center gap-3" v-if="asset?.current_assignment">

                                <Monitor class="size-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Departamento</p>
                                    <p class="text-sm">{{ asset?.current_assignment?.assigned_to?.department?.name }}
                                    </p>
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

                        <Empty v-if="assignments.length === 0">
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <MonitorSmartphone />
                                </EmptyMedia>
                                <EmptyTitle>Sin asignaciones</EmptyTitle>
                                <EmptyDescription>
                                    No hay asignaciones para este equipo.
                                </EmptyDescription>
                            </EmptyHeader>


                        </Empty>
                        <div v-else v-for="assignment in assignments" :key="assignment.id"
                            class="flex items-center justify-between gap-4 p-3 rounded-lg" :class="{
                                'bg-green-500/20': !assignment.returned_at,
                                'bg-muted/30': assignment.returned_at
                            }">
                            <div class="flex items-center gap-4">

                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">
                                        Asignado a {{ assignment?.assigned_to?.full_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ format(parseISO(assignment.assigned_at.split('T')[0]), 'yyyy-MM-dd') }}
                                        -
                                        <span v-if="assignment.returned_at">
                                            {{ format(parseISO(assignment.returned_at.split('T')[0]), 'yyyy-MM-dd') }}
                                        </span>
                                        <Badge v-else class="my-2 bg-green-100 text-green-800">Actualmente asignado
                                        </Badge>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-end gap-2">


                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="outline" :disabled="!assignment">
                                            <FileText class="size-4 mr-2" />
                                            Entrega
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="w-12" align="start">

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


                                        <DropdownMenuSub>
                                            <DropdownMenuSubTrigger>
                                                <div class="flex items-center gap-2">
                                                    <Download class="size-4" />

                                                    Activo
                                                </div>
                                            </DropdownMenuSubTrigger>
                                            <DropdownMenuPortal>
                                                <DropdownMenuSubContent>
                                                    <DropdownMenuItem @click="() => handleDownloadCargo(assignment.id)">
                                                        <Laptop />

                                                        {{ capitalize(asset?.type?.name || 'Laptop') }}
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        @click="() => handleDownloadPhoneCargo(assignment.id)">
                                                        <Smartphone />
                                                        Celular
                                                    </DropdownMenuItem>

                                                </DropdownMenuSubContent>
                                            </DropdownMenuPortal>
                                        </DropdownMenuSub>

                                    </DropdownMenuContent>
                                </DropdownMenu>


                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="outline" :disabled="!assignment.returned_at">
                                            <FileText class="size-4 mr-2" />
                                            Devolución
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="w-12" align="start">
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


                                        <DropdownMenuItem @click="() => handleDownloadReturnCargo(assignment.id)">
                                            <Download />
                                            Activo
                                            <MonitorSmartphone />
                                        </DropdownMenuItem>

                                    </DropdownMenuContent>
                                </DropdownMenu>


                            </div>


                        </div>


                    </div>



                </TabsContent>

            </Tabs>



        </DialogContent>
    </Dialog>

    <Dialog v-model:open="openUploadDialog">

        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <h2 class="text-lg font-semibold">Cargar Documento</h2>
            </DialogHeader>

            <FileUpload :current-url="url" v-model:reset="resetUpload" @error="(msg) => toast.error(msg)"
                accept="application/pdf,image/*" :asset-id="asset?.id" @update:file="handleUploadSignedDocument($event)"
                @close="openUploadDialog = false" />

            <!-- Upload form content goes here -->

        </DialogContent>

    </Dialog>

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
    DropdownMenuPortal,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle
} from '@/components/ui/empty';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger
} from '@/components/ui/tabs';
import { Asset, statusOp } from '@/interfaces/asset.interface';
import { AssetAssignment } from '@/interfaces/assetAssignment.interface';
import { DeliveryRecordType } from '@/interfaces/deliveryRecord.interface';
import { router } from '@inertiajs/vue3';
import { format, isAfter, parseISO } from 'date-fns';
import { Calendar, Download, Eye, FileText, Laptop, Monitor, MonitorSmartphone, Shield, Smartphone, Upload, User } from 'lucide-vue-next';
import { capitalize, computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import FileUpload from '../FileUpload.vue';


const asset = defineModel<Asset | null>('asset');


const open = defineModel<boolean>('open');

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


const isWarrantyValid = (warrantyEndDate: string): boolean => {
    // const warrantyEndDate = asset.warranty_expiration;

    const endDate = parseISO(warrantyEndDate.split('T')[0])
    const today = new Date()
    const todayDateOnly = parseISO(today.toISOString().split('T')[0])

    return isAfter(endDate, todayDateOnly)
}

const handleDownloadCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-laptop-assignment-doc/${assignmentId}`;
}

const handleDownloadPhoneCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-phone-assignment-doc/${assignmentId}`;
}


const handleDownloadReturnCargo = (assignmentId: number) => {
    if (!asset.value) return;
    window.location.href = `/assets/generate-return-doc/${assignmentId}`;
}


const viewDocument = (url?: string) => {
    window.open(url || '', '_blank');
}

const handleUploadSignedDocument = (file: File) => {
    router.post(`/assets/delivery-records/${assignmentId.value}`, {
        file: file,
        type: type.value,
    }, {
        onSuccess: (page) => {
            const fileUrl = page.props.flash.file_url;

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
            // resetUpload.value = true;
        },
        onFinish: () => {
            resetUpload.value = true;
        }

    });
    // Implement upload logic here
};


</script>
