export interface RRHHStat {
  label: string;
  value: number;
}

export interface RRHHChartDatum {
  key: string;
  value: number;
  color: string;
}

export interface RRHHTrendDatum {
  period: string;
  celulares: number;
  cargadores: number;
}

export interface RRHHAssetMock {
  id: number;
  name: string;
  brand: string;
  model: string;
  type: 'Celular' | 'Cargador de Celular';
  status: 'Disponible' | 'Asignado' | 'Mantenimiento';
}

export const rrhhStatsMock: RRHHStat[] = [
  { label: 'Total activos RRHH', value: 18 },
  { label: 'Celulares', value: 8 },
  { label: 'Cargadores de Celular', value: 10 },
];

export const rrhhTypeDistributionMock: RRHHChartDatum[] = [
  { key: 'Celular', value: 8, color: 'hsl(224 76% 48%)' },
  { key: 'Cargador de Celular', value: 10, color: 'hsl(38 92% 50%)' },
];

export const rrhhTrendMock: RRHHTrendDatum[] = [
  { period: 'Lun', celulares: 6, cargadores: 8 },
  { period: 'Mar', celulares: 7, cargadores: 8 },
  { period: 'Mié', celulares: 8, cargadores: 9 },
  { period: 'Jue', celulares: 8, cargadores: 9 },
  { period: 'Vie', celulares: 8, cargadores: 10 },
];

export const rrhhAssetsMock: RRHHAssetMock[] = [
  { id: 1, name: 'Samsung A15', brand: 'Samsung', model: 'SM-A155M', type: 'Celular', status: 'Asignado' },
  { id: 2, name: 'iPhone 13', brand: 'Apple', model: 'A2633', type: 'Celular', status: 'Disponible' },
  { id: 3, name: 'Moto G54', brand: 'Motorola', model: 'XT2343', type: 'Celular', status: 'Mantenimiento' },
  { id: 4, name: 'Cargador 20W', brand: 'Apple', model: 'A2305', type: 'Cargador de Celular', status: 'Disponible' },
  { id: 5, name: 'Cargador USB-C', brand: 'Anker', model: 'PowerPort', type: 'Cargador de Celular', status: 'Asignado' },
  { id: 6, name: 'Cargador 25W', brand: 'Samsung', model: 'EP-TA800', type: 'Cargador de Celular', status: 'Disponible' },
];
