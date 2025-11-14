<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="row justify-between items-center q-mb-md">
        <div class="text-h5">Notificaciones</div>
        <q-btn
          v-if="notificacionesStore.unreadCount > 0"
          flat
          color="primary"
          label="Marcar todas como leídas"
          @click="marcarTodasLeidas"
        />
      </div>

      <q-list bordered separator>
        <q-item
          v-for="notif in notificacionesStore.notificaciones"
          :key="notif.id"
          clickable
          :class="{ 'bg-blue-1': !notif.leida }"
          @click="marcarLeida(notif)"
        >
          <q-item-section avatar>
            <q-avatar :color="notif.leida ? 'grey-5' : 'primary'" text-color="white">
              <q-icon name="notifications" />
            </q-avatar>
          </q-item-section>

          <q-item-section>
            <q-item-label>
              <strong>{{ notif.remitente?.name || 'Sistema' }}</strong>
            </q-item-label>
            <q-item-label caption lines="2">
              {{ notif.mensaje }}
            </q-item-label>
            <q-item-label caption>
              {{ formatFecha(notif.fecha_envio) }}
            </q-item-label>
          </q-item-section>

          <q-item-section side>
            <q-icon
              v-if="!notif.leida"
              name="fiber_manual_record"
              color="primary"
              size="xs"
            />
          </q-item-section>
        </q-item>

        <q-item v-if="notificacionesStore.notificaciones.length === 0">
          <q-item-section class="text-center text-grey-6">
            <q-icon name="notifications_none" size="64px" />
            <p>No tienes notificaciones</p>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useNotificacionesStore } from 'stores/notificaciones'

const notificacionesStore = useNotificacionesStore()

const formatFecha = (fecha) => {
  return new Date(fecha).toLocaleString('es-PE', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const marcarLeida = async (notif) => {
  if (!notif.leida) {
    await notificacionesStore.marcarLeida(notif.id)
  }
}

const marcarTodasLeidas = async () => {
  await notificacionesStore.marcarTodasLeidas()
}

onMounted(() => {
  notificacionesStore.fetchAll()
})
</script>
