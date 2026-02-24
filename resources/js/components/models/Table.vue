<template>
  <div class="space-y-4">
    <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
      <div class="flex max-md:flex-col gap-4 items-start p-4">
        <div class="flex flex-col gap-2 w-full max-w-xl">
          <div class="flex items-center gap-2">
            <InputGroup>
              <InputGroupInput class="w-full" placeholder="Buscar modelo..." v-model="search" />
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
          <Button variant="default" @click="$emit('edit', null)">Nuevo modelo</Button>
        </div>
      </div>

      <ScrollArea class="h-[calc(100vh-320px)]">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                ID</TableHead>

              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Nombre</TableHead>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Creado</TableHead>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Actualizado</TableHead>
              <TableHead class="px-4 py-2"></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="model in filteredModels" :key="model.id">
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ model.id }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap flex items-center gap-2">
                <Box class="size-4 text-muted-foreground" />
                {{ model.name }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(model.created_at), 'dd/MM/yyyy HH:mm') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(model.updated_at), 'dd/MM/yyyy HH:mm') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-right flex gap-2">
                <Button variant="outline" size="icon-sm" @click="$emit('edit', model)">
                  <Pencil />
                </Button>

                <Button variant="destructive" size="icon-sm" @click="$emit('delete', model)">
                  <Trash />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </ScrollArea>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { Box, Pencil, RefreshCcw, Search, Trash } from 'lucide-vue-next';
import { format } from 'date-fns-tz';
import { AssetModel } from '@/interfaces/assetModel.interface';

const props = defineProps<{
  models: AssetModel[];
}>();

const search = ref('');

const filteredModels = computed(() => {
  if (!search.value) return props.models || [];
  return (props.models || []).filter((model) => model.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>
