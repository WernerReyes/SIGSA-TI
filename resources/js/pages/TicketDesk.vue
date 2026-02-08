<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Alert } from '@/components/ui/alert';
import { CheckCircle, Clock, ShieldAlert, ShieldCheck, TriangleAlert, User, Users, BarChart3, Timer, HardDrive, MonitorSmartphone, FileText, Wrench, BookOpen, ArrowUpRight, History, BadgeCheck, Briefcase, Building2, UserCog, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const metrics = [
  { label: 'Tickets abiertos', value: '128', icon: Clock, tone: 'bg-blue-500/10 text-blue-600' },
  { label: 'SLA violado', value: '14', icon: TriangleAlert, tone: 'bg-red-500/10 text-red-600' },
  { label: 'Tiempo medio de resolución', value: '3h 24m', icon: Timer, tone: 'bg-amber-500/10 text-amber-600' },
  { label: 'Escalados', value: '9', icon: ArrowUpRight, tone: 'bg-purple-500/10 text-purple-600' },
];

const tickets = [
  {
    id: 'TK-1023',
    title: 'Solicitud de equipo para nuevo colaborador',
    requester: 'María Rojas',
    area: 'Operations',
    category: 'Hardware',
    priority: 'Crítica',
    slaHours: 4.5,
    status: 'Nuevo',
    tech: 'Luis Torres',
    created: '2026-02-06 09:41'
  },
  {
    id: 'TK-1018',
    title: 'Acceso VPN para supervisor de almacén',
    requester: 'Carlos Pineda',
    area: 'Warehouse',
    category: 'Red',
    priority: 'Alta',
    slaHours: 2.1,
    status: 'En progreso',
    tech: 'Rosa Medina',
    created: '2026-02-06 08:15'
  },
  {
    id: 'TK-1012',
    title: 'Pantalla azul en laptop de Finanzas',
    requester: 'Ana Ruiz',
    area: 'Management',
    category: 'Software',
    priority: 'Media',
    slaHours: 0,
    status: 'Escalado',
    tech: 'Marco Rivas',
    created: '2026-02-05 17:02'
  },
];

const role = ref<'user' | 'technician' | 'admin'>('technician');

const userTickets = ref([
  { id: 'TK-2001', title: 'Acceso a CRM', status: 'En Progreso', priority: 'Media', created: '2026-02-06', tech: 'Rosa Medina' },
  { id: 'TK-2002', title: 'Laptop lenta', status: 'Resuelto', priority: 'Alta', created: '2026-02-05', tech: 'Luis Torres' },
]);

const histories = ref([
  { icon: History, title: 'Ticket creado', meta: '2026-02-06 09:41 · María Rojas', tone: 'bg-blue-500/10 text-blue-600' },
  { icon: UserCog, title: 'Técnico asignado', meta: '2026-02-06 09:52 · Luis Torres', tone: 'bg-purple-500/10 text-purple-600' },
  { icon: User, title: 'Comentario del usuario', meta: '2026-02-06 10:01 · Se adjunta evidencia del incidente', tone: 'bg-slate-500/10 text-slate-600' },
  { icon: Wrench, title: 'Comentario del técnico', meta: '2026-02-06 10:05 · Se inicia diagnóstico', tone: 'bg-amber-500/10 text-amber-600' },
  { icon: HardDrive, title: 'Equipo asignado', meta: '2026-02-06 10:14 · Dell Latitude 5520', tone: 'bg-emerald-500/10 text-emerald-600' },
  { icon: Wrench, title: 'Estado cambiado a En progreso', meta: '2026-02-06 10:20 · Luis Torres', tone: 'bg-amber-500/10 text-amber-600' },
]);

const assets = [
  { name: 'Laptop Dell XPS', type: 'Laptop', serial: 'DLX-3920', status: 'Active' },
  { name: 'Monitor Samsung 24"', type: 'Monitor', serial: 'SM-24-991', status: 'Active' },
  { name: 'Corporate Phone iPhone', type: 'Phone', serial: 'IP-14-002', status: 'Active' },
  { name: 'Mouse Logitech', type: 'Accessory', serial: 'LG-M-117', status: 'Active' },
];

const ticketAssets = ref([
  { name: 'Laptop Dell Latitude 5520', by: 'Luis Torres', date: '2026-02-06 10:14', action: 'Change' },
  { name: 'Headset Plantronics', by: 'Luis Torres', date: '2026-02-06 10:14', action: 'Return' },
]);

const kbItems = [
  { title: 'VPN Access - Standard Steps', type: 'Known Error' },
  { title: 'Laptop Provisioning Checklist', type: 'Quick Fix' },
  { title: 'Asset Assignment Best Practices', type: 'Documentation' },
];

const openCreateTicket = ref(false);
const openAssignAsset = ref(false);
const openAssignSelf = ref(false);
const openHoldUser = ref(false);
const openHoldProvider = ref(false);
const openReassign = ref(false);
const openEscalate = ref(false);
const openResolve = ref(false);
const openClose = ref(false);
const openUserConfirm = ref(false);
const openLinkAsset = ref(false);

const currentStatus = ref('Abierto');
const currentPriority = ref('Crítica');
const currentCategory = ref('Hardware');
const currentDepartment = ref('Operations');
const currentRequester = ref('María Rojas');
const currentSlaTotalHours = ref(8);
const currentSlaRemainingHours = ref(4.5);
const slaExpired = ref(false);
const userConfirmed = ref(false);
const currentClassification = ref('Incidente');

const pushHistory = (title: string, meta: string, tone: string, icon: any) => {
  histories.value.unshift({ title, meta, tone, icon });
};

const handleCreateTicket = () => {
  currentStatus.value = 'Abierto';
  currentPriority.value = 'Media';
  currentCategory.value = 'Software';
  currentClassification.value = 'Requerimiento de Servicio';
  currentSlaTotalHours.value = 8;
  currentSlaRemainingHours.value = 8;
  slaExpired.value = false;
  pushHistory('Ticket creado', 'Ahora · Sistema', 'bg-blue-500/10 text-blue-600', History);
  openCreateTicket.value = false;
};

const handleStartWork = () => {
  currentStatus.value = 'En progreso';
  pushHistory('Estado cambiado a En progreso', 'Ahora · Técnico', 'bg-amber-500/10 text-amber-600', Wrench);
};

const handleAssignAsset = () => {
  ticketAssets.value.unshift({
    name: 'Laptop Dell Latitude 5520',
    by: 'Luis Torres',
    date: 'Ahora',
    action: 'Assign'
  });
  if (currentStatus.value === 'Abierto') {
    currentStatus.value = 'Asignado';
  }
  pushHistory('Equipo asignado', 'Ahora · Dell Latitude 5520', 'bg-emerald-500/10 text-emerald-600', HardDrive);
  openAssignAsset.value = false;
};

const handleAssignSelf = () => {
  currentStatus.value = 'En progreso';
  pushHistory('Técnico asignado', 'Ahora · Luis Torres', 'bg-purple-500/10 text-purple-600', UserCog);
  openAssignSelf.value = false;
};

const handleHoldUser = () => {
  currentStatus.value = 'Esperando Usuario';
  pushHistory('Esperando usuario', 'Ahora · Se solicitó información', 'bg-amber-500/10 text-amber-600', TriangleAlert);
  openHoldUser.value = false;
};

const handleHoldProvider = () => {
  currentStatus.value = 'Esperando Proveedor';
  pushHistory('Esperando proveedor', 'Ahora · Proveedor externo', 'bg-amber-500/10 text-amber-600', TriangleAlert);
  openHoldProvider.value = false;
};

const handleReassign = () => {
  pushHistory('Técnico reasignado', 'Ahora · Nuevo técnico asignado', 'bg-purple-500/10 text-purple-600', UserCog);
  openReassign.value = false;
};

const handleLinkAsset = () => {
  pushHistory('Activo vinculado', 'Ahora · Laptop/PC vinculada', 'bg-emerald-500/10 text-emerald-600', HardDrive);
  openLinkAsset.value = false;
};

const handleUserReply = () => {
  if (currentStatus.value === 'Esperando Usuario') {
    currentStatus.value = 'En progreso';
  }
  pushHistory('Comentario del usuario', 'Ahora · Respuesta del usuario', 'bg-slate-500/10 text-slate-600', User);
};

const handleUserConfirmClose = () => {
  userConfirmed.value = true;
  currentStatus.value = 'Cerrado';
  pushHistory('Ticket cerrado por usuario', 'Ahora · Confirmación de usuario', 'bg-slate-500/10 text-slate-600', BadgeCheck);
  openUserConfirm.value = false;
};

const handleUserReopen = () => {
  currentStatus.value = 'En progreso';
  pushHistory('Ticket reabierto', 'Ahora · Usuario no confirmó', 'bg-amber-500/10 text-amber-600', TriangleAlert);
  openUserConfirm.value = false;
};

const handleEscalate = () => {
  currentStatus.value = 'Escalado';
  pushHistory('Ticket escalado', 'Ahora · Supervisor', 'bg-purple-500/10 text-purple-600', ShieldAlert);
  openEscalate.value = false;
};

const handleResolve = () => {
  currentStatus.value = 'Resuelto';
  pushHistory('Ticket resuelto', 'Ahora · Técnico', 'bg-emerald-500/10 text-emerald-600', CheckCircle);
  openResolve.value = false;
};

const handleClose = () => {
  currentStatus.value = 'Cerrado';
  pushHistory('Ticket cerrado', 'Ahora · Sistema', 'bg-slate-500/10 text-slate-600', BadgeCheck);
  openClose.value = false;
};

const slaProgress = computed(() => {
  const total = currentSlaTotalHours.value || 1;
  const remaining = currentSlaRemainingHours.value;
  const used = Math.max(0, total - remaining);
  return Math.min(100, Math.round((used / total) * 100));
});

const slaStatusLabel = computed(() => {
  if (slaExpired.value || currentSlaRemainingHours.value <= 0) return 'Violado';
  if (currentSlaRemainingHours.value <= 2) return 'En riesgo';
  return 'Dentro del SLA';
});

const slaStatusClass = computed(() => {
  if (slaStatusLabel.value === 'Violado') return 'bg-red-500/20 text-red-600 border-red-300';
  if (slaStatusLabel.value === 'En riesgo') return 'bg-amber-500/20 text-amber-600 border-amber-300';
  return 'bg-emerald-500/20 text-emerald-600 border-emerald-300';
});

const steps = ['Abierto', 'Asignado', 'En progreso', 'Escalado', 'Resuelto', 'Cerrado'];
const currentStepIndex = computed(() => {
  const idx = steps.indexOf(currentStatus.value);
  return idx === -1 ? 0 : idx;
});

const canClose = computed(() => {
  return currentStatus.value === 'Resuelto' && (userConfirmed.value || slaExpired.value);
});
</script>

<template>
  <Head title="IT Service Desk" />

  <AppLayout :breadcrumbs="[{ title: 'Service Desk', href: '#' }]">
    <div class="space-y-6">
      <Card class="shadow-sm">
        <CardContent class="flex flex-wrap items-center gap-2 py-3">
          <span class="text-xs text-muted-foreground">Rol:</span>
          <Button size="sm" :variant="role === 'user' ? 'default' : 'outline'" @click="role = 'user'">Usuario final</Button>
          <Button size="sm" :variant="role === 'technician' ? 'default' : 'outline'" @click="role = 'technician'">Técnico TI</Button>
          <Button size="sm" :variant="role === 'admin' ? 'default' : 'outline'" @click="role = 'admin'">Admin TI</Button>
        </CardContent>
      </Card>

      <!-- Dashboard Usuario -->
      <section v-if="role === 'user'" class="space-y-4">
        <Card class="shadow-sm">
          <CardContent class="flex flex-wrap items-center justify-between gap-3 py-6">
            <div>
              <CardTitle class="text-lg">Portal de Mesa de Ayuda</CardTitle>
              <p class="text-xs text-muted-foreground">Crea y gestiona tus tickets</p>
            </div>
            <Button size="lg" class="gap-2" @click="openCreateTicket = true">
              <Plus class="size-4" />
              Crear Ticket
            </Button>
          </CardContent>
        </Card>

        <Card class="shadow-sm">
          <CardHeader>
            <CardTitle class="text-sm">Mis Tickets</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="overflow-hidden rounded-xl border">
              <div class="grid grid-cols-[110px_2fr_120px_120px_140px_140px_100px] bg-muted/60 text-xs font-semibold text-muted-foreground">
                <div class="px-3 py-2">ID</div>
                <div class="px-3 py-2">Título</div>
                <div class="px-3 py-2">Estado</div>
                <div class="px-3 py-2">Prioridad</div>
                <div class="px-3 py-2">Fecha</div>
                <div class="px-3 py-2">Técnico</div>
                <div class="px-3 py-2">Acción</div>
              </div>
              <div class="divide-y">
                <div v-for="ticket in userTickets" :key="ticket.id" class="grid grid-cols-[110px_2fr_120px_120px_140px_140px_100px] items-center text-xs">
                  <div class="px-3 py-3 font-semibold">{{ ticket.id }}</div>
                  <div class="px-3 py-3">
                    <div class="font-medium">{{ ticket.title }}</div>
                  </div>
                  <div class="px-3 py-3">
                    <Badge variant="secondary" class="text-[11px]">{{ ticket.status }}</Badge>
                  </div>
                  <div class="px-3 py-3">
                    <Badge class="text-[11px]">{{ ticket.priority }}</Badge>
                  </div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.created }}</div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.tech }}</div>
                  <div class="px-3 py-3">
                    <Button size="sm" variant="outline" @click="openUserConfirm = true">Ver</Button>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="shadow-sm">
          <CardHeader>
            <CardTitle class="text-sm">Conversación del ticket</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <div class="space-y-2">
              <div class="rounded-lg border p-3 text-xs">Usuario: ¿Pueden ayudarme con el acceso al CRM?</div>
              <div class="rounded-lg border p-3 text-xs">Técnico: Revisando permisos, por favor espere.</div>
            </div>
            <div class="flex gap-2">
              <Input placeholder="Escribe tu respuesta..." />
              <Button @click="handleUserReply">Responder</Button>
              <Button variant="outline">Adjuntar</Button>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Dashboard Técnico / Admin -->
      <section v-else class="space-y-4">
        <template v-if="role === 'technician'">
      <!-- Ticket list dashboard -->
      <section class="space-y-4">
        <div class="grid gap-4 md:grid-cols-4">
          <Card v-for="metric in metrics" :key="metric.label" class="shadow-sm">
            <CardHeader class="flex flex-row items-center justify-between pb-2">
              <CardTitle class="text-sm font-medium text-muted-foreground">{{ metric.label }}</CardTitle>
              <div :class="['size-8 rounded-lg flex items-center justify-center', metric.tone]">
                <component :is="metric.icon" class="size-4" />
              </div>
            </CardHeader>
            <CardContent>
              <div class="text-2xl font-semibold">{{ metric.value }}</div>
            </CardContent>
          </Card>
        </div>

        <Card class="shadow-sm">
          <CardHeader class="flex flex-wrap items-center gap-3">
            <CardTitle class="text-lg">Dashboard de Tickets</CardTitle>
            <div class="ml-auto flex flex-wrap gap-2">
              <Input class="w-56" placeholder="Búsqueda avanzada..." />
              <Select class="w-40" placeholder="Estado" />
              <Select class="w-40" placeholder="Prioridad" />
              <Select class="w-40" placeholder="Técnico" />
              <Select class="w-40" placeholder="Categoría" />
              <Button size="sm" class="gap-2" @click="openCreateTicket = true">
                <Plus class="size-4" />
                Crear ticket
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div class="overflow-hidden rounded-xl border">
              <div class="grid grid-cols-[110px_2fr_1fr_1fr_1fr_120px_140px_140px_140px_160px] bg-muted/60 text-xs font-semibold text-muted-foreground">
                <div class="px-3 py-2">Ticket</div>
                <div class="px-3 py-2">Título</div>
                <div class="px-3 py-2">Solicitante</div>
                <div class="px-3 py-2">Área</div>
                <div class="px-3 py-2">Categoría</div>
                <div class="px-3 py-2">Prioridad</div>
                <div class="px-3 py-2">Estado</div>
                <div class="px-3 py-2">SLA restante</div>
                <div class="px-3 py-2">Técnico</div>
                <div class="px-3 py-2">Creado</div>
              </div>
              <div class="divide-y">
                <div v-for="ticket in tickets" :key="ticket.id"
                  class="grid grid-cols-[110px_2fr_1fr_1fr_1fr_120px_140px_140px_140px_160px] items-center text-xs">
                  <div class="px-3 py-3 font-semibold">{{ ticket.id }}</div>
                  <div class="px-3 py-3">
                    <div class="font-medium">{{ ticket.title }}</div>
                  </div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.requester }}</div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.area }}</div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.category }}</div>
                  <div class="px-3 py-3">
                    <Badge :class="ticket.priority === 'Crítica'
                      ? 'bg-red-500/20 text-red-600 border-red-300'
                      : ticket.priority === 'Alta'
                        ? 'bg-orange-500/20 text-orange-600 border-orange-300'
                        : ticket.priority === 'Media'
                          ? 'bg-yellow-500/20 text-yellow-700 border-yellow-300'
                          : 'bg-blue-500/20 text-blue-600 border-blue-300'" class="text-[11px]">
                      {{ ticket.priority }}
                    </Badge>
                  </div>
                  <div class="px-3 py-3">
                    <Badge variant="secondary" class="text-[11px]">{{ ticket.status }}</Badge>
                  </div>
                  <div class="px-3 py-3">
                    <Badge :class="ticket.slaHours <= 0 ? 'bg-red-500/20 text-red-600 border-red-300' : ticket.slaHours <= 2 ? 'bg-amber-500/20 text-amber-600 border-amber-300' : 'bg-emerald-500/20 text-emerald-600 border-emerald-300'" class="text-[11px]">
                      {{ ticket.slaHours }}h
                    </Badge>
                  </div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.tech }}</div>
                  <div class="px-3 py-3 text-muted-foreground">{{ ticket.created }}</div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Ticket detail page -->
      <section class="space-y-6">
        <Card class="shadow-sm">
          <CardHeader class="space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <Badge variant="outline">TK-1023</Badge>
                  <Badge class="bg-red-500/20 text-red-600 border-red-300">{{ currentPriority }}</Badge>
                  <Badge variant="secondary">{{ currentStatus }}</Badge>
                  <Badge :class="slaStatusClass" class="text-[11px]">{{ slaStatusLabel }}</Badge>
                </div>
                <CardTitle class="text-xl">Solicitud de equipo para nuevo colaborador</CardTitle>
                <div class="text-xs text-muted-foreground">{{ currentRequester }} · {{ currentDepartment }} · Jefe de Turno</div>
              </div>
              <div class="flex flex-wrap gap-2">
                <Button size="sm" @click="handleStartWork">Iniciar trabajo</Button>
                <Button variant="outline" size="sm" @click="openEscalate = true">Escalar</Button>
                <Button variant="outline" size="sm" @click="openResolve = true">Resolver</Button>
                <Button variant="outline" size="sm" :disabled="!canClose" @click="openClose = true">Cerrar</Button>
              </div>
            </div>

            <!-- Stepper -->
            <div class="rounded-xl border bg-muted/30 p-3">
              <div class="grid grid-cols-6 gap-2 text-[11px] font-semibold text-muted-foreground">
                <div v-for="(step, idx) in steps" :key="step" class="flex items-center gap-2">
                  <div class="size-2 rounded-full" :class="idx <= currentStepIndex ? 'bg-primary' : 'bg-muted'" />
                  <span :class="idx <= currentStepIndex ? 'text-foreground' : 'text-muted-foreground'">{{ step }}</span>
                </div>
              </div>
            </div>

            <!-- SLA panel -->
            <div class="grid gap-3 md:grid-cols-3">
              <div class="rounded-xl border bg-muted/40 p-3">
                <div class="text-xs text-muted-foreground">SLA total</div>
                <div class="text-sm font-semibold">{{ currentSlaTotalHours }}h</div>
              </div>
              <div class="rounded-xl border bg-muted/40 p-3">
                <div class="text-xs text-muted-foreground">SLA restante</div>
                <div class="text-sm font-semibold">{{ currentSlaRemainingHours }}h</div>
              </div>
              <div class="rounded-xl border bg-muted/40 p-3">
                <div class="text-xs text-muted-foreground">Estado SLA</div>
                <Badge :class="slaStatusClass" class="text-[11px]">{{ slaStatusLabel }}</Badge>
              </div>
              <div class="col-span-full text-xs text-muted-foreground">
                Tiempo excedido: {{ currentSlaRemainingHours <= 0 ? Math.abs(currentSlaRemainingHours).toFixed(1) : '0.0' }}h
              </div>
              <div class="col-span-full">
                <Progress :model-value="slaProgress" />
              </div>
            </div>
          </CardHeader>
        </Card>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_1.4fr_0.8fr]">
          <!-- Left panel (ticket info) -->
          <Card class="shadow-sm">
            <CardHeader>
              <CardTitle class="text-sm">Información del ticket</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4 text-sm">
              <div class="grid gap-3 md:grid-cols-2">
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">ID</div>
                  <div class="font-semibold">TK-1023</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Título</div>
                  <div class="font-semibold">Solicitud de equipo para nuevo colaborador</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Usuario</div>
                  <div class="font-semibold">{{ currentRequester }}</div>
                  <div class="text-xs text-muted-foreground">{{ currentDepartment }}</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Categoría</div>
                  <div class="font-semibold">{{ currentCategory }}</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Prioridad</div>
                  <div class="font-semibold">{{ currentPriority }}</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Estado</div>
                  <div class="font-semibold">{{ currentStatus }}</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">SLA Response Due</div>
                  <div class="font-semibold">00:10:23</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">SLA Resolution Due</div>
                  <div class="font-semibold">03:12:55</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Fecha de creación</div>
                  <div class="font-semibold">2026-02-06 09:41</div>
                </div>
                <div class="rounded-lg border p-3">
                  <div class="text-xs text-muted-foreground">Última actualización</div>
                  <div class="font-semibold">2026-02-06 10:20</div>
                </div>
              </div>

              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Descripción</CardTitle>
                </CardHeader>
                <CardContent class="text-sm text-muted-foreground">
                  El área solicita el equipamiento completo para un nuevo colaborador (laptop, monitor, teléfono y accesorios).
                </CardContent>
              </Card>

              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Clasificación ITIL</CardTitle>
                </CardHeader>
                <CardContent class="text-sm text-muted-foreground space-y-1">
                  <div>Tipo: {{ currentClassification }}</div>
                  <div>Solicitud: Equipo</div>
                  <div>Grupo técnico: Systems & IT</div>
                  <div>Activo asignado: Laptop HP ProBook</div>
                </CardContent>
              </Card>
            </CardContent>
          </Card>

          <!-- Center panel (chat) -->
          <Card class="shadow-sm">
            <CardHeader>
              <CardTitle class="text-sm">Conversación / Historial</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
              <ScrollArea class="h-[420px] pr-2">
                <div class="space-y-3">
                  <div v-for="entry in histories" :key="entry.title" class="flex gap-3">
                    <div :class="['size-8 rounded-full flex items-center justify-center', entry.tone]">
                      <component :is="entry.icon" class="size-4" />
                    </div>
                    <div class="flex-1 rounded-lg border p-3">
                      <div class="text-sm font-semibold">{{ entry.title }}</div>
                      <div class="text-xs text-muted-foreground">{{ entry.meta }}</div>
                    </div>
                  </div>
                </div>
              </ScrollArea>
              <div class="flex gap-2">
                <Input placeholder="Responder al usuario..." />
                <Button @click="handleUserReply">Enviar</Button>
                <Button variant="outline">Adjuntar</Button>
              </div>
            </CardContent>
          </Card>

          <!-- Right panel (actions) -->
          <Card class="shadow-sm">
            <CardHeader>
              <CardTitle class="text-sm">Acciones del técnico</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
              <div class="grid gap-2">
                <Button class="justify-start gap-2" @click="openAssignSelf = true">
                  <UserCog class="size-4" />
                  Asignarme
                </Button>
                <Button class="justify-start gap-2" @click="openAssignAsset = true">
                  <MonitorSmartphone class="size-4" />
                  Asignar equipo
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openHoldUser = true">
                  <TriangleAlert class="size-4" />
                  Esperando usuario
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openHoldProvider = true">
                  <TriangleAlert class="size-4" />
                  Esperando proveedor
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openReassign = true">
                  <UserCog class="size-4" />
                  Reasignar técnico
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openEscalate = true">
                  <TriangleAlert class="size-4" />
                  Escalar ticket
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openLinkAsset = true">
                  <HardDrive class="size-4" />
                  Vincular activo
                </Button>
                <Button variant="outline" class="justify-start gap-2" @click="openResolve = true">
                  <CheckCircle class="size-4" />
                  Resolver ticket
                </Button>
                <Button variant="outline" class="justify-start gap-2" :disabled="!canClose" @click="openClose = true">
                  <BadgeCheck class="size-4" />
                  Cerrar ticket
                </Button>
              </div>
              <div class="rounded-lg border p-3 text-xs text-muted-foreground">
                Cierre disponible solo si está resuelto y el usuario confirmó o el SLA expiró.
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Assets + SLA + KB Tabs -->
        <Card class="shadow-sm">
          <CardHeader>
            <CardTitle class="text-sm">Panel de trabajo</CardTitle>
          </CardHeader>
          <CardContent>
            <Tabs default-value="assets" class="w-full">
              <TabsList class="grid w-full grid-cols-4">
                <TabsTrigger value="assets">Activos</TabsTrigger>
                <TabsTrigger value="sla">SLA</TabsTrigger>
                <TabsTrigger value="history">Historial</TabsTrigger>
                <TabsTrigger value="kb">Base de conocimiento</TabsTrigger>
              </TabsList>

              <div class="mt-4 space-y-4">
                <div>
                  <Card class="shadow-none border">
                    <CardHeader class="flex items-center justify-between">
                      <CardTitle class="text-sm">Activos asignados al usuario</CardTitle>
                      <Badge variant="secondary" class="text-xs">Asignación activa</Badge>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                      <div class="grid grid-cols-[2fr_1fr_1fr_1fr] rounded-lg border bg-muted/40 text-[11px] font-semibold text-muted-foreground">
                        <div class="px-3 py-2">Activo</div>
                        <div class="px-3 py-2">Tipo</div>
                        <div class="px-3 py-2">Serie</div>
                        <div class="px-3 py-2">Estado</div>
                      </div>
                      <div class="divide-y rounded-lg border">
                        <div v-for="asset in assets" :key="asset.serial" class="grid grid-cols-[2fr_1fr_1fr_1fr] text-xs">
                          <div class="px-3 py-2 font-medium">{{ asset.name }}</div>
                          <div class="px-3 py-2 text-muted-foreground">{{ asset.type }}</div>
                          <div class="px-3 py-2 text-muted-foreground">{{ asset.serial }}</div>
                          <div class="px-3 py-2"><Badge variant="outline" class="text-[11px]">{{ asset.status }}</Badge></div>
                        </div>
                      </div>
                      <div class="flex flex-wrap gap-2">
                        <Button size="sm" @click="openAssignAsset = true">Asignar equipo</Button>
                        <Button variant="outline" size="sm">Reemplazar equipo</Button>
                        <Button variant="outline" size="sm">Solicitar devolución</Button>
                      </div>
                    </CardContent>
                  </Card>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <Card class="shadow-none border">
                    <CardHeader>
                      <CardTitle class="text-sm">Panel SLA</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                      <Progress :model-value="slaProgress" />
                      <div class="flex items-center gap-2 text-xs">
                        <ShieldCheck class="size-4 text-green-600" />
                        Estado: {{ slaStatusLabel }} · Restante {{ currentSlaRemainingHours }}h
                      </div>
                      <div class="text-xs text-muted-foreground">Total permitido: {{ currentSlaTotalHours }}h</div>
                    </CardContent>
                  </Card>
                  <Card class="shadow-none border">
                    <CardHeader>
                      <CardTitle class="text-sm">SLA controles</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                      <Button variant="outline" size="sm">Pausar SLA</Button>
                      <Button variant="outline" size="sm">Cambiar política SLA</Button>
                    </CardContent>
                  </Card>
                </div>

                <div class="space-y-3">
                  <div v-for="entry in histories" :key="entry.title" class="flex gap-3 rounded-lg border p-3">
                    <div :class="['size-8 rounded-full flex items-center justify-center', entry.tone]">
                      <component :is="entry.icon" class="size-4" />
                    </div>
                    <div class="flex-1">
                      <div class="text-sm font-semibold">{{ entry.title }}</div>
                      <div class="text-xs text-muted-foreground">{{ entry.meta }}</div>
                    </div>
                  </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <Card class="shadow-none border">
                    <CardHeader>
                      <CardTitle class="text-sm">Soluciones sugeridas</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                      <div v-for="item in kbItems" :key="item.title" class="rounded-lg border p-2">
                        <div class="text-sm font-medium">{{ item.title }}</div>
                        <div class="text-xs text-muted-foreground">{{ item.type }}</div>
                      </div>
                    </CardContent>
                  </Card>
                  <Card class="shadow-none border">
                    <CardHeader>
                      <CardTitle class="text-sm">Crear artículo</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm text-muted-foreground">
                      Genera un artículo de base de conocimiento desde este ticket.
                      <Button class="mt-3" size="sm">
                        <BookOpen class="size-4" />
                        Crear artículo
                      </Button>
                    </CardContent>
                  </Card>
                </div>
              </div>
            </Tabs>
          </CardContent>
        </Card>

        <!-- Timeline / historial tipo chat -->
        <Card class="shadow-sm">
          <CardHeader>
            <CardTitle class="text-sm">Timeline / Historial</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <div v-for="entry in histories" :key="entry.title" class="flex gap-3">
              <div :class="['size-9 rounded-full flex items-center justify-center', entry.tone]">
                <component :is="entry.icon" class="size-4" />
              </div>
              <div class="flex-1 rounded-lg border p-3">
                <div class="text-sm font-semibold">{{ entry.title }}</div>
                <div class="text-xs text-muted-foreground">{{ entry.meta }}</div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Supervisor dashboard -->
        <Card class="shadow-sm">
          <CardHeader>
            <CardTitle class="text-lg">Supervisor Dashboard</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="rounded-xl border p-3">
              <div class="flex items-center gap-2 text-sm font-semibold">
                <BarChart3 class="size-4" /> SLA compliance %
              </div>
              <Progress class="mt-2" :model-value="86" />
              <div class="text-xs text-muted-foreground mt-1">86% on-track</div>
            </div>
            <div class="rounded-xl border p-3">
              <div class="flex items-center gap-2 text-sm font-semibold">
                <Users class="size-4" /> Escalations by Area
              </div>
              <div class="mt-2 text-xs text-muted-foreground space-y-1">
                <div>Systems & IT · 4</div>
                <div>Operations · 3</div>
                <div>Warehouse · 2</div>
              </div>
            </div>
            <div class="rounded-xl border p-3">
              <div class="flex items-center gap-2 text-sm font-semibold">
                <User class="size-4" /> Technician workload
              </div>
              <div class="mt-2 text-xs text-muted-foreground space-y-1">
                <div>Rosa Medina · 12 tickets</div>
                <div>Luis Torres · 9 tickets</div>
                <div>Marco Rivas · 7 tickets</div>
              </div>
            </div>
            <div class="rounded-xl border p-3">
              <div class="flex items-center gap-2 text-sm font-semibold">
                <Briefcase class="size-4" /> MTTR
              </div>
              <div class="text-2xl font-semibold mt-2">3h 24m</div>
            </div>
            <div class="rounded-xl border p-3">
              <div class="flex items-center gap-2 text-sm font-semibold">
                <Building2 class="size-4" /> Ticket volume per Area
              </div>
              <div class="mt-2 text-xs text-muted-foreground space-y-1">
                <div>Operations · 48</div>
                <div>Systems & IT · 35</div>
                <div>Warehouse · 22</div>
              </div>
            </div>
          </CardContent>
        </Card>
        </section>
        </template>

        <template v-else>
          <Card class="shadow-sm">
            <CardHeader>
              <CardTitle class="text-lg">Panel Admin TI</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-4 md:grid-cols-2">
              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Configuración de SLA</CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground space-y-2">
                  <div>Crítica: 2h respuesta / 6h resolución</div>
                  <div>Alta: 4h respuesta / 12h resolución</div>
                  <div>Media: 8h respuesta / 24h resolución</div>
                  <div>Baja: 16h respuesta / 48h resolución</div>
                  <Button size="sm">Editar SLA</Button>
                </CardContent>
              </Card>
              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Categorías & Flujos</CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground space-y-2">
                  <div>Hardware · Software · Red · Acceso · Otros</div>
                  <div>Flujos: Incidente / Requerimiento</div>
                  <Button size="sm">Gestionar flujos</Button>
                </CardContent>
              </Card>
              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Reportes</CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground space-y-2">
                  <div>SLA compliance · MTTR · Escalados</div>
                  <Button size="sm">Ver reportes</Button>
                </CardContent>
              </Card>
              <Card class="shadow-none border">
                <CardHeader>
                  <CardTitle class="text-sm">Inventario & Activos</CardTitle>
                </CardHeader>
                <CardContent class="text-xs text-muted-foreground space-y-2">
                  <div>Gestión de activos vinculados</div>
                  <Button size="sm">Administrar activos</Button>
                </CardContent>
              </Card>
            </CardContent>
          </Card>
        </template>
      </section>

      <!-- Create ticket modal -->
      <Dialog v-model:open="openCreateTicket">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Crear nuevo ticket</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Input placeholder="Título del ticket" />
            <Textarea placeholder="Descripción" rows="3" />
            <Select placeholder="Usuario solicitante" />
            <Select placeholder="Departamento" />
            <Select placeholder="Categoría (Hardware, Software, Red, Seguridad, Otros)" />
            <Select placeholder="Prioridad (Baja, Media, Alta, Crítica)" />
            <div class="rounded-lg border p-2 text-xs text-muted-foreground">
              Clasificación automática: Incidente / Requerimiento de Servicio.
            </div>
            <div class="rounded-lg border p-2 text-xs text-muted-foreground">
              SLA asignado automáticamente según prioridad.
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openCreateTicket = false">Cancelar</Button>
            <Button @click="handleCreateTicket">Crear ticket</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Asset assignment modal -->
      <Dialog v-model:open="openAssignAsset">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Asignar equipo</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Select placeholder="Seleccionar activo IT (PC, Laptop, Monitor, etc.)" />
            <Select placeholder="Filtrar por tipo (Laptop, Monitor, Teléfono, Accesorio)" />
            <Select placeholder="Seleccionar accesorios (hijos)" />
            <Input placeholder="Fecha de asignación" />
            <Textarea placeholder="Observaciones / Entrega" rows="3" />
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openAssignAsset = false">Cancelar</Button>
            <Button @click="handleAssignAsset">Asignar activo</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Escalation modal UI -->
      <Dialog v-model:open="openEscalate">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Escalar ticket</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Select placeholder="Escalar a (Nivel 2 / Nivel 3 / Proveedor externo)" />
            <Textarea placeholder="Motivo de escalamiento" rows="3" />
            <Select placeholder="Nuevo grupo técnico" />
            <div class="flex items-center gap-2 text-xs">
              <input type="checkbox" />
              Aumentar prioridad
            </div>
            <div class="flex items-center gap-2 text-xs">
              <input type="checkbox" />
              Notificar supervisor
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openEscalate = false">Cancelar</Button>
            <Button @click="handleEscalate">Escalar</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Resolve modal -->
      <Dialog v-model:open="openResolve">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Resolver ticket</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Textarea placeholder="Solución aplicada" rows="3" />
            <Select placeholder="Categoría de causa raíz" />
            <Select placeholder="Resumen de activos afectados" />
            <Input placeholder="Evidencia adjunta (URL o referencia)" />
            <div class="flex items-center gap-2 text-xs">
              <input type="checkbox" />
              Usuario debe confirmar
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openResolve = false">Cancelar</Button>
            <Button @click="handleResolve">Resolver ticket</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Close modal -->
      <Dialog v-model:open="openClose">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Cerrar ticket</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <div class="rounded-lg border p-3 text-xs text-muted-foreground">
              Disponible solo si el ticket está resuelto.
            </div>
            <Textarea placeholder="Comentario final" rows="3" />
            <div class="flex items-center gap-2 text-xs">
              <input type="checkbox" v-model="userConfirmed" />
              Usuario confirmó la solución
            </div>
            <div class="flex items-center gap-2 text-xs">
              <input type="checkbox" v-model="slaExpired" />
              SLA expiró
            </div>
            <div class="rounded-lg border p-3 text-xs text-muted-foreground">
              Encuesta de satisfacción (estrellas)
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openClose = false">Cancelar</Button>
            <Button :disabled="!canClose" @click="handleClose">Cerrar ticket</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Assign self modal -->
      <Dialog v-model:open="openAssignSelf">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Asignarme ticket</DialogTitle>
          </DialogHeader>
          <div class="text-xs text-muted-foreground">Confirmar asignación al técnico actual.</div>
          <DialogFooter>
            <Button variant="outline" @click="openAssignSelf = false">Cancelar</Button>
            <Button @click="handleAssignSelf">Asignarme</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Hold user modal -->
      <Dialog v-model:open="openHoldUser">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Esperando usuario</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Textarea placeholder="Mensaje requerido al usuario" rows="3" />
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openHoldUser = false">Cancelar</Button>
            <Button @click="handleHoldUser">Enviar y pausar SLA</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Hold provider modal -->
      <Dialog v-model:open="openHoldProvider">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Esperando proveedor</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Textarea placeholder="Detalle para proveedor" rows="3" />
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openHoldProvider = false">Cancelar</Button>
            <Button @click="handleHoldProvider">Enviar y pausar SLA</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Reassign modal -->
      <Dialog v-model:open="openReassign">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Reasignar técnico</DialogTitle>
          </DialogHeader>
          <Select placeholder="Seleccionar técnico" />
          <DialogFooter>
            <Button variant="outline" @click="openReassign = false">Cancelar</Button>
            <Button @click="handleReassign">Reasignar</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Link asset modal -->
      <Dialog v-model:open="openLinkAsset">
        <DialogContent class="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Vincular activo</DialogTitle>
          </DialogHeader>
          <div class="space-y-3">
            <Select placeholder="Seleccionar Laptop/PC del inventario" />
            <Input placeholder="Serial" />
            <Textarea placeholder="Observaciones" rows="3" />
          </div>
          <DialogFooter>
            <Button variant="outline" @click="openLinkAsset = false">Cancelar</Button>
            <Button @click="handleLinkAsset">Vincular</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- User confirm close modal -->
      <Dialog v-model:open="openUserConfirm">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>¿Confirma que el problema fue solucionado?</DialogTitle>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" @click="handleUserReopen">No, reabrir</Button>
            <Button @click="handleUserConfirmClose">Sí, cerrar ticket</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
     
    </div>
  </AppLayout>
</template>
