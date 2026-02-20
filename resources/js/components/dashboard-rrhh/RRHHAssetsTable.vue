<template>
  <Card class="border-border/80">
    <CardHeader>
      <CardTitle>Listado de activos RRHH</CardTitle>
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
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="asset in rrhhAssetsMock" :key="asset.id">
            <TableCell>{{ asset.name }}</TableCell>
            <TableCell>{{ asset.brand }}</TableCell>
            <TableCell>{{ asset.model }}</TableCell>
            <TableCell>{{ asset.type }}</TableCell>
            <TableCell>
              <Badge :variant="asset.status === 'Disponible' ? 'secondary' : asset.status === 'Asignado' ? 'default' : 'destructive'">
                {{ asset.status }}
              </Badge>
            </TableCell>
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
</script>
