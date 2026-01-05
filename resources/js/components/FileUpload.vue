<template>
  <div class="w-full text-xs">

    <div v-if="props.currentUrl" class="my-4">
      <p class="font-medium text-foreground mb-2">Archivo actual:</p>
      <div v-if="isImageUrl" class="border rounded-md relative overflow-hidden">
        <img :src="props.currentUrl" alt="Current File" class="w-full h-auto" />
        <div class="absolute top-2 right-2">
          <Eye class="size-4 text-muted-foreground" />
        </div>
         

        <Dialog >
          <DialogTrigger asChild>
            <button class="absolute top-2 right-2 bg-white/80 hover:bg-white/100 rounded-full p-1 shadow-md">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize size-4 text-muted-foreground">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3m8-16h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3"/>
              </svg>
            </button>
          </DialogTrigger>
          <DialogContent class="max-w-5xl max-h-[80vh] p-0 overflow-hidden">
            <img :src="props.currentUrl" alt="Current File" class="w-full h-auto max-h-[80vh]" />
          </DialogContent>
        </Dialog>
        </div>
      <div v-else class="border rounded-md p-4 bg-muted flex items-center justify-center">
        <a :href="props.currentUrl" target="_blank" class="text-primary underline">
          Ver archivo
        </a>
        </div>
    </div>

    <div v-if="currentFile" class="my-4  border flex items-center rounded-md bg-muted">
      <Upload class="inline-block mr-2 size-4 text-muted-foreground" />
      <div class="inline-block">
        <p class="font-medium text-foreground">Archivo seleccionado:</p>
        <p class="text-muted-foreground">{{ currentFile.name }}</p>
      </div>
    </div>
    <div
      class="border-2 border-dashed rounded-md p-3 flex flex-col items-center justify-center text-center cursor-pointer transition"
      :class="[
        isDragging
          ? 'border-primary bg-primary/5'
          : 'border-border hover:bg-muted/40'
      ]" @click="openFilePicker" @dragover="onDragOver" @dragleave="onDragLeave" @drop="onDrop">
      <div class="mb-2 bg-muted rounded-full p-3">
        <Upload class="h-5 w-5 text-muted-foreground" />
      </div>

      <p class=" font-medium text-foreground">
        {{ label }}
      </p>

      <p class="text-muted-foreground mt-1">


        ({{ maxSizeMb }}MB max)
      </p>
    </div>

    <input ref="fileInput" type="file" class="hidden" :accept="accept" @change="onFileChange" />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Eye, Upload } from 'lucide-vue-next'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog'

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