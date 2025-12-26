<template>
    <Form :key="`${ticket?.id}-${ticket?.technician_id}`" :initial-values="{
        responsable_id: ticket?.technician_id || null
    }" v-slot="{ getValues, handleSubmit, errors, handleReset }" as="" keep-values :validation-schema="formSchema">
        <Dialog v-model:open="open" @update:open="(v) => !v && handleReset()">

            <DialogContent class=" max-h-screen sm:max-w-106.25  overflow-y-auto">
                <DialogHeader>

                    <h2 id="radix-:r88:" class="text-lg font-semibold leading-none tracking-tight">Asignar Ticket
                        TK-{{ ticket?.id }}</h2>

                </DialogHeader>

                <div class="space-y-4 py-4">


                    <VeeField name="responsable_id" v-slot="{ componentField, errors }">
                        <Field>
                            <FieldLabel for="title">Responsable</FieldLabel>
                            <Select v-bind="componentField">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecciona un responsable" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Responsable</SelectLabel>
                                        <SelectItem v-for="user in TIUsers" :key="user.staff_id" :value="user.staff_id">
                                            {{ user.full_name }}
                                        </SelectItem>

                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                            <FieldError v-if="errors.length" :errors="errors" />
                        </Field>
                    </VeeField>

                </div>

                <DialogFooter>

                    <Button :disabled="isSubmitting
                        || Object.keys(errors).length > 0
                        " type="submit" @click="handleSubmit(() => handleFormSubmit(getValues(), handleReset))">

                        <Spinner v-if="isSubmitting" />
                        {{ isSubmitting ? 'Reasignando...' : 'Reasignar' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </Form>

</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader
} from '@/components/ui/dialog';

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
import { type Ticket } from '@/interfaces/ticket.interface';
import { type User } from '@/interfaces/user.interface';
import { router, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Form, Field as VeeField } from 'vee-validate';
import z from 'zod';


const { ticket } = defineProps<{
    ticket: Ticket | null,
}>();
const open = defineModel<boolean>('open');

const TIUsers = computed<User[]>(() => {
    const users = (usePage().props.TIUsers || []) as User[];
    return users;
});

const isSubmitting = ref(false);

const formSchema = toTypedSchema(z.object({
    responsable_id: z.number({
        message: 'El responsable es obligatorio',
    })
}));

const handleFormSubmit = async (values: { responsable_id: number }, handleReset: () => void) => {
    isSubmitting.value = true;

    router.post(`/tickets/${ticket?.id}/reassign`, {

        responsable_id: values.responsable_id,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            handleReset();
            open.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;

        }
    });

};
</script>