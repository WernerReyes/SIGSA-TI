<template>
  <div class="space-y-4">
    <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
      <div class="flex max-md:flex-col gap-4 items-start p-4">
        <div class="flex flex-col gap-2 w-full max-w-xl">
          <div class="flex items-center gap-2">
            <InputGroup>
              <InputGroupInput class="w-full" placeholder="Buscar marca..." v-model="search" />
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
          <Button variant="default" @click="$emit('edit', null)">Nueva marca</Button>
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
            <TableRow v-for="brand in filteredBrands" :key="brand.id">
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ brand.id }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap flex items-center gap-2">
                <Tag class="size-4 text-muted-foreground" />
                {{ brand.name }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(brand.created_at), 'dd/MM/yyyy HH:mm') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-sm text-muted-foreground">
                {{ format(new Date(brand.updated_at), 'dd/MM/yyyy') }}
              </TableCell>
              <TableCell class="px-4 py-2 whitespace-nowrap text-right flex gap-2">
                <Button variant="outline" size="icon-sm" @click="$emit('edit', brand)">
                  <Pencil />
                </Button>

                <Button variant="destructive" size="icon-sm" @click="$emit('delete', brand)">
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
import { Pencil, RefreshCcw, Search, Tag, Trash } from 'lucide-vue-next';
import { format } from 'date-fns-tz';
import { Brand } from '@/interfaces/brand.interface';

const props = defineProps<{
  brands: Brand[];
}>();

const search = ref('');

const filteredBrands = computed(() => {
  if (!search.value) return props.brands || [];
  return (props.brands || []).filter((brand) => brand.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>
