<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false
        }
    }">
        <DialogContent class="sm:max-w-xl  space-y-4">
            <DialogHeader class="space-y-2 pb-4 border-b">
                <div class="flex items-center gap-3">
                    <div
                        class="size-12 rounded-xl bg-primary/10 flex items-center justify-center ring-2 ring-primary/15">
                        <FileText class="size-6 text-primary" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold leading-tight">Cargar Factura</h2>
                        <p class="text-sm text-muted-foreground">Adjunta el comprobante del activo para mantener el
                            expediente completo.</p>
                    </div>
                </div>
                <p v-if="asset"
                    class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded-md inline-flex gap-1 items-center w-fit">
                    <span class="font-mono">AST-{{ asset?.id }}</span>
                    <span class="text-foreground">·</span>
                    <span class="font-medium">{{ asset?.name }}</span>
                </p>
            </DialogHeader>

            <div class="space-y-3 rounded-xl border bg-muted/30 p-4 max-h-96 overflow-y-auto">
                <div class="flex items-start gap-2 text-sm text-muted-foreground">
                    <Info class="size-4 mt-0.5" />
                    <div>
                        <p class="font-medium text-foreground">Requisitos del archivo</p>
                        <p>Formato PDF o imagen, máximo 2MB. Asegúrate de que el documento sea legible.</p>
                    </div>
                </div>

                <FileUpload :max-size-mb="2" :current-url="invoiceUrl" v-model:reset="resetUpload"
                    @error="(msg) => toast.error(msg)" accept="application/pdf,image/*"
                    @update:file="handleUploadInvoiceDocument($event)" />
            </div>

        </DialogContent>

    </Dialog>


</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
import { FileText, Info } from 'lucide-vue-next';
import type { Asset } from '@/interfaces/asset.interface';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import FileUpload from '../FileUpload.vue';


const open = defineModel<boolean>('open')

const asset = defineModel<Asset | null>('asset');


const resetUpload = ref(false);



const invoiceUrl = computed({
    get: () => asset?.value?.invoice_url || '',
    set: (value: string) => {
        if (asset.value) {
            asset.value.invoice_url = value;
        }
    }
})

const handleUploadInvoiceDocument = (file: File) => {
    router.post(
        `/assets/invoice-documents/${asset?.value?.id}`,
        {
            invoice: file,
        },
        {
            onFlash: (flash) => {
                const fileUrl = flash.file_url as string;
                if (fileUrl) {
                    invoiceUrl.value = fileUrl;
                }
            },
            onFinish: () => {
                resetUpload.value = true;
            },
        }
    );
}
</script>