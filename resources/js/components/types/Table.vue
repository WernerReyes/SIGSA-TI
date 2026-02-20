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

      <ScrollArea class="h-[calc(100vh-320px)]">
      
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Nombre</TableHead>
              <!-- <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                ¿Accesorio?</TableHead> -->
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Categoría</TableHead>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Creado</TableHead>
              <TableHead class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                Actualizado</TableHead>

              <TableHead class="px-4 py-2"></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="type in filteredTypes" :key="type.id">
              <TableCell class="px-4 py-2 whitespace-nowrap flex items-center">
                <component :is="assetTypeUI.type?.icon(type.name)" class="size-4 text-muted-foreground mr-2" />
                {{ type.name }}
              </TableCell>
              <!-- <TableCell class="px-4 py-2 whitespace-nowrap">
                <Badge :variant="type.is_accessory ? 'default' : 'secondary'">
                  {{ type.is_accessory ? 'Sí' : 'No' }}
                </Badge>
              </TableCell> -->
              <TableCell class="px-4 py-2 whitespace-nowrap">
                <Badge variant="outline">
                  <component :is="assetTypeUI.category?.icon(type.doc_category)" class="size-4 text-muted-foreground" />
                  {{ assetTypeUI.category?.label(type.doc_category) }}
                </Badge>
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(type.created_at), 'dd/MM/yyyy') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(type.updated_at), 'dd/MM/yyyy') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-right flex gap-2">
                <Button variant="outline" size="icon-sm" @click="$emit('edit', type)">
                  <Pencil />
                </Button>

                <Button v-if="type.is_deletable" variant="destructive" size="icon-sm" @click="$emit('delete', type)">
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
import { ref, computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupInput, InputGroupAddon } from '@/components/ui/input-group';
import { Search, RefreshCcw, Pencil, Trash } from 'lucide-vue-next';
import { useApp } from '@/composables/useApp';
import { assetTypeUI } from '@/interfaces/assetType.interface';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { format } from 'date-fns-tz';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';

const { assetTypes } = useApp();

const search = ref('');
const filteredTypes = computed(() => {
  if (!search.value) return assetTypes.value;
  return assetTypes.value.filter(t => t.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>
