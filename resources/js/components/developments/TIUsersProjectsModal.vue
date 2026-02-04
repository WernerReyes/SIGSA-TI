<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button variant="outline" class="gap-2" size="sm">
                <Users class="h-4 w-4" />
                Equipo TI
            </Button>
        </DialogTrigger>
        <DialogContent class="max-w-[min(100vw-1.5rem,900px)] sm:max-w-3xl overflow-hidden">
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

            <ScrollArea class="max-h-[70vh] mt-4">
            <TIUsersProjects :developments-by-status="developmentsByStatus" />
            </ScrollArea>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import TIUsersProjects from './TIUsersProjects.vue';
import { type DevelopmentRequestSection } from '@/interfaces/developmentRequest.interface';
import { Users } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';

defineProps<{
    developmentsByStatus: DevelopmentRequestSection;
}>();


</script>
