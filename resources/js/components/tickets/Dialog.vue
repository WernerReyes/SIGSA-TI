<template>

    <Form :initial-values="{
        type: TicketType.SERVICE_REQUEST
    }" v-slot="{ getValues, handleSubmit, errors }" as="" keep-values :validation-schema="formSchema">

        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button class="w-fit ml-auto shadow-md hover:shadow-lg transition-all gap-2">
                    <Tag class="size-4" />
                    Nuevo Ticket
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-5/12 max-h-screen overflow-y-auto">
                <DialogHeader class="space-y-3 pb-4 border-b">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10">
                            <Tag class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <DialogTitle class="text-xl">Nuevo Ticket</DialogTitle>
                            <p class="text-sm text-muted-foreground mt-1">Completa los detalles para crear un ticket de soporte</p>
                        </div>
                    </div>
                </DialogHeader>

                <form id="dialogForm" @submit.prevent=" 
                    //   router.post('/tickets', getValues())
                    handleSubmit(onSubmit)
                    " class="space-y-5 py-4">
                    <!-- TÍTULO -->

                    <div class="flex max-sm:flex-col gap-5">
                    <FieldGroup>
                        <VeeField name="title" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="title" class="text-sm font-semibold">
                                    Título
                                </FieldLabel>
                                <Input 
                                    id="title" 
                                    placeholder="Ej: No puedo acceder al sistema de nómina"
                                    class="h-11 mt-2"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>

                    <!-- DESCRIPCIÓN -->
                    <FieldGroup>
                        <VeeField name="description" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="description" class="text-sm font-semibold">
                                    Descripción
                                </FieldLabel>
                                <Textarea 
                                    id="description" 
                                    rows="4"
                                    placeholder="Describe el problema en detalle: ¿Qué ocurrió? ¿Cuándo empezó? ¿Qué intentaste hacer?"
                                    class="mt-2 resize-none"
                                    v-bind="componentField" />
                                <FieldError v-if="errors.length" :errors="errors" />
                            </Field>
                        </VeeField>
                    </FieldGroup>
                    </div>

                    <FieldGroup>
                        <VeeField name="type" v-slot="{ componentField, errors }">
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel class="text-sm font-semibold mb-3">
                                    Tipo de Ticket
                                </FieldLabel>
                                <Tabs v-bind="componentField" :default-value="TicketType.SERVICE_REQUEST" class="w-full">
                                    <TabsList class="grid w-full grid-cols-2">
                                        <TabsTrigger v-for="type in Object.values(ticketTypeOptions)" :key="type.value"
                                            :value="type.value">
                                            {{ type.label }}
                                        </TabsTrigger>
                                    </TabsList>
                                </Tabs>
                            </Field>
                        </VeeField>
                    </FieldGroup>


                    <div class="grid gap-5"
                        :class="getValues().type === TicketType.SERVICE_REQUEST ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                        <!-- CATEGORÍA -->
                        <FieldGroup v-if="getValues().type === TicketType.SERVICE_REQUEST">
                            <VeeField name="request_type" v-slot="{ componentField, errors }">
                                <Field :data-invalid="!!errors.length">
                                    <FieldLabel class="text-sm font-semibold">
                                     Categoría
                                    </FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger class="h-11 mt-2">
                                            <SelectValue placeholder="Selecciona una categoría" />
                                        </SelectTrigger>
                                        <SelectContent>

                                            <SelectItem v-for="requestType in Object.values(ticketRequestTypeOptions)"
                                                :key="requestType.value" :value="requestType.value">
                                                {{ requestType.label }}
                                            </SelectItem>
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
                                    <FieldLabel class="text-sm font-semibold">
                                    Prioridad
                                    </FieldLabel>
                                    <Select v-bind="componentField">
                                        <SelectTrigger class="h-11 mt-2">
                                            <SelectValue placeholder="Selecciona la prioridad" />
                                        </SelectTrigger>

                                        <SelectContent>
                                            <SelectItem v-for="priority in Object.values(ticketPriorityOptions)"
                                                :key="priority.value" :value="priority.value">

                                                <Badge :class="priority.bg">
                                                    {{ priority.label }}
                                                </Badge>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FieldError v-if="errors.length" :errors="errors" />
                                </Field>
                            </VeeField>
                        </FieldGroup>
                    </div>


                </form>

                <DialogFooter class="pt-6 border-t gap-3">
                    <Button 
                        variant="outline" 
                        type="button" 
                        @click="open = false"
                        :disabled="isSubmitting"
                        class="flex-1 sm:flex-none">
                        Cancelar
                    </Button>
                    <Button 
                        :disabled="isSubmitting || Object.keys(errors).length > 0" 
                        type="submit" 
                        form="dialogForm"
                        class="flex-1 sm:flex-none shadow-md hover:shadow-lg transition-all gap-2">
                        <Spinner v-if="isSubmitting" class="size-4" />
                        <!-- <span v-if="!isSubmitting">✅</span> -->
                         <Check v-if="!isSubmitting" class="size-4" />
                        {{ isSubmitting ? 'Creando...' : 'Crear Ticket' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </Form>
</template>

<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { Form, Field as VeeField } from 'vee-validate'
import { ref } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog'
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { type Department } from '@/interfaces/department.interace'
import { TicketPriority, ticketPriorityOptions, TicketRequestType, ticketRequestTypeOptions, TicketType, ticketTypeOptions } from '@/interfaces/ticket.interface'
import { router } from '@inertiajs/vue3'
import { Check, Tag } from 'lucide-vue-next'
import * as z from 'zod'


defineProps<{
    departments: Array<Department>,

}>();

const open = ref(false);
const isSubmitting = ref(false);


const formSchema = toTypedSchema(z.object({
    title: z.string({
        message: 'El título es obligatorio',
    }).min(5, 'Minimo 5 caracteres').max(255, 'Máximo 255 caracteres'),
    description: z.string({
        message: 'La descripción es obligatoria',
    }).min(10, 'Minimo 10 caracteres').max(1000, 'Máximo 1000 caracteres'),
    type: z.nativeEnum(TicketType, {
        errorMap: () => ({ message: 'Selecciona un tipo válido' }),
        message: 'El tipo es obligatorio',
    }).default(TicketType.SERVICE_REQUEST),


    priority: z.nativeEnum(TicketPriority, {
        errorMap: () => ({ message: 'Selecciona una prioridad válida' }),
        message: 'La prioridad es obligatoria',
    }),

    request_type: z.nativeEnum(TicketRequestType, {
        errorMap: () => ({ message: 'Selecciona un tipo de solicitud válido' }),
        message: 'El tipo de solicitud es obligatorio',
    }).optional(),


}));

function onSubmit(values: any, { resetForm }: { resetForm: () => void }) {
    isSubmitting.value = true;
    router.post('/tickets', values, {
        only: ['tickets'],
        onSuccess: () => {
            open.value = false;
            resetForm();
        },
        onFinish: () => {
            isSubmitting.value = false;
        }


    })
}


</script>
