<template>
    <div class="rounded-lg border bg-card overflow-hidden">
        <!-- Tabla Responsiva -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Título</th>
                        <th class="px-4 py-3 text-left font-semibold">Solicitante</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold">Prioridad</th>
                        <th class="px-4 py-3 text-left font-semibold">Estado</th>
                        <th class="px-4 py-3 text-left font-semibold">Progreso</th>
                        <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="requirement in requirements"
                        :key="requirement.id"
                        class="border-b hover:bg-muted/50 transition-colors"
                    >
                        <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                            #{{ requirement.id }}
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium">{{ requirement.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ requirement.description }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ requirement.requester_area }}</td>
                        <td class="px-4 py-3">
                            <Badge :variant="getTypeVariant(requirement.type)" class="text-xs">
                                {{ getTypeLabel(requirement.type) }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <Badge :variant="getPriorityVariant(requirement.priority)" class="text-xs">
                                {{ getPriorityLabel(requirement.priority) }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <Badge :variant="getStatusVariant(requirement.status)" class="text-xs">
                                {{ getStatusLabel(requirement.status) }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-muted rounded-full h-1.5">
                                    <div
                                        class="bg-primary h-1.5 rounded-full transition-all"
                                        :style="{ width: requirement.progress + '%' }"
                                    ></div>
                                </div>
                                <span class="text-xs font-medium whitespace-nowrap">
                                    {{ requirement.progress }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="h-8 w-8 p-0"
                                    @click="editRequirement(requirement)"
                                    title="Ver detalles"
                                >
                                    <Eye class="h-4 w-4" />
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="h-8 w-8 p-0"
                                    @click="deleteRequirement(requirement.id)"
                                    title="Eliminar"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Mensaje cuando no hay requerimientos -->
            <div v-if="requirements.length === 0" class="text-center py-12">
                <FileText class="h-12 w-12 text-muted-foreground/50 mx-auto mb-3" />
                <p class="text-muted-foreground">No hay requerimientos registrados aún</p>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="requirements.length > 0" class="flex items-center justify-between px-4 py-3 border-t bg-muted/50">
            <p class="text-sm text-muted-foreground">
                Mostrando {{ requirements.length }} requerimientos
            </p>
            <div class="flex gap-2">
                <Button size="sm" variant="outline">← Anterior</Button>
                <Button size="sm" variant="outline">Siguiente →</Button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Eye, Trash2, FileText } from 'lucide-vue-next';

interface Requirement {
    id: number;
    title: string;
    description: string;
    requester_area: string;
    type: string;
    priority: string;
    status: string;
    progress: number;
}

interface Props {
    requirements: Requirement[];
}

defineProps<Props>();

const emit = defineEmits<{
    edit: [requirement: Requirement];
    delete: [id: number];
}>();

const getTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        new_module: 'Nuevo Módulo',
        modification: 'Modificación',
        integration: 'Integración',
        optimization: 'Optimización',
    };
    return labels[type] || type;
};

const getTypeVariant = (type: string) => {
    const variants: Record<string, any> = {
        new_module: 'default',
        modification: 'secondary',
        integration: 'outline',
        optimization: 'outline',
    };
    return variants[type] || 'outline';
};

const getPriorityLabel = (priority: string) => {
    const labels: Record<string, string> = {
        low: 'Baja',
        medium: 'Media',
        high: 'Alta',
        critical: 'Crítica',
    };
    return labels[priority] || priority;
};

const getPriorityVariant = (priority: string) => {
    const variants: Record<string, any> = {
        low: 'outline',
        medium: 'secondary',
        high: 'outline',
        critical: 'destructive',
    };
    return variants[priority] || 'outline';
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        draft: 'Borrador',
        pending_estimation: 'Pendiente Estimación',
        pending_technical_approval: 'Aprobación Técnica',
        pending_strategic_approval: 'Aprobación Estratégica',
        in_execution: 'En Ejecución',
        completed: 'Completado',
        rejected: 'Rechazado',
    };
    return labels[status] || status;
};

const getStatusVariant = (status: string) => {
    const variants: Record<string, any> = {
        draft: 'outline',
        pending_estimation: 'secondary',
        pending_technical_approval: 'secondary',
        pending_strategic_approval: 'secondary',
        in_execution: 'outline',
        completed: 'default',
        rejected: 'destructive',
    };
    return variants[status] || 'outline';
};

const editRequirement = (requirement: Requirement) => {
    emit('edit', requirement);
};

const deleteRequirement = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este requerimiento?')) {
        emit('delete', id);
    }
};
</script>
