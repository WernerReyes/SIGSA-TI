<template>
    <Dialog :open="open" @update:open="(val) => { if (!val) handleResetAndClose(); }">
        <!-- <DialogTrigger v-if="includeButton" as-child>
            <Button class="gap-2 shadow-md transition-all hover:shadow-lg">
                <Plus class="h-4 w-4" />
                Nuevo Ticket
            </Button>
        </DialogTrigger> -->

        <DialogContent class=" sm:max-w-4xl overflow-hidden">
            <DialogHeader class="space-y-3 border-b pb-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-primary/10 p-2">
                        <Tag class="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle class="text-xl">Crear Nuevo Ticket</DialogTitle>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Cuéntanos qué necesitas en 3 pasos sencillos
                        </p>
                    </div>
                </div>
            </DialogHeader>

            <ScrollArea class="max-h-[calc(100vh-14rem)] pr-4">
                <form id="ticketForm" class="space-y-6 py-4" @submit.prevent="handleSubmit(handleSave)()">
                    <!-- PASO 1: TIPO DE SOLICITUD -->
                    <Card>
                        <CardHeader class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Badge variant="secondary">Paso 1</Badge>
                                <CardTitle class="text-base">Tipo de solicitud</CardTitle>
                            </div>
                            <CardDescription>Selecciona la opcion que describe mejor tu necesidad.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <VeeField name="type" v-slot="{ field }">
                                    <div v-for="option in ticketTypeOptions" :key="option.value"
                                        class="relative cursor-pointer">
                                        <input :id="`type-${option.value}`" type="radio" :value="option.value"
                                            v-bind="field" class="sr-only"
                                            @change="setFieldValue('type', option.value)" />
                                        <label :for="`type-${option.value}`"
                                            class="flex h-full items-start gap-4 rounded-xl border-2 border-transparent p-4 transition-all"
                                            :class="values.type === option.value
                                                ? 'border-primary bg-primary/5 shadow-sm'
                                                : 'border-muted hover:border-muted-foreground/60'
                                                ">
                                            <div class="rounded-lg p-2" :class="option.bg">
                                                <component :is="option.icon" class="h-5 w-5" :class="option.color" />
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-sm font-semibold">{{ option.label }}</p>
                                                <p class="text-xs text-muted-foreground">{{ option.helper }}</p>
                                            </div>
                                        </label>
                                    </div>
                                </VeeField>
                            </div>
                            <p v-if="errors.type" class="text-xs text-destructive">{{ errors.type }}</p>
                        </CardContent>
                    </Card>

                    <!-- PASO 2: INFORMACIÓN PRINCIPAL -->
                    <Card>
                        <CardHeader class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Badge variant="secondary">Paso 2</Badge>
                                <CardTitle class="text-base">Informacion principal</CardTitle>
                            </div>
                            <CardDescription>Completa los datos basicos del ticket.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- TÍTULO -->
                            <FieldGroup>
                                <VeeField name="title" v-slot="{ field, errors }">
                                    
                                    <Field :data-invalid="errors.length > 0">
                                        <FieldLabel for="title" class="text-sm font-medium">
                                            Titulo
                                        </FieldLabel>
                                        <Input id="title" placeholder="Ej: No puedo acceder al sistema" class="h-10"
                                            v-bind="field" />
                                        <FieldError :errors="errors" />
                                    </Field>
                                </VeeField>
                            </FieldGroup>

                            <!-- DESCRIPCIÓN -->
                            <FieldGroup>
                                <VeeField name="description" v-slot="{ field, errors }">
                                    <Field :data-invalid="errors.length > 0">
                                        <FieldLabel for="description" class="text-sm font-medium">
                                            Describe el detalle
                                        </FieldLabel>
                                        <Textarea id="description"
                                            placeholder="Que ocurrio, cuando empezo y que intentaste hacer." rows="3"
                                            class="resize-none" v-bind="field" />
                                        <FieldError :errors="errors" />
                                    </Field>
                                </VeeField>
                            </FieldGroup>


                            <FieldGroup>
                                <Field>
                                    <FieldLabel class="text-sm font-medium">
                                        Adjuntar archivos (opcional)
                                    </FieldLabel>
                                    <Input type="file" multiple accept="image/*" />
                                    <p class="text-xs text-muted-foreground">Formatos sugeridos: PDF, PNG, JPG.</p>
                                </Field>
                            </FieldGroup>
                        </CardContent>
                    </Card>

                    <!-- PASO 3: IMPACTO Y URGENCIA -->
                    <Card>
                        <CardHeader class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Badge variant="secondary">Paso 3</Badge>
                                <CardTitle class="text-base">Impacto y urgencia</CardTitle>
                            </div>
                            <CardDescription>Describe a quien afecta y que tan urgente es.</CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-3">
                                <label class="text-xs font-medium text-muted-foreground">
                                    A cuantas personas afecta
                                </label>
                                <VeeField name="impact" v-slot="{ field, errors }">
                                    <div class="grid gap-2">
                                        <div v-for="option in impactOptions" :key="option.value"
                                            @click="setFieldValue('impact', option.value)"
                                            class="relative cursor-pointer">
                                            <input type="radio" :id="`impact-${option.value}`" :value="option.value"
                                                :checked="values.impact === option.value" class="sr-only" />
                                            <label :for="`impact-${option.value}`"
                                                class="flex items-center gap-3 rounded-lg border-2 border-transparent p-3 transition-all"
                                                :class="values.impact === option.value
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-muted hover:border-muted-foreground'
                                                    ">
                                                <div class="rounded-md bg-muted p-2">
                                                    <component :is="option.icon" class="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium">{{ option.label }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ option.description }}
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <FieldError :errors="errors" />
                                </VeeField>

                            </div>

                            <div class="space-y-3">
                                <label class="text-xs font-medium text-muted-foreground">
                                    Que tan urgente es para tu trabajo
                                </label>
                                <VeeField name="urgency" v-slot="{ field, errors }">
                                    <div class="grid gap-2">
                                        <div v-for="option in urgencyOptions" :key="option.value"
                                            @click="setFieldValue('urgency', option.value)"
                                            class="relative cursor-pointer">
                                            <input type="radio" :id="`urgency-${option.value}`" :value="option.value"
                                                :checked="values.urgency === option.value" class="sr-only" />
                                            <label :for="`urgency-${option.value}`"
                                                class="flex items-center gap-3 rounded-lg border-2 border-transparent p-3 transition-all"
                                                :class="values.urgency === option.value
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-muted hover:border-muted-foreground'
                                                    ">
                                                <div class="rounded-md bg-muted p-2">
                                                    <component :is="option.icon" class="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium">{{ option.label }}</p>
                                                    <p class="text-xs text-muted-foreground">{{ option.description }}
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <FieldError :errors="errors" />
                                </VeeField>
                            </div>
                        </CardContent>
                    </Card>



                    <!-- SECCIÓN CONDICIONAL: SERVICE REQUEST -->
                    <Card v-if="values.type === 'SERVICE_REQUEST'" class="border-sky-200/60 bg-sky-50/30">
                        <CardHeader class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Zap class="h-4 w-4 text-sky-600" />
                                <CardTitle class="text-base text-sky-900">Detalles de la solicitud</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-muted-foreground">
                                    Tipo de solicitud
                                </label>
                                <VeeField name="category" v-slot="{ field, errors }">
                                    <div class="grid gap-2 sm:grid-cols-3">
                                        <div v-for="option in Object.values(ticketCategoryOptions)" :key="option.value"
                                            @click="setFieldValue('category', option.value)"
                                            class="relative cursor-pointer">
                                            <input type="radio" :id="`request-${option.value}`" :value="option.value"
                                                :checked="values.category === option.value" class="sr-only" />
                                            <label :for="`request-${option.value}`"
                                                class="flex flex-col items-center gap-2 rounded-lg border-2 border-transparent p-3 text-center transition-all"
                                                :class="values.category === option.value
                                                    ? 'border-sky-500 bg-sky-100/40'
                                                    : 'border-muted hover:border-muted-foreground'
                                                    ">
                                                <component :is="option.icon" class="h-5 w-5" />
                                                <span class="text-sm font-medium">{{ option.label }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <FieldError :errors="errors" />
                                </VeeField>
                            </div>

                        </CardContent>
                    </Card>


                </form>
            </ScrollArea>

            <DialogFooter>
                <Button variant="outline" @click="handleResetAndClose()">
                    Cancelar
                </Button>
                <Button form="ticketForm" type="submit" class="gap-2" :disabled="disabledForm">
                    <CheckCircle2 class="h-4 w-4" />
                    Crear Ticket
                </Button>
            </DialogFooter>



        </DialogContent>
    </Dialog>
</template>



<script setup lang="ts">
import {
    Building2,
    CalendarCheck,
    CheckCircle2,
    Clock,
    LifeBuoy,
    Tag,
    TriangleAlert,
    User,
    Users,
    Zap
} from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, watch } from 'vue';

import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Field from '@/components/ui/field/Field.vue';
import FieldError from '@/components/ui/field/FieldError.vue';
import FieldGroup from '@/components/ui/field/FieldGroup.vue';
import FieldLabel from '@/components/ui/field/FieldLabel.vue';
import Input from '@/components/ui/input/Input.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Field as VeeField } from 'vee-validate';
// import * as yup from 'yup';
import { DialogFooter } from '@/components/ui/dialog';
import { Ticket, TicketCategory, TicketImpact, TicketType, TicketUrgency, ticketCategoryOptions } from '@/interfaces/ticket.interface';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import z from 'zod';
import { useApp } from '@/composables/useApp';
import { isEqual } from '@/lib/utils';



const open = defineModel<boolean>('open', {
    type: Boolean,
    default: false,
});


const selectedTicket = defineModel<Ticket | null>('selected', {
    type: Object,
    default: null,
});


const { isLoading } = useApp();

const disabledForm = computed(() => isLoading.value || Object.keys(errors.value).length > 0 || isEqual(values, initialValues.value));


const initialValues = computed(() => {
    const ticket = selectedTicket.value;
    return {
        type: ticket?.type || TicketType.SERVICE_REQUEST,
        title: ticket?.title || '',
        description: ticket?.description || '',
        // service_id: ticket?.service_id || '',
        // category_id: ticket?.category_id || '',
        impact: ticket?.impact || undefined,
        urgency: ticket?.urgency || undefined,
        // category: ticket?.category || 'ACCESS',
        images: [],

        category: ticket?.category || undefined,
    };

});

const formSchema = toTypedSchema(z.object({
    type: z.nativeEnum(TicketType, { message: 'Selecciona un tipo de solicitud' }),
    title: z.string().min(5, { message: 'El título debe tener al menos 5 caracteres' }).max(100, { message: 'El título no puede exceder los 100 caracteres' }),
    description: z.string().min(10, { message: 'La descripción debe tener al menos 10 caracteres' }).max(1000, { message: 'La descripción no puede exceder los 1000 caracteres' }),
    impact: z.nativeEnum(TicketImpact, { message: 'Selecciona el impacto' }),
    urgency: z.nativeEnum(TicketUrgency, { message: 'Selecciona la urgencia' }),
    category: z.nativeEnum(TicketCategory, { message: 'Selecciona la categoria del ticket' }).optional(),
}).refine((data) => {
    if (data.type === TicketType.SERVICE_REQUEST) {
        return !!data.category;
    }
    return true;
}, {
    path: ['category'],
    message: 'Selecciona la categoria del ticket',
}));


const { handleSubmit, values, setFieldValue, handleReset, errors, setValues } = useForm({
    // validationSchema,
    validationSchema: formSchema,
    initialValues: initialValues.value,
});

type FormValues = typeof values;

watch(selectedTicket, (newVal) => {
    if (newVal) {
        setValues(initialValues.value);
    } else {
        handleReset();
    }
});


const handleResetAndClose = () => {
    handleReset();
    open.value = false;
    if (selectedTicket.value) {
        selectedTicket.value = null;
    }

};



const handleSave = (data: FormValues) => {
    if (selectedTicket.value) {
    } else {
        router.post('/tickets', data, {
            onFlash: (flash) => {
                if (flash.error) return;
                handleResetAndClose();
            },
        });
    }
};



const ticketTypeOptions = [
    {
        value: TicketType.INCIDENT,
        label: 'Reportar un problema',
        helper: 'Incidentes, fallas o errores en servicios.',
        icon: TriangleAlert,
        color: 'text-red-600',
        bg: 'bg-red-500/10',
    },
    {
        value: TicketType.SERVICE_REQUEST,
        label: 'Solicitar algo',
        helper: 'Accesos, instalaciones o nuevos recursos.',
        icon: LifeBuoy,
        color: 'text-blue-600',
        bg: 'bg-blue-500/10',
    },
];

const impactOptions = [
    {
        value: TicketImpact.LOW,
        label: 'Solo a mí',
        description: 'Afecta a una sola persona.',
        icon: User,
    },
    {
        value: TicketImpact.MEDIUM,
        label: 'A mi equipo',
        description: 'Impacta en un equipo o area pequena.',
        icon: Users,
    },
    {
        value: TicketImpact.HIGH,
        label: 'A toda el área',
        description: 'Impacto amplio en el area o empresa.',
        icon: Building2,
    },
];

const urgencyOptions = [
    {
        value: TicketUrgency.LOW,
        label: 'No es urgente',
        description: 'Puede resolverse sin urgencia inmediata.',
        icon: Clock,
    },
    {
        value: TicketUrgency.MEDIUM,
        label: 'Necesito solución hoy',
        description: 'Necesito una solucion en el dia.',
        icon: CalendarCheck,
    },
    {
        value: TicketUrgency.HIGH,
        label: 'Está bloqueando mi trabajo',
        description: 'Trabajo detenido o bloqueo critico.',
        icon: TriangleAlert,
    },
];


</script>
