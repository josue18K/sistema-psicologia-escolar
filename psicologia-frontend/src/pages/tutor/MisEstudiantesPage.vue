<template>
  <q-page padding>
    <div class="q-pa-md fade-in">
      <div class="text-h4 q-mb-md">Mis Estudiantes Asignados</div>

      <div class="row q-col-gutter-md">
        <div
          v-for="estudiante in estudiantesStore.estudiantes.filter(e => e.tutor_id === authStore.user.id)"
          :key="estudiante.dni"
          class="col-12 col-md-6 col-lg-4"
        >
          <q-card class="info-card card-shadow">
            <q-card-section class="bg-gradient-primary text-white">
              <div class="text-h6">
                {{ estudiante.nombres }} {{ estudiante.apellidos }}
              </div>
              <div class="text-caption">{{ estudiante.grado }} - {{ estudiante.seccion }}</div>
            </q-card-section>

            <q-card-section>
              <div class="row q-col-gutter-sm">
                <div class="col-6">
                  <div class="text-caption text-grey-7">DNI</div>
                  <div class="text-weight-bold">{{ estudiante.dni }}</div>
                </div>
                <div class="col-6">
                  <div class="text-caption text-grey-7">Edad</div>
                  <div class="text-weight-bold">{{ estudiante.edad }} años</div>
                </div>
              </div>
            </q-card-section>

            <q-card-actions>
              <q-btn
                flat
                color="primary"
                label="Ver Diagnósticos"
                @click="verDiagnosticos(estudiante.dni)"
              />
              <q-space />
              <q-btn
                flat
                round
                icon="more_vert"
              >
                <q-menu>
                  <q-list>
                    <q-item clickable @click="verCitas(estudiante.dni)">
                      <q-item-section>Ver Citas</q-item-section>
                    </q-item>
                    <q-item clickable @click="verApoderado(estudiante)">
                      <q-item-section>Ver Apoderado</q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
            </q-card-actions>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useAuthStore } from 'stores/auth'
import { useQuasar } from 'quasar'

const estudiantesStore = useEstudiantesStore()
const authStore = useAuthStore()
const $q = useQuasar()

const verDiagnosticos = (dni) => {
  $q.notify({
    type: 'info',
    message: `Ver diagnósticos de estudiante ${dni}`,
    position: 'top'
  })
}

const verCitas = (dni) => {
  $q.notify({
    type: 'info',
    message: `Ver citas de estudiante ${dni}`,
    position: 'top'
  })
}

const verApoderado = (estudiante) => {
  if (!estudiante.apoderado) {
    $q.notify({
      type: 'warning',
      message: 'Este estudiante no tiene apoderado registrado',
      position: 'top'
    })
    return
  }

  $q.dialog({
    title: 'Apoderado',
    message: `
      <strong>Nombre:</strong> ${estudiante.apoderado.nombre}<br>
      <strong>Parentesco:</strong> ${estudiante.apoderado.parentesco}<br>
      <strong>Teléfono:</strong> ${estudiante.apoderado.telefono}
    `,
    html: true
  })
}

onMounted(() => {
  estudiantesStore.fetchAll()
})
</script>
