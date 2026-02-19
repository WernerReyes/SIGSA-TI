<template>
  <Dialog v-model:open="open" @update:open="onClose">
    <DialogContent class="sm:max-w-lg">
      <DialogHeader class="flex items-center gap-2 mb-4">
        <Tablet class="size-6 text-primary" />
        <h2 class="font-bold text-lg">{{ type ? 'Editar tipo' : 'Nuevo tipo' }}</h2>
      </DialogHeader>
      <form @submit.prevent="handleSubmit(onSubmit)">
        <div class="space-y-4">
          <VeeField name="name" v-slot="{ componentField, errors }">
            <div class="space-y-2">
              <Label :for="componentField.name" class="flex items-center gap-2">
                <Tablet class="h-4 w-4 text-muted-foreground" /> Nombre *
              </Label>
              <Input placeholder="Ej: Laptop" v-bind="componentField" />
              <FieldError v-if="errors.length" :errors="errors" />
            </div>
          </VeeField>
          <VeeField name="is_accessory" type="checkbox" v-slot="{ componentField }">
            <div class="flex items-center gap-2">
              <input type="checkbox" v-bind="componentField" id="is_accessory" />
              <Label for="is_accessory" class="text-sm">¿Es accesorio?</Label>
            </div>
          </VeeField>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <Button variant="secondary" type="button" @click="onClose">Cancelar</Button>
          <Button variant="default" type="submit">Guardar</Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';
import { Field as VeeField, useForm } from 'vee-validate';
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { Tablet } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { FieldError } from '@/components/ui/field';

const props = defineProps({
  currentType: Object,
});
const emit = defineEmits(['update:currentType', 'saved']);

const open = defineModel<boolean>('open');



const type = computed(() => props.currentType);

const schema = toTypedSchema(z.object({
  name: z.string().min(2, 'El nombre es obligatorio'),
  is_accessory: z.boolean().optional().default(false),
}));

const { handleSubmit, setValues, resetForm } = useForm({
  validationSchema: schema,
  initialValues: { name: '', is_accessory: false },
});

watch(type, (val) => {
  if (val) {
    setValues({ name: val.name, is_accessory: !!val.is_accessory });
  } else {
    resetForm();
  }
}, { immediate: true });

function onClose() {
  emit('update:currentType', null);
  open.value = false;
  resetForm();
}

function onSubmit(values: any) {
  emit('saved', { ...values, id: type.value?.id });
  onClose();
}
</script>
