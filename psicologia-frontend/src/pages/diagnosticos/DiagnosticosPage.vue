<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Estadísticas rápidas -->
      <div class="row q-col-gutter-md q-mb-md">
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <div class="text-h4 text-negative">{{ stats?.alto || 0 }}</div>
              <div class="text-grey-7">Riesgo Alto</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <div class="text-h4 text-warning">{{ stats?.medio || 0 }}</div>
              <div class="text-grey-7">Riesgo Medio</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <div class="text-h4 text-positive">{{ stats?.bajo || 0 }}</div>
              <div class="text-grey-7">Riesgo Bajo</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <div class="text-h4 text-primary">{{ stats?.total || 0 }}</div>
              <div class="text-grey-7">Total</div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Tabla de diagnósticos -->
      <data-table
        :rows="diagnosticosStore.diagnosticos"
        :columns="columns"
        :loading="diagnosticosStore.loading"
        title="Diagnósticos Registrados"
        add-button-label="Nuevo Diagnóstico"
        @add="openDialog()"
      >
        <template v-slot:body-cell-nivel_riesgo="props">
          <q-td :props="props">
            <q-badge
              :color="getRiesgoColor(props.row.nivel_riesgo)"
              :label="props.row.nivel_riesgo"
            />
          </q-td>
        </template>

        <template v-slot:actions="{ row }">
          <q-btn
            flat
            dense
            round
            icon="visibility"
            color="primary"
            @click="viewDiagnostico(row)"
          >
            <q-tooltip>Ver detalles</q-tooltip>
          </q-btn>

          <q-btn
            flat
            dense
            round
            icon="edit"
            color="primary"
            @click="openDialog(row)"
          >
            <q-tooltip>Editar</q-tooltip>
          </q-btn>

          <q-btn
            flat
            dense
            round
            icon="delete"
            color="negative"
            @click="confirmDelete(row)"
          >
            <q-tooltip>Eliminar</q-tooltip>
          </q-btn>
        </template>
      </data-table>

      <!-- Dialog formulario -->
      <dialog-form
        v-model="showDialog"
        :title="isEditing ? 'Editar Diagnóstico' : 'Nuevo Diagnóstico'"
        :loading="diagnosticosStore.loading"
        width="800px"
        @submit="handleSubmit"
      >
        <q-form ref="formRef" class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <!-- Estudiante -->
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.dni_estudiante"
                :options="estudiantesOptions"
                label="Estudiante *"
                outlined
                dense
                emit-value
                map-options
                option-value="dni"
                option-label="nombre_completo"
                :rules="[val => !!val || 'Selecciona un estudiante']"
              />
            </div>

            <!-- Psicólogo -->
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.psicologo_id"
                :options="psicologosOptions"
                label="Psicólogo *"
                outlined
                dense
                emit-value
                map-options
                option-value="id"
                option-label="name"
                :rules="[val => !!val || 'Selecciona un psicólogo']"
              />
            </div>

            <!-- Fecha -->
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.fecha"
                label="Fecha del diagnóstico *"
                outlined
                dense
                type="date"
                :rules="[val => !!val || 'La fecha es obligatoria']"
              />
            </div>

            <!-- Nivel de riesgo -->
            <div class="col-12 col-md-6">
              <q-select
                v-model="form.nivel_riesgo"
                :options="nivelRiesgoOptions"
                label="Nivel de Riesgo *"
                outlined
                dense
                emit-value
                map-options
                :rules="[val => !!val || 'Selecciona el nivel de riesgo']"
              />
            </div>

            <!-- Tipo de problema -->
            <div class="col-12">
              <q-input
                v-model="form.tipo"
                label="Tipo de problema *"
                outlined
                dense
                :rules="[val => !!val || 'El tipo es obligatorio']"
              />
            </div>

            <!-- Observaciones -->
            <div class="col-12">
              <q-input
                v-model="form.observaciones"
                label="Observaciones"
                outlined
                dense
                type="textarea"
                rows="4"
              />
            </div>

            <!-- Recomendaciones -->
            <div class="col-12">
              <q-input
                v-model="form.recomendaciones"
                label="Recomendaciones"
                outlined
                dense
                type="textarea"
                rows="4"
              />
            </div>
          </div>
        </q-form>
      </dialog-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useDiagnosticosStore } from 'stores/diagnosticos'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useUsuariosStore } from 'stores/usuarios'
import { useAuthStore } from 'stores/auth'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'
import DialogForm from 'components/DialogForm.vue'

const diagnosticosStore = useDiagnosticosStore()
const estudiantesStore = useEstudiantesStore()
const usuariosStore = useUsuariosStore()
const authStore = useAuthStore()
const $q = useQuasar()
const formRef = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const stats = ref(null)

const estudiantesOptions = ref([])
const psicologosOptions = ref([])

const form = ref({
  dni_estudiante: '',
  psicologo_id: null,
  fecha: '',
  tipo: '',
  observaciones: '',
  recomendaciones: '',
  nivel_riesgo: ''
})

const nivelRiesgoOptions = [
  { label: 'Bajo', value: 'BAJO' },
  { label: 'Medio', value: 'MEDIO' },
  { label: 'Alto', value: 'ALTO' }
]

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  {
    name: 'estudiante',
    label: 'Estudiante',
    field: row => row.estudiante?.nombre_completo || 'N/A',
    align: 'left',
    sortable: true
  },
  { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left', sortable: true },
  { name: 'nivel_riesgo', label: 'Riesgo', field: 'nivel_riesgo', align: 'center', sortable: true },
  { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'center', sortable: true },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const getRiesgoColor = (nivel) => {
  switch (nivel) {
    case 'ALTO': return 'negative'
    case 'MEDIO': return 'warning'
    case 'BAJO': return 'positive'
    default: return 'grey'
  }
}

const openDialog = (diagnostico = null) => {
  if (diagnostico) {
    isEditing.value = true
    editingId.value = diagnostico.id
    form.value = {
      dni_estudiante: diagnostico.dni_estudiante,
      psicologo_id: diagnostico.psicologo_id,
      fecha: diagnostico.fecha,
      tipo: diagnostico.tipo,
      observaciones: diagnostico.observaciones,
      recomendaciones: diagnostico.recomendaciones,
      nivel_riesgo: diagnostico.nivel_riesgo
    }
  } else {
    isEditing.value = false
    editingId.value = null
    form.value = {
      dni_estudiante: '',
      psicologo_id: authStore.isPsicologo ? authStore.user.id : null,
      fecha: new Date().toISOString().split('T')[0],
      tipo: '',
      observaciones: '',
      recomendaciones: '',
      nivel_riesgo: ''
    }
  }
  showDialog.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  let result
  if (isEditing.value) {
    result = await diagnosticosStore.update(editingId.value, form.value)
  } else {
    result = await diagnosticosStore.create(form.value)
  }

  if (result.success) {
    showDialog.value = false
    loadStats()
  }
}

const viewDiagnostico = (diagnostico) => {
  $q.dialog({
    title: 'Detalles del Diagnóstico',
    message: `
      <strong>Estudiante:</strong> ${diagnostico.estudiante?.nombre_completo}<br>
      <strong>Tipo:</strong> ${diagnostico.tipo}<br>
      <strong>Nivel de Riesgo:</strong> ${diagnostico.nivel_riesgo}<br>
      <strong>Fecha:</strong> ${diagnostico.fecha}<br><br>
      <strong>Observaciones:</strong><br>${diagnostico.observaciones || 'Sin observaciones'}<br><br>
      <strong>Recomendaciones:</strong><br>${diagnostico.recomendaciones || 'Sin recomendaciones'}
    `,
    html: true
  })
}

const confirmDelete = (diagnostico) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: '¿Estás seguro de eliminar este diagnóstico?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await diagnosticosStore.delete(diagnostico.id)
    loadStats()
  })
}

const loadStats = async () => {
  stats.value = await diagnosticosStore.fetchEstadisticas()
}

onMounted(async () => {
  diagnosticosStore.fetchAll()
  await estudiantesStore.fetchAll()

  estudiantesOptions.value = estudiantesStore.estudiantes.map(e => ({
    dni: e.dni,
    nombre_completo: `${e.nombres} ${e.apellidos} (${e.dni})`
  }))

  psicologosOptions.value = await usuariosStore.fetchPsicologos()

  loadStats()
})
</script>
