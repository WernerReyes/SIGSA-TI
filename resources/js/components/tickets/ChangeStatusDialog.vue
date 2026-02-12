<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) onResetAndClose()
    }">
        <DialogContent class="max-h-screen sm:max-w-2xl overflow-y-auto">
            <DialogHeader class="space-y-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                            Cambiar Estado de Ticket
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            TK-{{ ticket?.id }}
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <div class="space-y-6 py-6">
                <!-- Información del ticket actual -->
                <div v-if="ticket"
                    class="rounded-lg bg-gray-50 dark:bg-gray-800/50 p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado Actual</p>
                            <div class="mt-2">
                                <Badge :class="ticketStatusOptions[ticket.status]?.bg || 'bg-gray-100'">
                                    <component v-if="ticketStatusOptions[ticket.status]?.icon"
                                        :is="ticketStatusOptions[ticket.status].icon" class="mr-1" />
                                    {{ ticketStatusOptions[ticket.status]?.label || ticket.status }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selector de nuevo estado -->
                <VeeField name="status" v-slot="{ componentField, errors }">
                    <Field>
                        <FieldLabel for="title" class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            Seleccionar Nuevo Estado
                        </FieldLabel>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Elige el estado al que deseas cambiar este ticket
                        </p>

                        <SelectFilters :items="Object.values(ticketStatusOptions)" :show-selected-focus="false"
                            :show-refresh="false" :label="'Selecciona un nuevo estado'" item-label="label"
                            item-value="value" selected-as-label :default-value="componentField.modelValue"
                            @select="(value) => setFieldValue('status', value)" filter-placeholder="Buscar estado..."
                            empty-text="No se encontraron estados">
                            <template #item="{ item }">
                                <Badge :class="item.bg" class="px-3 py-1">
                                    <component :is="item.icon" class="mr-1" />
                                    {{ item.label }}
                                </Badge>
                            </template>
                        </SelectFilters>
                        <FieldError v-if="errors.length" :errors="errors" class="mt-2" />
                    </Field>
                </VeeField>
            </div>

            <DialogFooter class="gap-3 sm:gap-3">
                <Button type="button" variant="outline" @click="open = false" :disabled="isLoading"
                    class="flex-1 sm:flex-initial">
                    Cancelar
                </Button>
                <Button :disabled="disabled" type="submit" form="dialogForm" @click="handleSubmit(handleFormSubmit)()">


                    <Check />
                    Cambiar Estado
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { nextTick, ref } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Spinner } from '@/components/ui/spinner';
import { TicketStatus, ticketStatusOptions, type Ticket } from '@/interfaces/ticket.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm, Field as VeeField } from 'vee-validate';
import z from 'zod';
import SelectFilters from '../SelectFilters.vue';
import { Check } from 'lucide-vue-next';
import { computed } from 'vue';
import { useApp } from '@/composables/useApp';
import { isEqual } from '@/lib/utils';


const { ticket } = defineProps<{
    ticket: Ticket | null,
}>();

const open = defineModel<boolean>('open');


const { isLoading } = useApp();


const disabled = computed(() => {
    return isLoading.value || Object.keys(errors.value).length > 0 || isEqual(values.status, ticket?.status)
});

const formSchema = toTypedSchema(z.object({
    status: z.nativeEnum(TicketStatus, {
        required_error: 'El estado es obligatorio',
        invalid_type_error: 'El estado seleccionado no es válido',
        message: 'El estado seleccionado no es válido'
    })
}));

const { errors, handleSubmit, handleReset, setFieldValue, values } = useForm({
    validationSchema: formSchema,
    initialValues: {
        status: ticket?.status
    },
});

const onResetAndClose = () => {
    handleReset();
    open.value = false;
};

const handleFormSubmit = (values: { status?: TicketStatus }) => {

    router.post(
        `/tickets/${ticket?.id}/change-status`,
        {
            status: values.status
        },
        {
            preserveScroll: true,
            preserveState: true,
            preserveUrl: true,
            onFlash: (flash) => {
                if (flash.error) return;
                router.replaceProp('tickets.data', (tickets: Ticket[]) => {
                    return tickets.map((t: Ticket) => {
                        if (t.id === ticket?.id) {

                            return {
                                ...t,
                                status: values.status
                            };
                        }
                        return t;
                    });


                });

                onResetAndClose();

            },


        }
    );
};

</script>