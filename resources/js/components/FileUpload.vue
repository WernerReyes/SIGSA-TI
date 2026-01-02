<template>
  <div class="w-full text-xs">
    <div v-if="currentFile" class="mb-4  border flex items-center rounded-md bg-muted">
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
import { ref } from 'vue'
import { Upload } from 'lucide-vue-next'

interface Props {
  maxSizeMb?: number
  accept?: string
  label?: string
}

const props = withDefaults(defineProps<Props>(), {
  maxSizeMb: 4,
  accept: 'image/*',
  label: 'Arrastra o selecciona un archivo'
})

const emit = defineEmits<{
  (e: 'update:file', file: File): void
  (e: 'error', message: string): void
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)

const currentFile = ref<File | null>(null)

function openFilePicker() {
  fileInput.value?.click()
}

function validateAndEmit(file: File) {
  const maxBytes = props.maxSizeMb * 1024 * 1024

  if (!file.type.startsWith(props.accept.replace('*', ''))) {
    emit('error', 'Solo se permiten imágenes')
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