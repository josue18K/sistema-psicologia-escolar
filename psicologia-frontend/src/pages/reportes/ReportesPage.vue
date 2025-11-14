<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Botón generar reporte mensual -->
      <div class="row q-mb-md">
        <q-btn
          color="primary"
          icon="auto_graph"
          label="Generar Reporte Mensual"
          @click="showGenerarDialog = true"
          unelevated
        />
      </div>

      <!-- Tabla de reportes -->
      <data-table
        :rows="reportesStore.reportes"
        :columns="columns"
        :loading="reportesStore.loading"
        title="Reportes Registrados"
        add-button-label="Nuevo Reporte Manual"
        @add="openDialog()"
      >
        <template v-slot:actions="{ row }">
          <q-btn
            flat
            dense
            round
            icon="visibility"
            color="primary"
            @click="viewReporte(row)"
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
            icon="download"
            color="secondary"
            @click="exportarPDF(row)"
          >
            <q-tooltip>Exportar PDF</q-tooltip>
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

      <!-- Dialog generar reporte mensual -->
      <q-dialog v-model="showGenerarDialog" persistent>
        <q-card style="min-width: 400px">
          <q-card-section>
            <div class="text-h6">Generar Reporte Mensual</div>
          </q-card-section>

          <q-card-section>
            <q-form ref="generarFormRef" class="q-gutter-md">
              <q-select
                v-model="generarForm.psicologo_id"
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

              <q-input
                v-model="generarForm.mes"
                label="Mes *"
                outlined
                dense
                placeholder="Ej: Enero 2025"
                :rules="[val => !!val || 'El mes es obligatorio']"
              />
            </q-form>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn
              label="Cancelar"
              color="grey-7"
              flat
              @click="showGenerarDialog = false"
            />
            <q-btn
              label="Generar"
              color="primary"
              :loading="reportesStore.loading"
              @click="handleGenerarMensual"
              unelevated
            />
          </q-card-actions>
        </q-card>
      </q-dialog>

      <!-- Dialog formulario manual -->
      <dialog-form
        v-model="showDialog"
        :title="isEditing ? 'Editar Reporte' : 'Nuevo Reporte'"
        :loading="reportesStore.loading"
        @submit="handleSubmit"
      >
        <q-form ref="formRef" class="q-gutter-md">
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

          <q-input
            v-model="form.mes"
            label="Mes *"
            outlined
            dense
            placeholder="Ej: Enero 2025"
            :rules="[val => !!val || 'El mes es obligatorio']"
          />

          <q-input
            v-model.number="form.total_estudiantes"
            label="Total de estudiantes *"
            outlined
            dense
            type="number"
            :rules="[
              val => val !== null || 'El total es obligatorio',
              val => val >= 0 || 'Debe ser mayor o igual a 0'
            ]"
          />

          <q-input
            v-model.number="form.casos_criticos"
            label="Casos críticos *"
            outlined
            dense
            type="number"
            :rules="[
              val => val !== null || 'Los casos críticos son obligatorios',
              val => val >= 0 || 'Debe ser mayor o igual a 0'
            ]"
          />

          <q-input
            v-model="form.observaciones"
            label="Observaciones"
            outlined
            dense
            type="textarea"
            rows="4"
          />
        </q-form>
      </dialog-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useReportesStore } from 'stores/reportes'
import { useUsuariosStore } from 'stores/usuarios'
import { useAuthStore } from 'stores/auth'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'
import DialogForm from 'components/DialogForm.vue'

const reportesStore = useReportesStore()
const usuariosStore = useUsuariosStore()
const authStore = useAuthStore()
const $q = useQuasar()
const formRef = ref(null)
const generarFormRef = ref(null)

const showDialog = ref(false)
const showGenerarDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const psicologosOptions = ref([])

const form = ref({
  psicologo_id: null,
  mes: '',
  total_estudiantes: 0,
  casos_criticos: 0,
  observaciones: ''
})

const generarForm = ref({
  psicologo_id: null,
  mes: ''
})

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  {
    name: 'psicologo',
    label: 'Psicólogo',
    field: row => row.psicologo?.name || 'N/A',
    align: 'left',
    sortable: true
  },
  { name: 'mes', label: 'Mes', field: 'mes', align: 'center', sortable: true },
  { name: 'total_estudiantes', label: 'Total Estudiantes', field: 'total_estudiantes', align: 'center', sortable: true },
  { name: 'casos_criticos', label: 'Casos Críticos', field: 'casos_criticos', align: 'center', sortable: true },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const handleGenerarMensual = async () => {
  const valid = await generarFormRef.value.validate()
  if (!valid) return

  const result = await reportesStore.generarMensual(generarForm.value)

  if (result.success) {
    showGenerarDialog.value = false
    generarForm.value = { psicologo_id: null, mes: '' }
  }
}

const openDialog = (reporte = null) => {
  if (reporte) {
    isEditing.value = true
    editingId.value = reporte.id
    form.value = {
      psicologo_id: reporte.psicologo_id,
      mes: reporte.mes,
      total_estudiantes: reporte.total_estudiantes,
      casos_criticos: reporte.casos_criticos,
      observaciones: reporte.observaciones
    }
  } else {
    isEditing.value = false
    editingId.value = null
    form.value = {
      psicologo_id: authStore.isPsicologo ? authStore.user.id : null,
      mes: '',
      total_estudiantes: 0,
      casos_criticos: 0,
      observaciones: ''
    }
  }
  showDialog.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  let result
  if (isEditing.value) {
    result = await reportesStore.update(editingId.value, form.value)
  } else {
    result = await reportesStore.create(form.value)
  }

  if (result.success) {
    showDialog.value = false
  }
}

const viewReporte = (reporte) => {
  $q.dialog({
    title: 'Detalles del Reporte',
    message: `
      <strong>Psicólogo:</strong> ${reporte.psicologo?.name}<br>
      <strong>Mes:</strong> ${reporte.mes}<br>
      <strong>Total Estudiantes:</strong> ${reporte.total_estudiantes}<br>
      <strong>Casos Críticos:</strong> ${reporte.casos_criticos}<br><br>
      <strong>Observaciones:</strong><br>${reporte.observaciones || 'Sin observaciones'}
    `,
    html: true
  })
}

const exportarPDF = () => {
  $q.notify({
    type: 'info',
    message: 'Función de exportación en desarrollo',
    position: 'top'
  })
}

const confirmDelete = (reporte) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: '¿Estás seguro de eliminar este reporte?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await reportesStore.delete(reporte.id)
  })
}

onMounted(async () => {
  reportesStore.fetchAll()
  psicologosOptions.value = await usuariosStore.fetchPsicologos()

  // Auto-completar mes actual
  const now = new Date()
  const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
  generarForm.value.mes = `${meses[now.getMonth()]} ${now.getFullYear()}`
})
</script>
