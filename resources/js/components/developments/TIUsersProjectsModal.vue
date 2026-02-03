<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-w-[min(100vw-1.5rem,900px)] sm:max-w-3xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <Users class="h-5 w-5 text-blue-600" />
                    </div>
                    <div>
                        <DialogTitle class="text-xl">Asignación de Proyectos por Usuario</DialogTitle>
                        <DialogDescription>
                            Proyectos activos asignados a cada miembro del equipo TI
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <TIUsersProjects :developments-by-status="developmentsByStatus" />
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import TIUsersProjects from './TIUsersProjects.vue';
import { type DevelopmentRequestSection } from '@/interfaces/developmentRequest.interface';
import { Users } from 'lucide-vue-next';

defineProps<{
    developmentsByStatus: DevelopmentRequestSection;
}>();

const open = defineModel<boolean>('open');
</script>
