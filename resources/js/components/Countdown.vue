<template>
  <Card class="w-80 mx-auto text-center">
    <CardHeader>
      <CardTitle>{{ title }}</CardTitle>
      <CardDescription>
        {{ targetLabel }}
      </CardDescription>
    </CardHeader>

    <CardContent>
      <div class="grid grid-cols-4 gap-2 text-center">
        <div>
          <p class="text-3xl font-bold">{{ time.days }}</p>
          <span class="text-xs text-muted-foreground">Días</span>
        </div>
        <div>
          <p class="text-3xl font-bold">{{ time.hours }}</p>
          <span class="text-xs text-muted-foreground">Horas</span>
        </div>
        <div>
          <p class="text-3xl font-bold">{{ time.minutes < 10 ? '0' + time.minutes : time.minutes }}</p>
          <span class="text-xs text-muted-foreground">Min</span>
        </div>
        <div>
          <p class="text-3xl font-bold">
             {{ time.seconds < 10 ? '0' + time.seconds : time.seconds }}
            </p>
          <span class="text-xs text-muted-foreground">Seg</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle
} from '@/components/ui/card';
import { onMounted, onUnmounted, ref } from 'vue';

import { addMinutes } from 'date-fns';



const emit = defineEmits<{
  (e: 'timeout'): void;
}>()


const { targetDate, targetLabel, title,  duration } = defineProps<{
  targetDate: Date
  title: string
  targetLabel: string
  duration: number // in minutes
}>()

const time = ref({
  days: 0,
  hours: 0,
  minutes: 0,
  seconds: 0,
})

let interval: number | undefined

function updateCountdown() {
  const future = addMinutes(targetDate, duration)
  const now = new Date()

  const distance = future.getTime() - now.getTime()

  if (distance <= 0) {
    clearInterval(interval)
    time.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    emit('timeout')
    return
  }

  time.value.days = Math.floor(distance / (1000 * 60 * 60 * 24))
  time.value.hours = Math.floor(
    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  )
  time.value.minutes = Math.floor(
    (distance % (1000 * 60 * 60)) / (1000 * 60)
  )
  time.value.seconds = Math.floor(
    (distance % (1000 * 60)) / 1000
  )
}

function reset() {
  const now = new Date()
  targetDate.setTime(now.getTime() + duration * 60 * 1000)
  updateCountdown()
}

onMounted(() => {
  updateCountdown()
  interval = window.setInterval(updateCountdown, 1000)
})

onUnmounted(() => {
  clearInterval(interval)
})


</script>
