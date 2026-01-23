<template>

    <Dialog v-model:open="open" @update:open="(v) => !v && handleFormReset()">

        <DialogContent class="max-h-screen sm:max-w-lg overflow-y-auto">
            <DialogHeader class="space-y-3 pb-4 border-b">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-amber-500/10">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Reasignar Ticket</h2>
                        <p class="text-sm text-muted-foreground mt-1">
                            <span class="font-medium text-foreground">TK-{{ ticket?.id }}</span> - Selecciona un
                            nuevo responsable
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <div class="space-y-5 py-6">
                <!-- Información del ticket actual -->
                <div v-if="ticket?.responsible" class="p-4 rounded-lg bg-muted/50 border border-border">
                    <p
                        class="text-xs text-muted-foreground uppercase tracking-wide font-semibold mb-2 flex items-center gap-2">
                        <UserCheck class="size-4" />
                        Responsable Actual
                    </p>
                    <p class="text-sm font-medium">{{ ticket.responsible.full_name }}</p>
                </div>

                <!-- Campo de selección -->
                <VeeField name="responsible_id" v-slot="{ componentField, errors }">
                    <Field>
                        <FieldLabel for="responsible_id" class="text-sm font-semibold">
                            Nuevo Responsable
                        </FieldLabel>
                        <SelectFilters data-key="TIUsers" :items="TIUsers" :show-selected-focus="false"
                            :show-refresh="false" :label="placeholder" item-label="full_name" item-value="staff_id"
                            selected-as-label :default-value="componentField.modelValue"
                            @select="(value) => setFieldValue('responsible_id', +value)"
                            filter-placeholder="Buscar responsable..." empty-text="No se encontraron responsables">
                        </SelectFilters>
                        <FieldError v-if="errors.length" :errors="errors" />
                    </Field>
                </VeeField>
            </div>

            <DialogFooter class="pt-6 border-t gap-3">
                <Button variant="outline" type="button" @click="open = false" :disabled="isLoading"
                    class="flex-1 sm:flex-none">
                    Cancelar
                </Button>
                <Button :disabled="isLoading || Object.keys(errors).length > 0" type="submit"
                    @click="handleSubmit(handleFormSubmit)()"
                    class="flex-1 sm:flex-none shadow-md hover:shadow-lg transition-all gap-2">
                    <Spinner v-if="isLoading" class="h-4 w-4" />

                    <Save class="size-4" v-if="!isLoading" />
                    {{ isLoading ? 'Asignando...' : 'Asignar Ticket' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>


</template>

<script setup lang="ts">
import { computed, watch } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { useApp } from '@/composables/useApp';
import { type Ticket } from '@/interfaces/ticket.interface';
import { type User } from '@/interfaces/user.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Save, UserCheck } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import z from 'zod';
import SelectFilters from '../SelectFilters.vue';

const ticket = defineModel<Ticket | null>('ticket');
const open = defineModel<boolean>('open');

const { TIUsers, isLoading } = useApp()

const formSchema = toTypedSchema(z.object({
    responsible_id: z.number({
        message: 'El responsable es obligatorio',
    })
}));

const { handleReset, handleSubmit, errors, setFieldValue } = useForm({
    initialValues: {
        responsible_id: ticket?.value?.responsible_id,
    },
    validationSchema: formSchema,
});



const placeholder = computed(() => {
    let placeholderText = 'Selecciona un responsable';
    if (ticket?.value?.responsible) {
        placeholderText = ticket.value.responsible.full_name;
    }
    return placeholderText;

});


watch(() => ticket?.value?.responsible_id, (newVal) => {
    setFieldValue('responsible_id', newVal);
});

const handleFormReset = () => {
    handleReset();
    ticket.value = null;

};

const handleFormSubmit = async (values: { responsible_id: number }) => {

    router.post(`/tickets/${ticket?.value?.id}/assign`, {

        responsible_id: values.responsible_id,
    }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            const responsible = flash.responsible as User | null;
            if (!responsible) return;
            router.replaceProp('tickets.data', (tickets: Ticket[]) => {
                return tickets.map(t => {
                    if (t.id === ticket?.value?.id) {
                        return {
                            ...t,
                            responsible,
                            responsible_id: values.responsible_id,
                        };
                    }
                    return t;
                });
            });
        },
        onSuccess: () => {
            handleFormReset();
            open.value = false;
        },

    });

};
</script>