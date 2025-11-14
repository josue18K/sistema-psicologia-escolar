<template>
  <q-form @submit="onSubmit" class="q-gutter-md">
    <div class="row q-col-gutter-md">
      <!-- Columna Izquierda -->
      <div class="col-12 col-md-6">
        <q-select
          v-model="form.dni_estudiante"
          :options="estudiantesOptions"
          label="Estudiante *"
          :rules="[val => !!val || 'El estudiante es obligatorio']"
          lazy-rules
          outlined
          use-input
          @filter="filterEstudiantes"
          map-options
          emit-value
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey">
                No se encontraron estudiantes
              </q-item-section>
            </q-item>
          </template>
        </q-select>

        <q-input
          v-model="form.fecha_cita"
          label="Fecha y Hora *"
          type="datetime-local"
          :rules="[
            val => !!val || 'La fecha y hora son obligatorias',
            val => esFechaFutura(val) || 'La fecha debe ser futura'
          ]"
          lazy-rules
          outlined
        />

        <q-select
          v-model="form.estado"
          :options="ESTADOS_OPTIONS"
          label="Estado"
          outlined
          map-options
          emit-value
          v-if="esEdicion"
        />
      </div>

      <!-- Columna Derecha -->
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.motivo"
          label="Motivo *"
          type="textarea"
          rows="5"
          :rules="[val => !!val || 'El motivo es obligatorio']"
          lazy-rules
          outlined
        />

        <q-checkbox
          v-model="form.correo_enviado"
          label="Correo enviado al apoderado"
          :disable="!estudianteSeleccionado?.apoderado?.correo"
        />

        <div v-if="!estudianteSeleccionado?.apoderado?.correo" class="text-caption text-warning">
          El estudiante no tiene un apoderado con correo registrado
        </div>
      </div>
    </div>

    <!-- Información del Estudiante Seleccionado -->
    <q-card v-if="estudianteSeleccionado" flat bordered class="q-mt-md bg-blue-1">
      <q-card-section>
        <div class="text-h6">Información del Estudiante</div>
        <div class="row q-gutter-y-sm">
          <div class="col-12 col-sm-6">
            <strong>Nombre:</strong> {{ estudianteSeleccionado.nombres }} {{ estudianteSeleccionado.apellidos }}
          </div>
          <div class="col-12 col-sm-6">
            <strong>Grado y Sección:</strong> {{ estudianteSeleccionado.grado }} {{ estudianteSeleccionado.seccion }}
          </div>
          <div class="col-12 col-sm-6">
            <strong>Edad:</strong> {{ estudianteSeleccionado.edad }} años
          </div>
          <div class="col-12 col-sm-6">
            <strong>Apoderado:</strong> {{ estudianteSeleccionado.apoderado?.nombre || 'No asignado' }}
          </div>
          <div class="col-12 col-sm-6" v-if="estudianteSeleccionado.apoderado">
            <strong>Correo apoderado:</strong> {{ estudianteSeleccionado.apoderado.correo || 'No registrado' }}
          </div>
          <div class="col-12 col-sm-6" v-if="estudianteSeleccionado.apoderado">
            <strong>Teléfono:</strong> {{ estudianteSeleccionado.apoderado.telefono || 'No registrado' }}
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Botones de Acción -->
    <div class="row q-mt-lg">
      <div class="col">
        <q-btn
          flat
          label="Cancelar"
          @click="$emit('cancelado')"
        />
      </div>
      <div class="col-auto q-gutter-x-sm">
        <q-btn
          label="Limpiar"
          color="grey"
          @click="resetForm"
          v-if="!esEdicion"
        />
        <q-btn
          :label="esEdicion ? 'Actualizar' : 'Registrar'"
          type="submit"
          color="primary"
          :loading="loading"
        />
      </div>
    </div>
  </q-form>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { citaService, estudianteService } from 'src/services/api'

const $q = useQuasar()
const props = defineProps({
  cita: {
    type: Object,
    default: null
  },
  esEdicion: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['guardado', 'cancelado'])

const loading = ref(false)
const estudiantes = ref([])
const estudiantesFiltrados = ref([])

const form = ref({
  dni_estudiante: '',
  fecha_cita: '',
  motivo: '',
  estado: 'PENDIENTE',
  correo_enviado: false
})

const ESTADOS_OPTIONS = [
  { label: 'Pendiente', value: 'PENDIENTE' },
  { label: 'Realizada', value: 'REALIZADA' },
  { label: 'Cancelada', value: 'CANCELADA' }
]

// Computed
const estudiantesOptions = computed(() =>
  estudiantesFiltrados.value.map(est => ({
    label: `${est.nombres} ${est.apellidos} - ${est.grado}${est.seccion} (${est.dni})`,
    value: est.dni
  }))
)

const estudianteSeleccionado = computed(() =>
  estudiantes.value.find(est => est.dni === form.value.dni_estudiante)
)

// Methods
async function loadEstudiantes() {
  try {
    const response = await estudianteService.getEstudiantes()
    estudiantes.value = response.data.estudiantes
    estudiantesFiltrados.value = [...estudiantes.value]
  } catch (error) {
    console.error('Error cargando estudiantes:', error)
  }
}

function filterEstudiantes(val, update) {
  if (val === '') {
    update(() => {
      estudiantesFiltrados.value = estudiantes.value
    })
    return
  }

  update(() => {
    const needle = val.toLowerCase()
    estudiantesFiltrados.value = estudiantes.value.filter(
      est => est.nombres.toLowerCase().includes(needle) ||
             est.apellidos.toLowerCase().includes(needle) ||
             est.dni.includes(needle)
    )
  })
}

function esFechaFutura(fecha) {
  if (!fecha) return true
  const fechaCita = new Date(fecha)
  const ahora = new Date()
  return fechaCita > ahora
}

async function onSubmit() {
  loading.value = true
  try {
    if (props.esEdicion) {
      await citaService.updateCita(props.cita.id, form.value)
      $q.notify({
        type: 'positive',
        message: 'Cita actualizada exitosamente'
      })
    } else {
      await citaService.createCita(form.value)
      $q.notify({
        type: 'positive',
        message: 'Cita registrada exitosamente'
      })
    }

    emit('guardado')

  } catch (error) {
    console.error('Error guardando cita:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error guardando cita'
    })
  } finally {
    loading.value = false
  }
}

function resetForm() {
  if (!props.esEdicion) {
    Object.keys(form.value).forEach(key => {
      if (key !== 'estado') {
        form.value[key] = ''
      }
    })
    form.value.estado = 'PENDIENTE'
    form.value.correo_enviado = false
  }
}

// Watchers
watch(() => props.cita, (newCita) => {
  if (newCita && props.esEdicion) {
    // Formatear fecha para el input datetime-local
    const fecha = new Date(newCita.fecha_cita)
    const fechaFormateada = fecha.toISOString().slice(0, 16)

    form.value = {
      dni_estudiante: newCita.dni_estudiante,
      fecha_cita: fechaFormateada,
      motivo: newCita.motivo,
      estado: newCita.estado,
      correo_enviado: newCita.correo_enviado
    }
  }
}, { immediate: true })

// Lifecycle
onMounted(async () => {
  await loadEstudiantes()
})
</script>
