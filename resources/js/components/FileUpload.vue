<template>
  <div class="w-full text-sm space-y-4">
    <div v-if="props.currentUrl" class="rounded-xl border bg-card shadow-sm overflow-hidden">
      <div class="flex flex-wrap gap-4 items-center justify-between px-4 py-3 border-b">
        <div class="flex items-center gap-2">
          <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <FileCheck class="size-4 text-primary" />
          </div>
          <div>
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Archivo actual</p>
            <p class="text-foreground font-medium line-clamp-1 block">{{ fileNameFromUrl }}</p>
          </div>
        </div>
        <div class="flex gap-2 ml-auto">
          <Button variant="outline" size="sm" @click="downloadCurrent" :disabled="!props.currentUrl">
            Descargar
          </Button>
          <Button variant="ghost" size="sm" @click="openFilePicker">Reemplazar</Button>
        </div>
      </div>

      <div v-if="isImageUrl" class="relative">
        <img :src="props.currentUrl" alt="Archivo actual" class="w-full max-h-64 object-cover" />

        <Dialog>
          <DialogTrigger asChild>
            <button
              class="absolute top-3 right-3 bg-white/85 dark:bg-black/70 hover:bg-white rounded-full p-2 shadow-md border">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-maximize size-4 text-muted-foreground">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3m8-16h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3" />
              </svg>
            </button>
          </DialogTrigger>
          <DialogContent class="max-w-5xl max-h-[80vh] px-0 pb-0 overflow-hidden">
            <img :src="props.currentUrl" alt="Archivo actual" class="w-full h-auto max-h-[80vh]" />
          </DialogContent>
        </Dialog>
      </div>

      <div v-else class="px-4 py-5 bg-muted/50 flex items-center gap-3">
        <FileCheck class="size-4 text-muted-foreground" />
        <a :href="props.currentUrl" target="_blank" class="text-primary underline">Ver archivo</a>
      </div>
    </div>


    <div v-if="currentFile" class="rounded-md border bg-muted/40 p-3 flex items-start gap-3">
      <Upload class="size-4 text-muted-foreground mt-0.5" />
      <div class="space-y-1">
        <p class="font-medium text-foreground">Archivo seleccionado</p>
        <p class="text-muted-foreground text-xs">{{ currentFile.name }} ({{ formatSize(currentFile.size) }})</p>
      </div>
    </div>

    <div
      class="border-2 border-dashed rounded-xl p-5 flex flex-col items-center justify-center text-center cursor-pointer transition relative overflow-hidden"
      :class="[
        isDragging
          ? 'border-primary bg-primary/5 shadow-[0_10px_40px_-20px_rgba(0,0,0,0.3)]'
          : 'border-border hover:bg-muted/40'
      ]" @click="openFilePicker" @dragover="onDragOver" @dragleave="onDragLeave" @drop="onDrop">
      <div class="absolute inset-0 pointer-events-none bg-linear-to-br from-primary/5 via-transparent to-primary/10">
      </div>
      <div class="relative flex flex-col items-center gap-3">
        <div class="bg-muted rounded-full p-3 shadow-inner">
          <Upload class="h-5 w-5 text-muted-foreground" />
        </div>

        <div class="space-y-1">
          <p class="font-semibold text-foreground">{{ label }}</p>
          <p class="text-muted-foreground text-xs">{{ acceptLabel }} · {{ maxSizeMb }}MB máx.</p>
        </div>

        <Button variant="outline" size="sm" class="mt-1">Seleccionar archivo</Button>
      </div>
    </div>

    <input ref="fileInput" type="file" class="hidden" :accept="accept" @change="onFileChange" />
  </div>
</template>

<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTrigger
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { FileCheck, Upload } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'


interface Props {
  maxSizeMb?: number
  accept?: string
  label?: string
  currentUrl?: string
}

const reset = defineModel('reset', {
  type: Boolean,
  required: false,
  // default: false
})

const props = withDefaults(defineProps<Props>(), {
  maxSizeMb: 4,
  accept: 'image/*',
  label: 'Arrastra o selecciona un archivo',
  currentUrl: ''
})

const emit = defineEmits<{
  (e: 'update:file', file: File): void
  (e: 'error', message: string): void
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)

const currentFile = ref<File | null>(null)

const isImageUrl = computed(() => {
  if (!props.currentUrl) return false
  return props.currentUrl.match(/\.(jpeg|jpg|gif|png|svg)$/) != null
})

const fileNameFromUrl = computed(() => {
  if (!props.currentUrl) return 'Archivo'
  try {
    const urlObj = new URL(props.currentUrl)
    return decodeURIComponent(urlObj.pathname.split('/').pop() || 'Archivo')
  } catch (e) {
    return props.currentUrl.split('/').pop() || 'Archivo'
  }
})

const acceptLabel = computed(() => {
  if (!props.accept) return 'Imágenes o PDF'
  if (props.accept.includes('pdf')) return 'Imágenes o PDF'
  return 'Imágenes'
})

watch(() => reset.value, (resetVal) => {
  if (resetVal) {
    currentFile.value = null
    reset.value = false
  }
}, { immediate: true }
)

function openFilePicker() {
  fileInput.value?.click()
}

function downloadCurrent() {
  if (props.currentUrl) window.open(props.currentUrl, '_blank')
}

function formatSize(bytes: number) {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

function validateAndEmit(file: File) {
  const maxBytes = props.maxSizeMb * 1024 * 1024

  if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
    emit('error', 'Tipo de archivo no válido. Solo se permiten imágenes y PDFs.')
    return
  }

  if (file.size > maxBytes) {
    emit('error', `El archivo no puede superar ${props.maxSizeMb}MB`)
    return
  }

  currentFile.value = file

  emit('update:file', file)
}

function onFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) validateAndEmit(file)


  target.value = ''
}

/* Drag & Drop */
function onDragOver(event: DragEvent) {
  event.preventDefault()
  isDragging.value = true
}

function onDragLeave() {
  isDragging.value = false
}

function onDrop(event: DragEvent) {
  event.preventDefault()
  isDragging.value = false

  const file = event.dataTransfer?.files?.[0]
  if (file) validateAndEmit(file)


}
</script>