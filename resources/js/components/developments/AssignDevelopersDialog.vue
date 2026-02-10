<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) resetAndClose();
    }">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <div class="flex items-start gap-3">
                    <div
                        class="size-10 rounded-xl bg-blue-500/10 flex items-center justify-center ring-2 ring-blue-500/10">
                        <Users class="size-5 text-primary" />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg font-semibold">
                            Asignar Desarrolladores
                        </DialogTitle>
                        <DialogDescription>
                            Selecciona los desarrolladores que deseas asignar a este desarrollo.
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <div class="space-y-4 py-4">
                <!-- Current Development Info -->
                <section class="rounded-lg border bg-muted/30 p-3">
                    <p class="font-mono text-xs text-muted-foreground mb-1">DEV-{{ currentDev?.id }}</p>
                    <p class="font-medium text-sm">{{ currentDev?.title }}</p>
                </section>
            </div>


            <SelectFilters :label="selectLabel" :items="TIUsers" data-key="TIUsers" :icon="Users" item-value="staff_id"
                item-label="firstname" :multiple="true" :default-value="selectedDevelopers" :show-refresh="false"
                :selected-as-label="true" :full-width="true" :show-selected-focus="false"
                @select="(selects) => selectedDevelopers = selects" :max-label-length="MAX_LABEL_LENGTH">
                <template #item="{ item }">
                    {{ item.full_name }}
                </template>
            </SelectFilters>

            <DialogFooter>
                <Button @click="handleAssignDevelopers" :disabled="disabledForm">
                    Asignar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useApp } from '@/composables/useApp';
import { type DevelopmentRequest } from '@/interfaces/developmentRequest.interface';
import { router } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

import SelectFilters from '../SelectFilters.vue';
import { isEqual } from '@/lib/utils';

const MAX_LABEL_LENGTH = 3;

const open = defineModel<boolean>('open');
const currentDev = defineModel<DevelopmentRequest | null>('currentDevelopment');

const { isLoading, TIUsers } = useApp();

const selectedDevelopers = ref<number[]>([]);

const disabledForm = computed(() => {
    return selectedDevelopers.value.length === 0 || isLoading.value || isEqual(
        currentDev.value?.developers?.map(dev => dev.staff_id).sort() || [],
        selectedDevelopers.value.slice().sort()
    );
});


const selectLabel = computed(() => {
    if (!TIUsers.value.length) {
        const developers = (currentDev.value?.developers || []).map(dev => dev.firstname);
        if (developers.length === 0) {
            return 'Seleccionar Desarrolladores';
        } else if (developers.length > MAX_LABEL_LENGTH) {
            return `${developers.slice(0, MAX_LABEL_LENGTH).join(', ')}${developers.length > MAX_LABEL_LENGTH ? ` +${developers.length - MAX_LABEL_LENGTH}` : ''}`;
        } else {
            return developers.join(', ');
        }
    };
})

watch(currentDev, (dev) => {
    if (dev?.developers) {
        selectedDevelopers.value = dev.developers.map(dev => dev.staff_id);
    } else {
        selectedDevelopers.value = [];
    }
});

const handleAssignDevelopers = () => {
    if (!currentDev.value) return;
    router.patch(`/developments/${currentDev.value?.id}/assign-developers`, {
        developer_ids: selectedDevelopers.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            if (flash.error) return;
            currentDev.value!.developers = TIUsers.value.filter(user => selectedDevelopers.value.includes(user.staff_id));
            resetAndClose();

        },
    });
};

const resetAndClose = () => {
    selectedDevelopers.value = [];
    open.value = false;
    currentDev.value = null;
};
</script>
