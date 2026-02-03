<template>
    <div class="space-y-6">
        <!-- Users Accordion -->
        <Accordion type="single" collapsible class="w-full">
            <AccordionItem v-for="user in tiUsersWithProjects" :key="user.id" :value="`user-${user.id}`">
                <AccordionTrigger class="hover:no-underline">
                    <div class="flex items-center gap-3 flex-1 text-left">
                        <div class="size-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                            <User class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ user.full_name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                        </div>
                        <div class="flex items-center gap-4 mr-2">
                            <div class="text-right">
                                <p class="text-xs text-muted-foreground">Proyectos</p>
                                <p class="font-bold text-sm">{{ user.projects.length }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-muted-foreground">Horas</p>
                                <p class="font-bold text-sm">{{ totalHoursByUser(user) }}h</p>
                            </div>
                        </div>
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <!-- Projects List -->
                    <div v-if="user.projects.length > 0" class="space-y-3 pt-4">
                        <div v-for="project in user.projects" :key="project.id"
                            class="p-3 rounded-lg bg-muted/30 border border-border/50 hover:border-border transition-colors">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ project.title }}</p>
                                    <p class="text-xs text-muted-foreground">DEV-{{ project.id }}</p>
                                </div>
                                <Badge :class="getStatusBadgeClass(project.status)" class="text-xs shrink-0">
                                    {{ getStatusLabel(project.status) }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground mb-2">
                                <Clock class="h-3 w-3" />
                                <span>{{ project.estimated_hours || 0 }}h estimadas</span>
                            </div>
                            <div v-if="project.latest_progress" class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground">Progreso</span>
                                    <span class="text-xs font-medium">{{ project.latest_progress.percentage }}%</span>
                                </div>
                                <Progress :model-value="project.latest_progress.percentage" class="h-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="py-4 text-center">
                        <p class="text-xs text-muted-foreground">Sin proyectos asignados</p>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>

        <!-- Summary Stats -->
        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-border/50">
            <div class="rounded-lg border border-border/80 bg-muted/30 p-4 text-center">
                <p class="text-xs text-muted-foreground mb-2">Miembros del Equipo</p>
                <p class="text-3xl font-bold text-primary">{{ tiUsersWithProjects.length }}</p>
            </div>
            <div class="rounded-lg border border-border/80 bg-muted/30 p-4 text-center">
                <p class="text-xs text-muted-foreground mb-2">Proyectos en Desarrollo</p>
                <p class="text-3xl font-bold text-amber-600">{{ totalProjectsInDevelopment }}</p>
            </div>
            <div class="rounded-lg border border-border/80 bg-muted/30 p-4 text-center">
                <p class="text-xs text-muted-foreground mb-2">Horas Totales Asignadas</p>
                <p class="text-3xl font-bold text-blue-600">{{ totalHoursAssigned }}h</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion';
import { type DevelopmentRequest, DevelopmentRequestStatus, getStatusOp } from '@/interfaces/developmentRequest.interface';
import { Clock, User } from 'lucide-vue-next';
import { computed } from 'vue';

interface TIUserWithProjects {
    id: number;
    full_name: string;
    email: string;
    projects: DevelopmentRequest[];
}

const props = defineProps<{
    developmentsByStatus: Record<string, DevelopmentRequest[]>;
}>();

// Get all active projects (not registered or rejected)
const activeProjects = computed(() => {
    const statuses = [
        DevelopmentRequestStatus.IN_ANALYSIS,
        DevelopmentRequestStatus.APPROVED,
        DevelopmentRequestStatus.IN_DEVELOPMENT,
        DevelopmentRequestStatus.IN_TESTING,
        DevelopmentRequestStatus.COMPLETED,
    ];

    return statuses.flatMap(status => props.developmentsByStatus[status] || []);
});

// Group projects by TI user
const tiUsersWithProjects = computed<TIUserWithProjects[]>(() => {
    const userMap = new Map<number, TIUserWithProjects>();

    activeProjects.value.forEach(project => {
        if (project.developers && project.developers.length > 0) {
            project.developers.forEach(user => {
                if (!userMap.has(user.staff_id)) {
                    userMap.set(user.staff_id, {
                        id: user.staff_id,
                        full_name: user.full_name,
                        email: user.email,
                        projects: [],
                    });
                }
                userMap.get(user.staff_id)!.projects.push(project);
            });
        }
    });

    return Array.from(userMap.values()).sort((a, b) => a.full_name.localeCompare(b.full_name));
});

// Calculate total hours by user
const totalHoursByUser = (user: TIUserWithProjects): number => {
    return user.projects.reduce((sum, project) => sum + (project.estimated_hours || 0), 0);
};

// Total projects in development (excluding registered, analysis, and rejected)
const totalProjectsInDevelopment = computed(() => {
    return activeProjects.value.filter(p => 
        [DevelopmentRequestStatus.IN_DEVELOPMENT, DevelopmentRequestStatus.IN_TESTING].includes(p.status)
    ).length;
});

// Total hours assigned to all users
const totalHoursAssigned = computed(() => {
    return tiUsersWithProjects.value.reduce((sum, user) => sum + totalHoursByUser(user), 0);
});

// Get status badge class
const getStatusBadgeClass = (status: DevelopmentRequestStatus): string => {
    const statusOp = getStatusOp(status);
    return statusOp.bg || 'bg-gray-100 text-gray-800';
};

// Get status label
const getStatusLabel = (status: DevelopmentRequestStatus): string => {
    const statusOp = getStatusOp(status);
    return statusOp.label || status;
};
</script>
