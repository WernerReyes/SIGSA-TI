<template>
    <Card class="border-border/60 shadow-sm bg-linear-to-br from-muted/40 via-background to-background">
        <CardHeader class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <CardTitle class="text-base flex items-center gap-2">
                    <span class="h-9 w-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <HardHat class="h-4 w-4" />
                    </span>
                    Bitácora de eventos
                </CardTitle>
                <CardDescription>
                    Registro de cambios de infraestructura y mantenimientos programados.
                </CardDescription>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button size="sm" variant="outline" @click="
                    filters.search = '';
                filters.types = [];
                ">
                    <RefreshCcw />
                    Limpiar filtros
                </Button>
                <Button size="sm" class="gap-2" @click="openDialog = true">
                    <Plus class="h-4 w-4" />
                    Registrar evento
                </Button>
            </div>
        </CardHeader>
        <CardContent>
            <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                <Input placeholder="Buscar por titulo, sistema o autor" v-model="filters.search" />

                <SelectFilters label="Tipos" :items="infrastructureEventTypeOptionsArray" :icon="Tags"
                    :show-refresh="false" :show-selected-focus="false" item-value="value" item-label="label"
                    :multiple="true" :default-value="filters.types" @select="(selects) => filters.types = selects">

                    <template #item="{ item }">
                        <Badge :class="getInfrasEventOp('type', item.value)?.bg">
                            <component :is="getInfrasEventOp('type', item.value)?.icon" class="size-4" />
                            {{ item.label }}
                        </Badge>
                    </template>

                </SelectFilters>


            </div>
            <div class="space-y-4">
                <div v-if="!eventsFiltered.length"
                    class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                    No hay eventos que coincidan con los filtros.
                </div>
                <div v-for="event in eventsFiltered" :key="event.id"
                    class="rounded-lg border bg-card p-4 transition hover:bg-muted/40 relative overflow-hidden">
                    <span class="absolute inset-y-0 left-0 w-1" />
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 rounded-full bg-background/70 border flex items-center justify-center">
                                <component :is="getInfrasEventOp('type', event.type)?.icon" class="size-4" />
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold">{{ event.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ event.description }}</p>
                                <div class="flex flex-wrap items-center gap-2 text-[11px] text-muted-foreground">
                                    <Badge class="text-[11px]" :class="getInfrasEventOp('type', event.type)?.bg">

                                        {{ getInfrasEventOp('type', event.type)?.label }}
                                    </Badge>

                                    <span>•</span>
                                    <span>{{ event.responsible?.full_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground sm:text-right flex gap-2 sm:items-end">
                            <Button size="icon" variant="ghost" class="mt-2" :disabled="isLoading"
                                @click="selectedEvent = event; openDialog = true">
                                <Pencil class="h-4 w-4" />
                            </Button>
                            <Button size="icon" :disabled="isLoading" variant="destructive" class="mt-2"
                                @click="selectedEvent = event; openAlertDialog = true">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                </div>
            </div>
        </CardContent>
    </Card>

    <UpsertEventDialog v-model:open="openDialog" v-model:selected="selectedEvent" />

    <AlertDialog title="Confirmar eliminación" v-model:open="openAlertDialog"
        description="¿Estás seguro de que deseas eliminar este evento? Esta acción no se puede deshacer."
        @confirm="handleDeleteInfrastructureEvent(selectedEvent?.id!)" @cancel="selectedEvent = null" />
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { InfrastructureEvent, InfrastructureEventType, getInfrasEventOp, infrastructureEventTypeOptionsArray } from '@/interfaces/infrastructureEvent.interface';
import { HardHat, Plus, RefreshCcw, Tags, Trash2, Pencil } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import SelectFilters from '../SelectFilters.vue';
import UpsertEventDialog from './UpsertEventDialog.vue';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useApp } from '@/composables/useApp';
import AlertDialog from '../AlertDialog.vue';

const props = defineProps({
    events: {
        type: Array as () => InfrastructureEvent[],
        required: true,
    },
});

const filters = reactive({
    search: '',
    types: [] as InfrastructureEventType[],
});

const { isLoading } = useApp();


const openDialog = ref(false);
const selectedEvent = ref<InfrastructureEvent | null>(null);
const openAlertDialog = ref(false);


const eventsFiltered = computed(() => {
    return props.events.filter(event => {
        const matchesSearch = filters.search
            ? event.title.toLowerCase().includes(filters.search.toLowerCase()) ||
            event.description.toLowerCase().includes(filters.search.toLowerCase()) ||
            event.responsible?.full_name.toLowerCase().includes(filters.search.toLowerCase())
            : true;

        const matchesType = filters.types.length > 0 ? filters.types.includes(event.type) : true;

        return matchesSearch && matchesType;
    });
});

const handleDeleteInfrastructureEvent = (eventId: number) => {
    router.delete(`/admin-control/infrastructure-events/${eventId}`, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            router.replaceProp('infrastructureEvents', (events: InfrastructureEvent[]) => {
                return events.filter(event => event.id !== eventId);
            });
            openAlertDialog.value = false;
            selectedEvent.value = null;
        }
    });
};

</script>