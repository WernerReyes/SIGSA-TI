<template>
  <div class="space-y-4">
    <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
      <div class="flex max-md:flex-col gap-4 items-start p-4">
        <div class="flex flex-col gap-2 w-full max-w-xl">
          <div class="flex items-center gap-2">
            <InputGroup>
              <InputGroupInput class="w-full" placeholder="Buscar tipo..." v-model="search" />
              <InputGroupAddon>
                <Search />
              </InputGroupAddon>
            </InputGroup>
            <Button variant="ghost" size="icon" :disabled="!search" class="rounded-full" @click="search = ''">
              <RefreshCcw class="size-4" />
            </Button>
          </div>
        </div>
        <div class="max-sm:w-full ml-auto flex gap-2 flex-wrap justify-end items-end">
          <Button variant="default" @click="$emit('edit', null)">Nuevo tipo</Button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-muted-200">
          <thead>
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">¿Accesorio?</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-muted-200">
            <tr v-for="type in filteredTypes" :key="type.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ type.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <Badge :variant="type.is_accessory ? 'success' : 'secondary'">
                  {{ type.is_accessory ? 'Sí' : 'No' }}
                </Badge>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <Button variant="outline" size="sm" @click="$emit('edit', type)">Editar</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupInput, InputGroupAddon } from '@/components/ui/input-group';
import { Search, RefreshCcw } from 'lucide-vue-next';

const props = defineProps({
  types: Array,
});

const search = ref('');
const filteredTypes = computed(() => {
  if (!search.value) return props.types;
  return props.types.filter(t => t.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>
