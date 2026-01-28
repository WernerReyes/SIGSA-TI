<template>
    <AlertDialog v-if="open" v-model:open="open">
        <AlertDialogTrigger v-if="trigger">{{ trigger }}</AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader v-if="title || description">
                <AlertDialogTitle v-if="title">{{ title }}</AlertDialogTitle>
                <AlertDialogDescription v-if="description">
                    {{ description }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="emit('cancel')">{{ cancelText }}</AlertDialogCancel>
                <AlertDialogAction @click="emit('confirm')">{{ actionText }}</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

const open = defineModel<boolean>('open');

withDefaults(defineProps<{
    trigger?: string
    title?: string
    description?: string
    cancelText?: string
    actionText?: string
}>(), {
    cancelText: 'Cancelar',
    actionText: 'Aceptar'
})

const emit = defineEmits<{
    (e: 'confirm'): void
    (e: 'cancel'): void
}>()

</script>