<template>
    <Dialog v-model:open="open" @update:open="(val) => {
        if (!val) resetForm();
    }">
        <DialogContent class="sm:max-w-xl">
            <form @submit.prevent="handleSubmit(handleSubmitForm)()">
                <DialogHeader>
                    <DialogHeader class="space-y-3 pb-4 border-b">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">

                                <MonitorCog class="size-5 text-primary" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <DialogTitle class="text-xl font-semibold">
                                    {{ currentService ? `Editar ${currentService?.name}` : 'Crear Nuevo Servicio' }}
                                </DialogTitle>
                                <p class="text-sm text-muted-foreground">
                                    {{ currentService ? `Actualiza la información de ${currentService?.name}` :
                                        'Completa los datos del nuevo servicio' }}
                                </p>
                            </div>
                        </div>
                    </DialogHeader>
                </DialogHeader>
                <div class="max-h-96 overflow-y-auto space-y-4">
                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 space-y-3">
                        <VeeField name="name" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Label for="name">Nombre del servicio</Label>
                                <Input id="name" v-bind="componentField" :disabled="isLoading"
                                    placeholder="Ej. SAP ERP" />
                                <FieldError v-if="errors.length" :errors="errors" />
                                <p v-else class="text-xs text-muted-foreground">Usa el nombre visible que verán los
                                    técnicos.</p>
                            </div>
                        </VeeField>


                        <VeeField name="url" v-slot="{ componentField, errors }">

                            <div class="space-y-1">
                                <Label for="url">URL</Label>
                                <div class="relative">
                                    <Input id="url" v-bind="componentField" :disabled="isLoading" type="url"
                                        placeholder="https://" class="pl-10" />
                                    <LinkIcon
                                        class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                </div>
                                <FieldError v-if="errors.length" :errors="errors" />
                                <p v-else class="text-xs text-muted-foreground">Incluye el protocolo (https://) para
                                    abrirlo
                                    desde
                                    la
                                    tabla.</p>
                            </div>
                        </VeeField>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">

                        <VeeField name="username" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Label for="username">Usuario</Label>
                                <div class="relative">
                                    <Input id="username" v-bind="componentField" :disabled="isLoading"
                                        placeholder="usuario" class="pl-10" />
                                    <UserRound
                                        class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                </div>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </div>
                        </VeeField>

                        <VeeField name="password" v-slot="{ componentField, errors }">
                            <div class="space-y-1">
                                <Label for="password">Contraseña</Label>
                                <div class="relative">
                                    <Input v-bind="componentField" id="password" :disabled="isLoading"
                                        :type="showPassword ? 'text' : 'password'" placeholder="••••••" class="pr-10" />
                                    <button type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                        @click="showPassword = !showPassword">
                                        <Eye v-if="showPassword" class="size-4" />
                                        <EyeOff v-else class="size-4" />
                                    </button>
                                </div>
                                <FieldError v-if="errors.length" :errors="errors" />
                            </div>
                        </VeeField>
                    </div>

                    <VeeField name="description" v-slot="{ componentField, errors }">
                        <div class="space-y-1">
                            <Label for="description">Descripción</Label>
                            <Textarea id="description" v-bind="componentField" :disabled="isLoading" rows="3"
                                placeholder="Acceso ERP para Finanzas" />
                            <FieldError v-if="errors.length" :errors="errors" />
                            <p v-else class="text-xs text-muted-foreground">Detalle el alcance (módulos, rol, área) para
                                auditoría.</p>
                        </div>
                    </VeeField>

                    <div
                        class="flex items-center justify-between rounded-lg border border-border/70 bg-muted/40 px-3 py-2">
                        <div>
                            <p class="text-sm font-medium">Activo</p>
                            <p class="text-xs text-muted-foreground">Habilita el servicio para asignarlo en la matriz.
                            </p>
                        </div>

                        <VeeField name="is_active" v-slot="{ componentField, errors }">
                            <div class="flex items-center gap-2">
                                <Switch :model-value="componentField.modelValue"
                                    @update:model-value="componentField.onChange" :disabled="isLoading" />
                                <Badge v-if="values.is_active" variant="outline"
                                    class="border-emerald-300 text-emerald-700">
                                    Activo</Badge>
                                <Badge v-else variant="outline" class="border-slate-300 text-slate-700">Inactivo</Badge>
                            </div>
                            <FieldError v-if="errors.length" :errors="errors" />
                        </VeeField>
                    </div>
                </div>
                <DialogFooter>

                    <Button type="button" variant="ghost" :disabled="isLoading" @click="resetForm">Cancelar</Button>
                    <Button type="submit" :disabled="isLoading || Object.keys(errors).length > 0">
                        {{ isLoading ? 'Guardando...' : 'Guardar servicio' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useApp } from '@/composables/useApp';
import { Service } from '@/interfaces/service.interface';
import { router, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Eye, EyeOff, Link as LinkIcon, MonitorCog, UserRound } from 'lucide-vue-next';
import { useForm, Field as VeeField } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import z from 'zod';
import { FieldError } from '@/components/ui/field';

const open = defineModel<boolean>('open', { default: false });

const currentService = defineModel<Service | null>('service', { default: null });



const page = usePage();
const { isLoading } = useApp();

const services = computed(() => {
    const servicesData = page.props.services as Service[];
    if (currentService.value) {
        return servicesData.filter(s => s.id !== currentService.value?.id);
    }
    return servicesData;
});

const formSchema = toTypedSchema(z.object({
    name: z.string({
        message: 'El nombre es obligatorio',
    }).min(3, 'El nombre es obligatorio').max(100, 'El nombre es muy largo').refine((val) => services.value.every(s => s.name?.toLowerCase().trim() !== val.toLowerCase().trim()), {
        message: 'El nombre ya está en uso',
    }),
    url: z.string().url('Debe ser una URL válida'),
    username: z.string().max(100, 'El usuario es muy largo'),
    password: z.string().max(100, 'La contraseña es muy larga'),
    description: z.string().max(500, 'La descripción es muy larga').optional().or(z.literal('')),
    is_active: z.boolean().default(true),
}));

const { handleSubmit, values, handleReset, errors, setValues } = useForm({
    initialValues: {
        name: currentService.value?.name || '',
        description: currentService.value?.description || '',
        url: currentService.value?.url || '',
        username: currentService.value?.username || '',
        password: currentService.value?.password || '',
        is_active: currentService.value?.is_active || true,
    },
    validationSchema: formSchema,
});


const showPassword = ref(false);


watch(currentService, (newService) => {
    if (!newService) {
        resetForm();
        return;
    }
    setValues({
        name: newService.name,
        description: newService.description || '',
        url: newService.url || '',
        username: newService.username || '',
        password: newService.password || '',
        is_active: newService.is_active,
    });

    open.value = true;
});

const resetForm = () => {
    handleReset();
    open.value = false;
};

const handleSubmitForm = () => {
    if (!currentService.value) {

    router.post('/access', { ...values }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            const service = flash?.service as Service | null;
            if (service) {
                router.prependToProp('services', service);
            }
        },
        onSuccess: () => {
            resetForm();
        },

    });
    return;
} 

    router.put(`/access/${currentService.value.id}`, { ...values }, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onFlash: (flash) => {
            const service = flash?.service as Service | null;
            if (service) {
                router.replaceProp('services', (services: Service[]) => {
                    return services.map(s => s.id === service.id ? service : s);
                });
            }
        },
        onSuccess: () => {
            resetForm();
        },

    });


};
</script>
