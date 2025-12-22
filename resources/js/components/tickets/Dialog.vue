<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'
import { h } from 'vue'

import { toast } from 'vue-sonner'
import * as z from 'zod'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import {
    Field,
    FieldDescription,
    FieldError,
    FieldGroup,
    FieldLabel,
} from '@/components/ui/field'
import { Textarea } from '@/components/ui/textarea'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { TabsList, Tabs, TabsTrigger } from '@/components/ui/tabs'
import { Tag } from 'lucide-vue-next'
import { TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface'
import { User } from '@/interfaces/user.interface'


defineProps<{
    technicians: Array<User>
}>();


const formSchema = toTypedSchema(z.object({
    title: z.string({
        message: 'El título es obligatorio',
    }).min(3, 'El título es obligatorio'),
    description: z.string({
        message: 'La descripción es obligatoria',
    }).min(10, 'La descripción es obligatoria'),
    type: z.enum(['account', 'password'], {
        errorMap: () => ({ message: 'Selecciona un tipo válido' }),
    }),
    // category: z.enum(['hardware', 'software', 'red'], {
    //     errorMap: () => ({ message: 'Selecciona una categoría válida' }),
    // }),
    // priority: z.enum(['baja', 'media', 'alta', 'critica'], {
    //     errorMap: () => ({ message: 'Selecciona una prioridad válida' }),
    // }),
    
    technician_id: z.number({
        invalid_type_error: 'Selecciona un técnico válido',
        required_error: 'El técnico es obligatorio',
    }),
    asset_id: z.string().optional(),
}))

function onSubmit(values: any) {
    toast('You submitted the following values:', {
        description: h('pre', { class: 'mt-2 w-[320px] rounded-md bg-neutral-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
    })
}
</script>

<template>

    <Form v-slot="{ handleSubmit, getValues }" as="" keep-values :validation-schema="formSchema">

        <Dialog>
            <DialogTrigger as-child>
                <Button class="w-fit ml-auto">
                    <Tag />
                    Nuevo Ticket
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-106.25 max-h-screen overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Nuevo Ticket</DialogTitle>
                </DialogHeader>


                <form id="dialogForm" @submit="handleSubmit($event, onSubmit)" class="space-y-3">
                    <!-- TÍTULO -->
                    <FieldGroup>
                        <VeeField name="title" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="title">Título</FieldLabel>
                                <Input id="title" placeholder="Describe brevemente el problema"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- DESCRIPCIÓN -->
                    <FieldGroup>
                        <VeeField name="description" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="description">Descripción</FieldLabel>
                                <Textarea id="description" rows="4"
                                    placeholder="Proporciona detalles adicionales del problema..."
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <FieldGroup>
                        <VeeField name="type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <!-- <Input id="eventId" type="number" placeholder="12345"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" /> -->

                                <Tabs v-bind="componentField" :default-value="TicketType.SERVICE_REQUEST" class="mt-4">

                                    <TabsList>
                                        <!-- <TabsTrigger value="account">
                                            Account
                                        </TabsTrigger> -->
                                        <!-- <TabsTrigger value="password">
                                            Password
                                        </TabsTrigger> -->
                                        <TabsTrigger v-for="type in Object.values(ticketTypeOptions)" :key="type.value"
                                            :value="type.value">
                                            {{ type.label }}
                                        </TabsTrigger>


                                    </TabsList>
                                </Tabs>
                            </Field>
                        </VeeField>
                    </FieldGroup>


                    <div class="grid gap-4"
                        :class="getValues().type === TicketType.SERVICE_REQUEST ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                        <!-- CATEGORÍA -->
                        <FieldGroup v-if="getValues().type === TicketType.SERVICE_REQUEST">
                            <VeeField name="category" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Categoría</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="hardware">Hardware</SelectItem>
                                            <SelectItem value="software">Software</SelectItem>
                                            <SelectItem value="red">Red</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>

                        <!-- PRIORIDAD -->
                        <FieldGroup>
                            <VeeField name="priority" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel>Prioridad</FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="baja">Baja</SelectItem>
                                            <SelectItem value="media">Media</SelectItem>
                                            <SelectItem value="alta">Alta</SelectItem>
                                            <SelectItem value="critica">Crítica</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>

                    <!-- SOLICITANTE -->
                    <FieldGroup>
                        <VeeField name="reques_id" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel>Solicitante</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar solicitante" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="tech in technicians" :key="tech.staff_id"
                                            :value="tech.staff_id">
                                            {{ tech.firstname }} {{ tech.lastname }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- ACTIVO -->
                    <FieldGroup>
                        <VeeField name="asset_id" v-slot="{ componentField, errors }">
                            <Field>
                                <FieldLabel>Activo Relacionado (opcional)</FieldLabel>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar activo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pc-01">PC-01</SelectItem>
                                        <SelectItem value="laptop-02">Laptop</SelectItem>
                                    </SelectContent>
                                </Select>
                            </Field>
                        </VeeField>
                    </FieldGroup>
                </form>

                <DialogFooter>
                    <Button type="submit" form="dialogForm">
                        Save changes
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </Form>
</template>
