<template>
  <div class="w-full space-y-4 text-sm">

    <!-- ================== VISTA (BACKEND) ================== -->
    <div v-if="viewUrls.length">
      <p class="text-xs uppercase tracking-wide text-muted-foreground mb-2">
        Archivos cargados
      </p>

      <div
        :class="previewMode === 'gallery'
          ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3'
          : 'space-y-2'"
      >
        <div
          v-for="url in viewUrls"
          :key="url"
          class="rounded-lg border bg-muted/30 overflow-hidden"
          :class="previewMode === 'gallery'
            ? 'aspect-square'
            : 'flex items-center gap-3 p-3'"
        >
          <img
            v-if="isImageUrl(url)"
            :src="url"
            class="object-cover w-full h-full"
          />

          <div
            v-else
            class="flex items-center gap-3"
          >
            <Upload class="size-4 text-muted-foreground" />
            <p class="truncate">{{ fileNameFromUrl(url) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ================== UPLOADS ================== -->
    <div v-if="localFiles.length">
      <p class="text-xs uppercase tracking-wide text-muted-foreground mb-2">
        Archivos nuevos
      </p>

      <div
        :class="previewMode === 'gallery'
          ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3'
          : 'space-y-2'"
      >
        <div
          v-for="(file, index) in localFiles"
          :key="file.name + index"
          class="relative group rounded-lg border bg-muted/40 overflow-hidden"
          :class="previewMode === 'gallery'
            ? 'aspect-square'
            : 'flex items-center gap-3 p-3'"
        >
          <img
            v-if="isImageFile(file)"
            :src="file.preview"
            class="object-cover w-full h-full"
          />

          <div
            v-else
            class="flex items-center gap-3"
          >
            <Upload class="size-4 text-muted-foreground" />
            <p class="truncate">{{ file.name }}</p>
          </div>

          <!-- ❌ eliminar SOLO uploads -->
          <button
            type="button"
            @click.stop="removeFile(index)"
            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100
                   transition bg-black/70 hover:bg-black text-white
                   rounded-full p-1"
          >
            ✕
          </button>
        </div>
      </div>
    </div>

    <!-- ================== DROPZONE ================== -->
    <div
      v-if="canAddMore"
      class="border-2 border-dashed rounded-xl p-5 flex flex-col
             items-center justify-center text-center cursor-pointer transition"
      :class="isDragging
        ? 'border-primary bg-primary/5'
        : 'border-border hover:bg-muted/40'"
      @click="openFilePicker"
      @dragover="onDragOver"
      @dragleave="onDragLeave"
      @drop="onDrop"
    >
      <Upload class="h-6 w-6 text-muted-foreground mb-2" />
      <p class="font-semibold">{{ label }}</p>
      <p class="text-xs text-muted-foreground">
        {{ acceptLabel }} · {{ maxSizeMb }}MB máx.
        · {{ localFiles.length }}/{{ maxFiles }}
      </p>
    </div>

    <input
      ref="fileInput"
      type="file"
      class="hidden"
      :accept="accept"
      multiple
      @change="onFileChange"
    />
  </div>
</template>

<script setup lang="ts">
import { Upload } from 'lucide-vue-next'
import { computed, ref } from 'vue'

/* ================= PROPS ================= */
interface Props {
  accept?: string
  maxSizeMb?: number
  maxFiles: number
  label?: string
  previewMode?: 'list' | 'gallery'
  currentUrl?: string | string[]
}

const props = withDefaults(defineProps<Props>(), {
  accept: 'image/*',
  maxSizeMb: 4,
  label: 'Arrastra o selecciona archivos',
  previewMode: 'gallery',
  currentUrl: ''
})

const emit = defineEmits<{
  (e: 'update:file', files: File[]): void
  (e: 'error', message: string): void
}>()

/* ================= STATE ================= */
const fileInput = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)
const localFiles = ref<(File & { preview?: string })[]>([])

/* ================= COMPUTED ================= */
const viewUrls = computed(() =>
  Array.isArray(props.currentUrl)
    ? props.currentUrl
    : props.currentUrl
      ? [props.currentUrl]
      : []
)

const canAddMore = computed(() =>
  localFiles.value.length < props.maxFiles
)

const acceptLabel = computed(() => {
  if (props.accept.includes('image')) return 'Imágenes'
  if (props.accept.includes('pdf')) return 'PDF'
  return 'Archivos'
})

/* ================= HELPERS ================= */
function isImageFile(file: File) {
  return file.type.startsWith('image/')
}

function isImageUrl(url: string) {
  return /\.(png|jpe?g|gif|webp|svg)$/i.test(url)
}

function fileNameFromUrl(url: string) {
  try {
    return decodeURIComponent(new URL(url).pathname.split('/').pop() || 'Archivo')
  } catch {
    return url.split('/').pop() || 'Archivo'
  }
}

/* ================= ACTIONS ================= */
function openFilePicker() {
  fileInput.value?.click()
}

function removeFile(index: number) {
  const file = localFiles.value[index]
  if (file.preview) URL.revokeObjectURL(file.preview)
  localFiles.value.splice(index, 1)
  emit('update:file', localFiles.value)
}

/* ================= VALIDATION ================= */
function validateAndAdd(files: File[]) {
  const maxBytes = props.maxSizeMb * 1024 * 1024

  for (const file of files) {
    if (localFiles.value.length >= props.maxFiles) {
      emit('error', `Máximo ${props.maxFiles} archivos`)
      return
    }

    if (file.size > maxBytes) {
      emit('error', `${file.name} supera ${props.maxSizeMb}MB`)
      continue
    }

    if (isImageFile(file)) {
      ;(file as any).preview = URL.createObjectURL(file)
    }

    localFiles.value.push(file as any)
  }

  emit('update:file', localFiles.value)
}

/* ================= EVENTS ================= */
function onFileChange(e: Event) {
  const files = Array.from((e.target as HTMLInputElement).files ?? [])
  validateAndAdd(files)
  ;(e.target as HTMLInputElement).value = ''
}

function onDragOver(e: DragEvent) {
  e.preventDefault()
  isDragging.value = true
}

function onDragLeave() {
  isDragging.value = false
}

function onDrop(e: DragEvent) {
  e.preventDefault()
  isDragging.value = false
  const files = Array.from(e.dataTransfer?.files ?? [])
  validateAndAdd(files)
}
</script>
