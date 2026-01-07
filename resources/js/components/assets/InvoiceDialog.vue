<template>

    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) {
            asset = null;
            open = false
        }
    }">

        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <h2 class="text-lg font-semibold">Cargar Factura</h2>
            </DialogHeader>

            <FileUpload :current-url="invoiceUrl" v-model:reset="resetUpload" @error="(msg) => toast.error(msg)"
                accept="application/pdf,image/*" @update:file="handleUploadInvoiceDocument($event)" />

        </DialogContent>

    </Dialog>


</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
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
            onSuccess: (page) => {
                invoiceUrl.value = page.props.flash.file_url as string;

                
            },
            onFinish: () => {
                resetUpload.value = true;
            },
        }
    );
}
</script>