<template>
    <!-- <Form :key="`${ticket?.id}-${ticket?.status}`" :initial-values="{
        status: ticket?.status || null
    }" v-slot="{ getValues, handleSubmit, errors, handleReset }" as="" keep-values :validation-schema="formSchema"> -->
    <Dialog v-model:open="open">
        <DialogContent class=" max-h-screen sm:max-w-106.25  overflow-y-auto">
            <DialogHeader>
                <h2 id="radix-:r88:" class="text-lg font-semibold leading-none tracking-tight">Cambiar Estado
                    TK-{{ ticket?.id }}</h2>

            </DialogHeader>

            <div class="space-y-4 py-4">
                <VeeField name="status" v-slot="{ componentField, errors }">
                    <Field>
                        <FieldLabel for="title">Nuevo Estado</FieldLabel>
                        <Select v-bind="componentField">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Selecciona un nuevo estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Estados</SelectLabel>

                                    <SelectItem v-for="{ value, label, bg, icon } in Object.values(ticketStatusOptions)"
                                        :key="value" :value="value">

                                        <Badge :class="bg">
                                            <component :is="icon" />
                                            {{ label }}
                                        </Badge>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </Field>
                    <FieldError v-if="errors.length" :errors="errors" />
                </VeeField>

            </div>

            <DialogFooter>
                <Button :disabled="isSubmitting
                    || Object.keys(errors).length > 0
                    " type="submit" form="dialogForm" @click="handleSubmit(handleFormSubmit)()">
                    <Spinner v-if="isSubmitting" />
                    {{ isSubmitting ? 'Cambiando estado...' : 'Cambiar Estado' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <!-- </Form> -->

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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { TicketStatus, ticketStatusOptions, type Ticket } from '@/interfaces/ticket.interface';
import { router } from '@inertiajs/vue3';
import { useForm, Field as VeeField } from 'vee-validate';
import z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';


const { ticket } = defineProps<{
    ticket: Ticket | null,
}>();

const open = defineModel<boolean>('open');

const isSubmitting = ref(false);

const formSchema = toTypedSchema(z.object({
    status: z.nativeEnum(TicketStatus, {
        required_error: 'El estado es obligatorio',
        invalid_type_error: 'El estado seleccionado no es válido',
        message: 'El estado seleccionado no es válido'
    })
}));

const { errors, handleSubmit, handleReset } = useForm({
    validationSchema: formSchema,
    initialValues: {
        status: ticket?.status
    },
});


const handleFormSubmit = (values: { status?: TicketStatus }) => {
    isSubmitting.value = true;

    router.post(
        `/tickets/${ticket?.id}/change-status`,
        {
            status: values.status
        },
        {

            onSuccess: () => {

                nextTick(() => {

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
                });


                handleReset();
                open.value = false;

            },
            onFinish: () => {
                isSubmitting.value = false;
            }

        }
    );
};

</script>