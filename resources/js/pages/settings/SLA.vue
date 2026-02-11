<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save, Timer } from 'lucide-vue-next';
import { type Component, computed } from 'vue';

import { type BreadcrumbItem } from '@/types';

import { priorityOp, TicketPriority } from '@/interfaces/ticket.interface';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type SlaPolicy } from '@/interfaces/slaPolicy.interface';

const props = defineProps<{
    slas: SlaPolicy[];
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuracion de SLA',
        href: '#',
    },
];

const form = useForm({
    slas: props.slas.map((sla) => ({
        priority: sla.priority,
        response_time_minutes: sla.response_time_minutes,
        resolution_time_minutes: sla.resolution_time_minutes,
    })),
});



const labelByName = Object.fromEntries(props.slas.map((sla) => [sla.priority, sla.label]));

const metaByName: Record<
    TicketPriority,
    { icon?: Component; tone: string; badge: string; accent: string; note: string }
> = {
    URGENT: {
        icon: priorityOp(TicketPriority.URGENT)?.icon,
        tone: 'from-rose-500/10 via-rose-500/5 to-transparent',
        badge: 'bg-rose-500/10 text-rose-600',
        accent: 'text-rose-600',
        note: 'Atencion inmediata y escalamiento rapido.',
    },
    HIGH: {
        icon: priorityOp(TicketPriority.HIGH)?.icon,
        tone: 'from-amber-500/10 via-amber-500/5 to-transparent',
        badge: 'bg-amber-500/10 text-amber-600',
        accent: 'text-amber-600',
        note: 'Impacto alto en operacion diaria.',
    },
    MEDIUM: {
        icon: priorityOp(TicketPriority.MEDIUM)?.icon,
        tone: 'from-sky-500/10 via-sky-500/5 to-transparent',
        badge: 'bg-sky-500/10 text-sky-600',
        accent: 'text-sky-600',
        note: 'Incidentes relevantes sin bloqueo total.',
    },
    LOW: {
        icon: priorityOp(TicketPriority.LOW)?.icon,
        tone: 'from-emerald-500/10 via-emerald-500/5 to-transparent',
        badge: 'bg-emerald-500/10 text-emerald-600',
        accent: 'text-emerald-600',
        note: 'Solicitudes de baja prioridad y soporte.',
    },
};

const slaCards = computed(() =>
    form.slas.map((sla) => ({
        ...sla,
        label: labelByName[sla.priority] ?? sla.priority,
        meta: metaByName[sla.priority],
    })),
);

const formatMinutes = (minutes: number) => {
    if (!minutes || minutes <= 0) return 'Sin definir';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours && mins) return `${hours} h ${mins} min`;
    if (hours) return `${hours} h`;
    return `${mins} min`;
};

const submit = () => {
    form.put('/settings/sla', {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Configuracion de SLA" />

        <SettingsLayout>
         
                <form  @submit.prevent="submit">

                    <div class="flex flex-wrap items-center justify-between gap-4 border-b px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-primary/10 p-2 text-primary">
                                <Timer class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold">Matriz de SLA por prioridad</p>
                                <p class="text-xs text-muted-foreground">
                                    Ajusta rapidamente los objetivos de respuesta y resolucion.
                                </p>
                            </div>
                        </div>
                        <div class="hidden items-center gap-2 text-xs text-muted-foreground sm:flex">
                            <span class="rounded-full bg-muted px-2 py-1">Minutos</span>
                            <span class="rounded-full bg-muted px-2 py-1">Respuesta vs Resolucion</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid gap-4 lg:grid-cols-2">
                            <div v-for="(sla, index) in slaCards" :key="sla.priority"
                                class="relative overflow-hidden rounded-xl border bg-linear-to-br"
                                :class="sla.meta?.tone">
                                <div class="space-y-4 p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="rounded-lg p-2" :class="sla.meta.badge">
                                                <component :is="sla.meta.icon" class="h-4 w-4" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold">{{ sla.label }}</p>
                                                <p class="text-xs text-muted-foreground">{{ sla.meta.note }}</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full px-2 py-1 text-xs font-medium"
                                            :class="sla.meta.badge">
                                            Prioridad
                                        </span>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="space-y-1">
                                            <label class="text-xs font-medium text-muted-foreground">
                                                Tiempo de respuesta (min)
                                            </label>
                                            <input v-model.number="form.slas[index].response_time_minutes" type="number"
                                                min="1"
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                                            <p class="text-xs" :class="sla.meta.accent">
                                                {{ formatMinutes(form.slas[index].response_time_minutes) }}
                                            </p>
                                            <p v-if="form.errors[`slas.${index}.response_time_minutes`]"
                                                class="text-xs text-destructive">
                                                {{ form.errors[`slas.${index}.response_time_minutes`] }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-medium text-muted-foreground">
                                                Tiempo de resolucion (min)
                                            </label>
                                            <input v-model.number="form.slas[index].resolution_time_minutes"
                                                type="number" min="1"
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                                            <p class="text-xs" :class="sla.meta.accent">
                                                {{ formatMinutes(form.slas[index].resolution_time_minutes) }}
                                            </p>
                                            <p v-if="form.errors[`slas.${index}.resolution_time_minutes`]"
                                                class="text-xs text-destructive">
                                                {{ form.errors[`slas.${index}.resolution_time_minutes`] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-muted-foreground">
                                Los tiempos se guardan en minutos y se aplican a nuevos tickets.
                            </p>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-50"
                                :disabled="form.processing">
                                <Save class="h-4 w-4" />
                                Guardar cambios
                            </button>
                        </div>
                    </div>

                </form>
    
        </SettingsLayout>
    </AppLayout>
</template>