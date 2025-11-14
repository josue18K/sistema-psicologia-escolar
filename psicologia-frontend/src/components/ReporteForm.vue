<template>
  <q-form @submit="onSubmit" class="q-gutter-md">
    <div class="row q-col-gutter-md">
      <!-- Columna Izquierda -->
      <div class="col-12 col-md-6">
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
          v-model="form.mes"
          label="Mes *"
          :rules="[val => !!val || 'El mes es obligatorio']"
          lazy-rules
          outlined
        >
          <template v-slot:hint>
            Ejemplo: Enero 2024, Febrero 2024, etc.
          </template>
        </q-input>

        <q-input
          v-model="form.total_estudiantes"
          label="Total de Estudiantes *"
          type="number"
          :rules="[
            val => !!val || 'El total es obligatorio',
            val => val >= 0 || 'El total debe ser mayor o igual a 0'
          ]"
          lazy-rules
          outlined
        />
      </div>

      <!-- Columna Derecha -->
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.casos_criticos"
          label="Casos Críticos *"
          type="number"
          :rules="[
            val => !!val || 'Los casos críticos son obligatorios',
            val => val >= 0 || 'Los casos críticos deben ser mayor o igual a 0',
            val => val <= form.total_estudiantes || 'Los casos críticos no pueden ser mayores al total'
          ]"
          lazy-rules
          outlined
        />

        <q-input
          v-model="form.observaciones"
          label="Observaciones"
          type="textarea"
          rows="4"
          outlined
        />
      </div>
    </div>

    <!-- Resumen del Reporte -->
    <q-card flat bordered class="q-mt-md bg-grey-2">
      <q-card-section>
        <div class="text-h6">Resumen del Reporte</div>
        <div class="row q-gutter-y-sm">
          <div class="col-12 col-sm-6">
            <strong>Psicólogo:</strong> {{ psicologoSeleccionado?.name || 'No seleccionado' }}
          </div>
          <div class="col-12 col-sm-6">
            <strong>Mes:</strong> {{ form.mes || 'No especificado' }}
          </div>
          <div class="col-12 col-sm-6">
            <strong>Total Estudiantes:</strong> {{ form.total_estudiantes || 0 }}
          </div>
          <div class="col-12 col-sm-6">
            <strong>Casos Críticos:</strong> {{ form.casos_criticos || 0 }}
          </div>
          <div class="col-12 col-sm-6" v-if="form.total_estudiantes > 0">
            <strong>Porcentaje Críticos:</strong>
            {{ Math.round((form.casos_criticos / form.total_estudiantes) * 100) }}%
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
import { reporteService, userService } from 'src/services/api'

const $q = useQuasar()
const props = defineProps({
  reporte: {
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
const psicologos = ref([])

const form = ref({
  psicologo_id: '',
  mes: '',
  total_estudiantes: 0,
  casos_criticos: 0,
  observaciones: ''
})

// Computed
const psicologosOptions = computed(() =>
  psicologos.value.map(psic => ({
    label: psic.name,
    value: psic.id
  }))
)

const psicologoSeleccionado = computed(() =>
  psicologos.value.find(psic => psic.id === form.value.psicologo_id)
)

// Methods
async function loadPsicologos() {
  try {
    const response = await userService.getPsicologos()
    psicologos.value = response.data.psicologos
  } catch (error) {
    console.error('Error cargando psicólogos:', error)
  }
}

async function onSubmit() {
  loading.value = true
  try {
    if (props.esEdicion) {
      await reporteService.updateReporte(props.reporte.id, form.value)
      $q.notify({
        type: 'positive',
        message: 'Reporte actualizado exitosamente'
      })
    } else {
      await reporteService.createReporte(form.value)
      $q.notify({
        type: 'positive',
        message: 'Reporte registrado exitosamente'
      })
    }

    emit('guardado')

  } catch (error) {
    console.error('Error guardando reporte:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error guardando reporte'
    })
  } finally {
    loading.value = false
  }
}

function resetForm() {
  if (!props.esEdicion) {
    Object.keys(form.value).forEach(key => {
      if (key === 'total_estudiantes' || key === 'casos_criticos') {
        form.value[key] = 0
      } else {
        form.value[key] = ''
      }
    })
  }
}

// Watchers
watch(() => props.reporte, (newReporte) => {
  if (newReporte && props.esEdicion) {
    form.value = {
      psicologo_id: newReporte.psicologo_id,
      mes: newReporte.mes,
      total_estudiantes: newReporte.total_estudiantes,
      casos_criticos: newReporte.casos_criticos,
      observaciones: newReporte.observaciones || ''
    }
  }
}, { immediate: true })

// Lifecycle
onMounted(async () => {
  await loadPsicologos()
})
</script>
