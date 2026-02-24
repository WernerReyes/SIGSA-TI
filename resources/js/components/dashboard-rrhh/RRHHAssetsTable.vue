<template>
  <Card class="border-border/80">
    <CardHeader>
      <CardTitle>Activos recientes</CardTitle>
    </CardHeader>
    <CardContent>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Nombre</TableHead>
            <TableHead>Marca</TableHead>
            <TableHead>Modelo</TableHead>
            <TableHead>Tipo</TableHead>
            <TableHead>Estado</TableHead>
            <TableHead>Creado</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="!recentAssets.length">
                        <TableCell colspan="6" class="text-center text-sm text-muted-foreground">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <TabletSmartphone />
                                    </EmptyMedia>
                                    <EmptyTitle>No hay activos recientes</EmptyTitle>
                                    <EmptyDescription>
                                        No se han reportado nuevos activos esta semana.
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button as="a" href="/assets">
                                        Crear activo
                                    </Button>
                                </EmptyContent>

                            </Empty>
                        </TableCell>
                    </TableRow>
          <TableRow v-else v-for="asset in recentAssets" :key="asset.id">
            <TableCell>{{ asset.name }}</TableCell>
            <TableCell>{{ asset.brand?.name }}</TableCell>
            <TableCell>
              <template v-if="asset.model">{{ asset.model?.name  }}</template>
              <Badge v-else variant="secondary">N/A</Badge>

            </TableCell>
            <TableCell class="flex items-center">
              <component :is="assetTypeUI.type.icon(asset.type?.name)" class="w-4 h-4 mr-1" />
              {{ asset.type?.name
              }}
            </TableCell>
            <TableCell>

              <Badge :class="statusOp(asset.status).bg">
                <component :is="statusOp(asset.status).icon" class="w-4 h-4 mr-1" />
                {{ statusOp(asset.status).label }}


              </Badge>
            </TableCell>
            <TableCell> {{ formatDistanceToNow(asset.created_at, { addSuffix: true, locale: es }) }}</TableCell>

          </TableRow>
        </TableBody>
      </Table>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { rrhhAssetsMock } from './mock';
import { type Asset, statusOp } from '@/interfaces/asset.interface';
import { assetTypeUI } from '@/interfaces/assetType.interface';
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, EmptyContent } from '@/components/ui/empty';
import { TabletSmartphone } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

defineProps<{
  recentAssets: Asset[];
}>();
</script>
