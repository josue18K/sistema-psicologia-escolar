<template>
  <q-page class="q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h4 text-weight-bold text-primary">
          {{ estudiante.nombres }} {{ estudiante.apellidos }}
        </div>
        <div class="text-h6 text-grey-7">
          Detalle del estudiante - DNI: {{ estudiante.dni }}
        </div>
      </div>
      <div class="col-auto q-gutter-x-sm">
        <q-btn
          flat
          icon="arrow_back"
          label="Volver"
          @click="$router.push('/estudiantes')"
        />
        <q-btn
          v-if="isTOE || isPsicologo"
          color="primary"
          icon="edit"
          label="Editar"
          @click="editarEstudiante"
        />
      </div>
    </div>

    <div v-if="loading" class="text-center q-pa-xl">
      <q-spinner-gears size="xl" color="primary" />
      <div class="q-mt-md">Cargando información del estudiante...</div>
    </div>

    <div v-else class="row q-col-gutter-lg">
      <!-- Columna Izquierda: Información Personal -->
      <div class="col-12 col-lg-4">
        <!-- Tarjeta de Información Personal -->
        <q-card class="q-mb-md" flat bordered>
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Información Personal</div>
          </q-card-section>
          <q-card-section>
            <div class="q-gutter-y-md">
              <div class="row">
                <div class="col-4 text-weight-medium">DNI:</div>
                <div class="col-8">{{ estudiante.dni }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Nombres:</div>
                <div class="col-8">{{ estudiante.nombres }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Apellidos:</div>
                <div class="col-8">{{ estudiante.apellidos }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Fecha Nac.:</div>
                <div class="col-8">{{ formatDate(estudiante.fecha_nacimiento) }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Edad:</div>
                <div class="col-8">{{ estudiante.edad }} años</div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Tarjeta de Información Académica -->
        <q-card class="q-mb-md" flat bordered>
          <q-card-section class="bg-blue text-white">
            <div class="text-h6">Información Académica</div>
          </q-card-section>
          <q-card-section>
            <div class="q-gutter-y-md">
              <div class="row">
                <div class="col-4 text-weight-medium">Grado:</div>
                <div class="col-8">{{ estudiante.grado }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Sección:</div>
                <div class="col-8">{{ estudiante.seccion }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Tutor:</div>
                <div class="col-8">
                  <div v-if="estudiante.tutor">
                    {{ estudiante.tutor.name }}
                    <div class="text-caption text-grey">
                      {{ estudiante.tutor.email }}
                    </div>
                  </div>
                  <div v-else class="text-grey">No asignado</div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Tarjeta de Apoderado -->
        <q-card flat bordered>
          <q-card-section class="bg-green text-white">
            <div class="text-h6">Apoderado</div>
          </q-card-section>
          <q-card-section>
            <div v-if="estudiante.apoderado" class="q-gutter-y-md">
              <div class="row">
                <div class="col-4 text-weight-medium">Nombre:</div>
                <div class="col-8">{{ estudiante.apoderado.nombre }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Parentesco:</div>
                <div class="col-8">{{ estudiante.apoderado.parentesco }}</div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Correo:</div>
                <div class="col-8">
                  {{ estudiante.apoderado.correo || 'No registrado' }}
                </div>
              </div>
              <div class="row">
                <div class="col-4 text-weight-medium">Teléfono:</div>
                <div class="col-8">{{ estudiante.apoderado.telefono }}</div>
              </div>
            </div>
            <div v-else class="text-center text-grey q-py-md">
              No tiene apoderado asignado
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Columna Derecha: Diagnósticos y Citas -->
      <div class="col-12 col-lg-8">
        <!-- Pestañas -->
        <q-tabs
          v-model="tab"
          class="text-primary"
        >
          <q-tab name="diagnosticos" icon="clinical_notes" label="Diagnósticos" />
          <q-tab name="citas" icon="event" label="Citas" />
          <q-tab name="estadisticas" icon="analytics" label="Estadísticas" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="tab" animated>
          <!-- Panel de Diagnósticos -->
          <q-tab-panel name="diagnosticos">
            <div class="row items-center q-mb-md">
              <div class="col">
                <div class="text-h6">Historial de Diagnósticos</div>
              </div>
              <div class="col-auto">
                <q-btn
                  v-if="isTOE || isPsicologo"
                  color="primary"
                  icon="add"
                  label="Nuevo Diagnóstico"
                  @click="nuevoDiagnostico"
                />
              </div>
            </div>

            <div v-if="loadingDiagnosticos" class="text-center q-pa-md">
              <q-spinner size="md" color="primary" />
            </div>
            <div v-else-if="diagnosticos.length === 0" class="text-center text-grey q-pa-lg">
              <q-icon name="clinical_notes" size="xl" />
              <div class="q-mt-md">No hay diagnósticos registrados</div>
            </div>
            <div v-else class="q-gutter-y-md">
              <q-card
                v-for="diagnostico in diagnosticos"
                :key="diagnostico.id"
                flat
                bordered
                :class="`border-left-${getRiskColor(diagnostico.nivel_riesgo)}`"
              >
                <q-card-section>
                  <div class="row items-center">
                    <div class="col">
                      <div class="text-weight-medium">
                        {{ diagnostico.tipo }}
                      </div>
                      <div class="text-caption text-grey">
                        {{ formatDate(diagnostico.fecha) }} -
                        Por: {{ diagnostico.psicologo?.name }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-badge :color="getRiskColor(diagnostico.nivel_riesgo)" class="text-capitalize">
                        {{ diagnostico.nivel_riesgo.toLowerCase() }}
                      </q-badge>
                    </div>
                  </div>

                  <q-separator class="q-my-sm" />

                  <div v-if="diagnostico.observaciones" class="q-mt-sm">
                    <div class="text-caption text-weight-medium">Observaciones:</div>
                    <div class="text-body2">{{ diagnostico.observaciones }}</div>
                  </div>

                  <div v-if="diagnostico.recomendaciones" class="q-mt-sm">
                    <div class="text-caption text-weight-medium">Recomendaciones:</div>
                    <div class="text-body2">{{ diagnostico.recomendaciones }}</div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </q-tab-panel>

          <!-- Panel de Citas -->
          <q-tab-panel name="citas">
            <div class="row items-center q-mb-md">
              <div class="col">
                <div class="text-h6">Historial de Citas</div>
              </div>
              <div class="col-auto">
                <q-btn
                  v-if="isTOE || isPsicologo"
                  color="primary"
                  icon="add"
                  label="Nueva Cita"
                  @click="nuevaCita"
                />
              </div>
            </div>

            <div v-if="loadingCitas" class="text-center q-pa-md">
              <q-spinner size="md" color="primary" />
            </div>
            <div v-else-if="citas.length === 0" class="text-center text-grey q-pa-lg">
              <q-icon name="event" size="xl" />
              <div class="q-mt-md">No hay citas registradas</div>
            </div>
            <div v-else class="q-gutter-y-md">
              <q-card
                v-for="cita in citas"
                :key="cita.id"
                flat
                bordered
              >
                <q-card-section>
                  <div class="row items-center">
                    <div class="col">
                      <div class="text-weight-medium">
                        {{ formatDateTime(cita.fecha_cita) }}
                      </div>
                      <div class="text-caption text-grey">
                        {{ cita.motivo }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-badge :color="getStatusColor(cita.estado)" class="text-capitalize">
                        {{ cita.estado.toLowerCase() }}
                      </q-badge>
                    </div>
                  </div>

                  <div class="row q-mt-sm">
                    <div class="col">
                      <div class="text-caption">
                        <q-icon name="info" size="xs" />
                        {{ cita.correo_enviado ? 'Correo enviado' : 'Correo pendiente' }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-btn
                        v-if="isTOE || isPsicologo"
                        flat
                        dense
                        icon="edit"
                        size="sm"
                        @click="editarCita(cita)"
                      >
                        <q-tooltip>Editar cita</q-tooltip>
                      </q-btn>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </q-tab-panel>

          <!-- Panel de Estadísticas -->
          <q-tab-panel name="estadisticas">
            <div class="text-h6 q-mb-md">Estadísticas del Estudiante</div>

            <div class="row q-col-gutter-md">
              <div class="col-12 col-sm-6">
                <q-card flat bordered>
                  <q-card-section class="text-center">
                    <div class="text-h4 text-primary">{{ diagnosticos.length }}</div>
                    <div class="text-caption">Total Diagnósticos</div>
                  </q-card-section>
                </q-card>
              </div>

              <div class="col-12 col-sm-6">
                <q-card flat bordered>
                  <q-card-section class="text-center">
                    <div class="text-h4 text-orange">{{ citas.length }}</div>
                    <div class="text-caption">Total Citas</div>
                  </q-card-section>
                </q-card>
              </div>
            </div>

            <!-- Distribución de Niveles de Riesgo -->
            <q-card class="q-mt-md" flat bordered>
              <q-card-section>
                <div class="text-h6">Distribución de Diagnósticos</div>
              </q-card-section>
              <q-card-section>
                <div v-if="diagnosticos.length === 0" class="text-center text-grey">
                  No hay datos para mostrar
                </div>
                <div v-else class="row items-center">
                  <div class="col-12 col-md-8">
                    <div class="q-gutter-y-sm">
                      <div
                        v-for="nivel in nivelesRiesgo"
                        :key="nivel.value"
                        class="row items-center"
                      >
                        <div class="col-3">
                          <q-badge :color="nivel.color" class="text-capitalize">
                            {{ nivel.value.toLowerCase() }}
                          </q-badge>
                        </div>
                        <div class="col-6">
                          <q-linear-progress
                            :value="getPorcentajeNivel(nivel.value)"
                            :color="nivel.color"
                            size="20px"
                          >
                            <div class="absolute-full flex flex-center">
                              <q-badge
                                :color="nivel.color"
                                text-color="white"
                                :label="`${getCountNivel(nivel.value)} (${Math.round(getPorcentajeNivel(nivel.value) * 100)}%)`"
                              />
                            </div>
                          </q-linear-progress>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { estudianteService, diagnosticoService, citaService } from 'src/services/api'
import { formatDate, formatDateTime, getRiskColor, getStatusColor } from 'src/utils/helpers'
import { NIVELES_RIESGO } from 'src/utils/constants'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const loadingDiagnosticos = ref(false)
const loadingCitas = ref(false)
const estudiante = ref({})
const diagnosticos = ref([])
const citas = ref([])
const tab = ref('diagnosticos')

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

const nivelesRiesgo = computed(() => NIVELES_RIESGO)

// Methods
async function loadEstudiante() {
  loading.value = true
  try {
    const response = await estudianteService.getEstudiante(route.params.dni)
    estudiante.value = response.data.estudiante
    await Promise.all([loadDiagnosticos(), loadCitas()])
  } catch (error) {
    console.error('Error cargando estudiante:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando información del estudiante'
    })
    router.push('/estudiantes')
  } finally {
    loading.value = false
  }
}

async function loadDiagnosticos() {
  loadingDiagnosticos.value = true
  try {
    const response = await diagnosticoService.getByEstudiante(route.params.dni)
    diagnosticos.value = response.data.diagnosticos
  } catch (error) {
    console.error('Error cargando diagnósticos:', error)
  } finally {
    loadingDiagnosticos.value = false
  }
}

async function loadCitas() {
  loadingCitas.value = true
  try {
    const response = await citaService.getCitas({ dni_estudiante: route.params.dni })
    citas.value = response.data.citas
  } catch (error) {
    console.error('Error cargando citas:', error)
  } finally {
    loadingCitas.value = false
  }
}

function editarEstudiante() {
  router.push(`/estudiantes/editar/${route.params.dni}`)
}

function nuevoDiagnostico() {
  // Redirigir al formulario de diagnóstico con el estudiante pre-seleccionado
  router.push('/diagnosticos')
}

function nuevaCita() {
  // Redirigir al formulario de cita con el estudiante pre-seleccionado
  router.push('/citas')
}

function editarCita() {
  // Aquí podrías implementar la edición de cita
  $q.notify({
    type: 'info',
    message: 'Funcionalidad de edición de cita en desarrollo'
  })
}

function getCountNivel(nivel) {
  return diagnosticos.value.filter(d => d.nivel_riesgo === nivel).length
}

function getPorcentajeNivel(nivel) {
  if (diagnosticos.value.length === 0) return 0
  return getCountNivel(nivel) / diagnosticos.value.length
}

// Lifecycle
onMounted(() => {
  loadEstudiante()
})
</script>

<style scoped>
.border-left-positive {
  border-left: 4px solid #4caf50;
}

.border-left-warning {
  border-left: 4px solid #ff9800;
}

.border-left-negative {
  border-left: 4px solid #f44336;
}
</style>
