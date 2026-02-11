<script setup>
import { ref, computed } from 'vue'

const step = ref(1)

const form = ref({
  type: null,
  title: '',
  description: '',
  service_id: null,
  category_id: null,
  impact_level: null,
  urgency_level: null,
  is_service_down: false,
  affected_since: null,
  request_type: null,
  required_date: null,
})
</script>

<template>
  <!-- PASO 1 -->
  <div v-if="step === 1" class="grid grid-cols-2 gap-4">
    <button class="card" @click="form.type='INCIDENT'; step=2">
      🔴 Reportar un problema
    </button>

    <button class="card" @click="form.type='SERVICE_REQUEST'; step=2">
      🔵 Solicitar algo
    </button>
  </div>

  <!-- PASO 2 -->
  <div v-if="step === 2" class="space-y-6">

    <input v-model="form.title" placeholder="Título del ticket" />
    <textarea v-model="form.description" placeholder="Describe el detalle" />

    <!-- Impacto -->
    <div>
      <h4>¿A cuántas personas afecta?</h4>
      <label><input type="radio" value="LOW" v-model="form.impact_level" /> Solo a mí</label>
      <label><input type="radio" value="MEDIUM" v-model="form.impact_level" /> Mi equipo</label>
      <label><input type="radio" value="HIGH" v-model="form.impact_level" /> Toda el área/empresa</label>
    </div>

    <!-- Urgencia -->
    <div>
      <h4>¿Qué tan urgente es?</h4>
      <label><input type="radio" value="LOW" v-model="form.urgency_level" /> No urgente</label>
      <label><input type="radio" value="MEDIUM" v-model="form.urgency_level" /> Necesito solución hoy</label>
      <label><input type="radio" value="HIGH" v-model="form.urgency_level" /> Bloquea mi trabajo</label>
    </div>

    <!-- INCIDENT -->
    <div v-if="form.type === 'INCIDENT'">
      <label>
        <input type="checkbox" v-model="form.is_service_down" />
        El servicio está completamente caído
      </label>
    </div>

    <!-- SERVICE REQUEST -->
    <div v-if="form.type === 'SERVICE_REQUEST'">
      <select v-model="form.request_type">
        <option value="ACCESS">Acceso</option>
        <option value="SOFTWARE">Software</option>
        <option value="EQUIPMENT">Equipo</option>
      </select>
    </div>

  </div>
</template>
