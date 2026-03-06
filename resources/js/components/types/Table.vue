<template>
  <div class="space-y-4">
    <div class="rounded-xl border bg-linear-to-br from-muted/40 via-background to-background shadow-sm">
      <div class="flex max-md:flex-col gap-4 items-start p-4">
        <div class="flex flex-col gap-2 w-full ">
          <div class="flex w-full items-center gap-2">
            <InputGroup>
              <InputGroupInput class="w-full" placeholder="Buscar tipo, marca o modelo..." v-model="search" 
               @update:model-value="(value: string) => search = value.trim()"
              />
              <InputGroupAddon>
                <Search />
              </InputGroupAddon>
            </InputGroup>
            <Button variant="ghost" size="icon" :disabled="!search" class="rounded-full" @click="search = ''">
              <RefreshCcw class="size-4" />
            </Button>
          </div>
        </div>
      </div>

      <ScrollArea class="h-[calc(100vh-320px)]">

        <Table>
          <TableHeader class="bg-muted/70 sticky top-0 z-10">
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
              <TableHead
                v-for="header in headerGroup.headers"
                :key="header.id"
                class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider"
              >
                <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
              </TableHead>
            </TableRow>
          </TableHeader>

          <TableBody v-if="table.getRowModel().rows.length">
            <TableRow
              v-for="row in table.getRowModel().rows"
              :key="row.id"
              :class="[
                row.depth === 0 ? 'bg-background' : row.depth === 1 ? 'bg-muted/20' : 'bg-muted/35',
                'hover:bg-muted/50 transition-colors',
              ]"
            >
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-2 align-middle">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
              </TableCell>
            </TableRow>
          </TableBody>

          <TableBody v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="text-center py-8 text-sm text-muted-foreground">
                No hay resultados para la busqueda aplicada.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

      </ScrollArea>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { AssetModel } from '@/interfaces/assetModel.interface';
import type { Brand } from '@/interfaces/brand.interface';
import type { AssetType, AssetTypeDocCategory, TypeName } from '@/interfaces/assetType.interface';
import type { ColumnDef, ExpandedState } from '@tanstack/vue-table';
import { FlexRender, getCoreRowModel, getExpandedRowModel, getFilteredRowModel, useVueTable } from '@tanstack/vue-table';
import { computed, h, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { InputGroup, InputGroupInput, InputGroupAddon } from '@/components/ui/input-group';
import { Search, RefreshCcw, Pencil, Trash, ChevronRight, ChevronDown, Tag, Boxes, FileText } from 'lucide-vue-next';
import { useApp } from '@/composables/useApp';
import { assetTypeUI } from '@/interfaces/assetType.interface';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { format } from 'date-fns-tz';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { valueUpdater } from '@/lib/utils';

type TableLevel = 'type' | 'brand' | 'model';

interface NestedTableRow {
  id: string;
  entityId: number;
  level: TableLevel;
  name: string;
  relatedCount: number;
  categoryLabel?: string;
  updatedAt: Date | string;
  typeRef?: AssetType;
  children?: NestedTableRow[];
}

const emit = defineEmits<{
  (e: 'edit', value: AssetType | null): void;
  (e: 'edit-brand', value: Brand | null): void;
  (e: 'edit-model', value: AssetModel | null): void;
  (e: 'delete', value: AssetType): void;
  (e: 'delete-brand', value: Brand): void;
  (e: 'delete-model', value: AssetModel): void;
}>();

const { assetTypes, brands, models, isFromRRHH } = useApp();

const search = ref('');

const expanded = ref<ExpandedState>({});


const sourceTypes = computed(() => (assetTypes.value.length ? assetTypes.value : []));
const sourceBrands = computed(() => (brands.value.length ? brands.value : []));
const sourceModels = computed(() => (models.value.length ? models.value : []));

const safeDate = (value: Date | string) => {
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? new Date() : date;
};

const rows = computed<NestedTableRow[]>(() => {
  const typeList = sourceTypes.value;
  const brandList = sourceBrands.value;
  const modelList = sourceModels.value;

  if (!typeList.length) {
    return [];
  }

  const typeRows = typeList.map((type) => {
    const brandRows = brandList
      .filter(brand => brand.type_id === type.id)
      .map((brand) => {
        const linkedModels = modelList.filter(model => model.brand_id === brand.id);

      return {
        id: `brand-${type.id}-${brand.id}`,
        entityId: brand.id,
        level: 'brand',
        name: brand.name,
        relatedCount: linkedModels.length,
        updatedAt: brand.updated_at,
        children: linkedModels.map((model) => ({
          id: `model-${type.id}-${brand.id}-${model.id}`,
          entityId: model.id,
          level: 'model',
          name: model.name,
          relatedCount: 1,
          updatedAt: model.updated_at,
        })),
      } as NestedTableRow;
    });

    return {
      id: `type-${type.id}`,
      entityId: type.id,
      level: 'type',
      name: type.name,
      categoryLabel: assetTypeUI.category.label(type.doc_category),
      relatedCount: brandRows.length,
      updatedAt: type.updated_at,
      typeRef: type,
      children: brandRows,
    } as NestedTableRow;
  });

  return typeRows;
});

const columns: ColumnDef<NestedTableRow>[] = [
  {
    accessorKey: 'name',
    header: 'Elemento',
    cell: ({ row }) => {
      const iconByLevel = {
        type: assetTypeUI.type.icon(row.original.name as TypeName),
        brand: Tag,
        model: Boxes,
      };

      return h(
        'div',
        { class: 'flex items-center gap-2' },
        [
          h('div', { style: { marginLeft: `${row.depth * 1.2}rem` }, class: 'flex items-center gap-1.5' }, [
            row.getCanExpand()
              ? h(
                  Button,
                  {
                    variant: 'ghost',
                    size: 'icon',
                    class: 'size-7',
                    onClick: row.getToggleExpandedHandler(),
                  },
                  () => (row.getIsExpanded() ? h(ChevronDown, { class: 'size-4' }) : h(ChevronRight, { class: 'size-4' })),
                )
              : h('span', { class: 'size-7 inline-flex' }),
            h(iconByLevel[row.original.level], { class: 'size-4 text-muted-foreground' }),
          ]),
          h('div', { class: 'flex items-center gap-2' }, [
            h('span', { class: 'font-medium' }, row.original.name),
            row.original.level === 'type'
              ? h(Badge, { variant: 'outline' }, () => [h(FileText), row.original.categoryLabel])
              : null,
          ]),
        ],
      );
    },
  },
  {
    id: 'level',
    header: 'Nivel',
    cell: ({ row }) => {
      const labels: Record<TableLevel, string> = {
        type: 'Tipo',
        brand: 'Marca',
        model: 'Modelo',
      };

      return h(Badge, { variant: 'secondary' }, () => labels[row.original.level]);
    },
  },
  {
    accessorKey: 'relatedCount',
    header: 'Relacionados',
    cell: ({ row }) => {
      const labelByLevel: Record<TableLevel, string> = {
        type: row.original.relatedCount === 1 ? 'marca' : 'marcas',
        brand: row.original.relatedCount === 1 ? 'modelo' : 'modelos',
        model: row.original.relatedCount === 1 ? 'marca' : 'marcas',
      };

      return h('span', { class: 'text-sm text-muted-foreground' }, `${row.original.relatedCount} ${labelByLevel[row.original.level]}`);
    },
  },
  {
    accessorKey: 'updatedAt',
    header: 'Actualizado',
    cell: ({ row }) => h('span', { class: 'text-sm text-muted-foreground' }, format(safeDate(row.original.updatedAt), 'dd/MM/yyyy HH:mm')),
  },
  {
    id: 'actions',
    header: '',
    cell: ({ row }) => {
      const level = row.original.level;
      if (level === 'type' && isFromRRHH.value) {
        return null;
      }

      return h('div', { class: 'flex justify-end gap-2' }, [
        h(
          Button,
          {
            variant: 'outline',
            size: 'icon-sm',
            onClick: () => {
              if (level === 'type') {
                emit('edit', row.original.typeRef ?? null);
              } else if (level === 'brand') {
                emit('edit-brand', brands.value.find(brand => brand.id === row.original.entityId) ?? null);
              } else if (level === 'model') {
                emit('edit-model', models.value.find(model => model.id === row.original.entityId) ?? null);
              }
            }
          },
          () => h(Pencil),
        ),
        row.original?.typeRef?.is_deletable || level !== 'type'
          ? h(
              Button,
              {
                variant: 'destructive',
                size: 'icon-sm',
                onClick: () => {
                  if (level === 'type') {
                    emit('delete', row.original.typeRef as AssetType);
                  } else if (level === 'brand') {
                    const brandToDelete = brands.value.find(brand => brand.id === row.original.entityId);
                    if (brandToDelete) {
                      emit('delete-brand', brandToDelete);
                    }
                  } else if (level === 'model') {
                    const modelToDelete = models.value.find(model => model.id === row.original.entityId);
                    if (modelToDelete) {
                      emit('delete-model', modelToDelete);
                    }
                  }
                }
              },
              () => h(Trash),
            )
          : null,
      ]);
    },
  },
];

const table = useVueTable({
  get data() {
    return rows.value;
  },
  columns,
  getSubRows: row => row.children,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onGlobalFilterChange: updaterOrValue => valueUpdater(updaterOrValue, search),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  filterFromLeafRows: true,
  state: {
    get globalFilter() {
      return search.value;
    },
    get expanded() {
      return expanded.value;
    },
  },
});
</script>
