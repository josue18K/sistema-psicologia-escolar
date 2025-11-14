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

        <q-select
          v-model="form.psicologo_id"
          :options="psicologosOptions"
          label="Psicólogo *"
          :rules="[val => !!val || 'El psicólogo es obligatorio']"
          lazy-rules
          outlined
          map-options
          emit-value
        />

        <q-input
          v-model="form.fecha"
          label="Fecha *"
          type="date"
          :rules="[val => !!val || 'La fecha es obligatoria']"
          lazy-rules
          outlined
        />

        <q-input
          v-model="form.tipo"
          label="Tipo de Problema *"
          :rules="[val => !!val || 'El tipo es obligatorio']"
          lazy-rules
          outlined
        />
      </div>

      <!-- Columna Derecha -->
      <div class="col-12 col-md-6">
        <q-select
          v-model="form.nivel_riesgo"
          :options="NIVELES_RIESGO_OPTIONS"
          label="Nivel de Riesgo *"
          :rules="[val => !!val || 'El nivel de riesgo es obligatorio']"
          lazy-rules
          outlined
          map-options
          emit-value
        />

        <q-input
          v-model="form.observaciones"
          label="Observaciones"
          type="textarea"
          rows="3"
          outlined
        />

        <q-input
          v-model="form.recomendaciones"
          label="Recomendaciones"
          type="textarea"
          rows="3"
          outlined
        />
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
            <strong>Tutor:</strong> {{ estudianteSeleccionado.tutor?.name || 'No asignado' }}
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
import { diagnosticoService, estudianteService, userService } from 'src/services/api'
import { NIVELES_RIESGO } from 'src/utils/constants'

const $q = useQuasar()
const props = defineProps({
  diagnostico: {
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
const psicologos = ref([])
const estudiantesFiltrados = ref([])

const form = ref({
  dni_estudiante: '',
  psicologo_id: '',
  fecha: '',
  tipo: '',
  observaciones: '',
  recomendaciones: '',
  nivel_riesgo: ''
})

const NIVELES_RIESGO_OPTIONS = NIVELES_RIESGO.map(nivel => ({
  label: nivel.label,
  value: nivel.value
}))

// Computed
const estudiantesOptions = computed(() =>
  estudiantesFiltrados.value.map(est => ({
    label: `${est.nombres} ${est.apellidos} - ${est.grado}${est.seccion} (${est.dni})`,
    value: est.dni
  }))
)

const psicologosOptions = computed(() =>
  psicologos.value.map(psic => ({
    label: psic.name,
    value: psic.id
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

async function loadPsicologos() {
  try {
    const response = await userService.getPsicologos()
    psicologos.value = response.data.psicologos
  } catch (error) {
    console.error('Error cargando psicólogos:', error)
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

async function onSubmit() {
  loading.value = true
  try {
    if (props.esEdicion) {
      await diagnosticoService.updateDiagnostico(props.diagnostico.id, form.value)
      $q.notify({
        type: 'positive',
        message: 'Diagnóstico actualizado exitosamente'
      })
    } else {
      await diagnosticoService.createDiagnostico(form.value)
      $q.notify({
        type: 'positive',
        message: 'Diagnóstico registrado exitosamente'
      })
    }

    emit('guardado')

  } catch (error) {
    console.error('Error guardando diagnóstico:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error guardando diagnóstico'
    })
  } finally {
    loading.value = false
  }
}

function resetForm() {
  if (!props.esEdicion) {
    Object.keys(form.value).forEach(key => {
      form.value[key] = ''
    })
  }
}

// Watchers
watch(() => props.diagnostico, (newDiagnostico) => {
  if (newDiagnostico && props.esEdicion) {
    form.value = {
      dni_estudiante: newDiagnostico.dni_estudiante,
      psicologo_id: newDiagnostico.psicologo_id,
      fecha: newDiagnostico.fecha,
      tipo: newDiagnostico.tipo,
      observaciones: newDiagnostico.observaciones || '',
      recomendaciones: newDiagnostico.recomendaciones || '',
      nivel_riesgo: newDiagnostico.nivel_riesgo
    }
  }
}, { immediate: true })

// Lifecycle
onMounted(async () => {
  await Promise.all([loadEstudiantes(), loadPsicologos()])
})
</script>
