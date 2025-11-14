<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Tabs para vista -->
      <q-tabs
        v-model="tab"
        dense
        class="text-grey q-mb-md"
        active-color="primary"
        indicator-color="primary"
        align="justify"
      >
        <q-tab name="tabla" label="Lista" icon="list" />
        <q-tab name="calendario" label="Calendario" icon="calendar_month" />
      </q-tabs>

      <q-tab-panels v-model="tab" animated>
        <!-- Vista de Tabla -->
        <q-tab-panel name="tabla">
          <data-table
            :rows="citasStore.citas"
            :columns="columns"
            :loading="citasStore.loading"
            title="Citas Programadas"
            add-button-label="Nueva Cita"
            @add="openDialog()"
          >
            <template v-slot:body-cell-estado="props">
              <q-td :props="props">
                <q-badge
                  :color="getEstadoColor(props.row.estado)"
                  :label="props.row.estado"
                />
              </q-td>
            </template>

            <template v-slot:actions="{ row }">
              <q-btn
                v-if="row.estado === 'PENDIENTE'"
                flat
                dense
                round
                icon="check_circle"
                color="positive"
                @click="marcarRealizada(row)"
              >
                <q-tooltip>Marcar realizada</q-tooltip>
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
        </q-tab-panel>

        <!-- Vista de Calendario -->
        <q-tab-panel name="calendario">
          <div class="text-center q-pa-xl">
            <q-icon name="calendar_month" size="64px" color="grey-5" />
            <p class="text-grey-7">Vista de calendario en desarrollo</p>
            <p class="text-caption text-grey-6">
              Se integrará con @quasar/quasar-ui-qcalendar
            </p>
          </div>
        </q-tab-panel>
      </q-tab-panels>

      <!-- Dialog formulario -->
      <dialog-form
        v-model="showDialog"
        :title="isEditing ? 'Editar Cita' : 'Nueva Cita'"
        :loading="citasStore.loading"
        @submit="handleSubmit"
      >
        <q-form ref="formRef" class="q-gutter-md">
          <!-- Estudiante -->
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

          <!-- Fecha y hora -->
          <q-input
            v-model="form.fecha_cita"
            label="Fecha y hora de la cita *"
            outlined
            dense
            type="datetime-local"
            :rules="[val => !!val || 'La fecha es obligatoria']"
          />

          <!-- Motivo -->
          <q-input
            v-model="form.motivo"
            label="Motivo de la cita *"
            outlined
            dense
            type="textarea"
            rows="4"
            :rules="[val => !!val || 'El motivo es obligatorio']"
          />

          <!-- Estado -->
          <q-select
            v-if="isEditing"
            v-model="form.estado"
            :options="estadoOptions"
            label="Estado"
            outlined
            dense
            emit-value
            map-options
          />
        </q-form>
      </dialog-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCitasStore } from 'stores/citas'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'
import DialogForm from 'components/DialogForm.vue'

const citasStore = useCitasStore()
const estudiantesStore = useEstudiantesStore()
const $q = useQuasar()
const formRef = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const tab = ref('tabla')

const estudiantesOptions = ref([])

const form = ref({
  dni_estudiante: '',
  fecha_cita: '',
  motivo: '',
  estado: 'PENDIENTE'
})

const estadoOptions = [
  { label: 'Pendiente', value: 'PENDIENTE' },
  { label: 'Realizada', value: 'REALIZADA' },
  { label: 'Cancelada', value: 'CANCELADA' }
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
  {
    name: 'fecha_cita',
    label: 'Fecha/Hora',
    field: 'fecha_cita',
    align: 'center',
    sortable: true,
    format: val => new Date(val).toLocaleString('es-PE')
  },
  { name: 'motivo', label: 'Motivo', field: 'motivo', align: 'left' },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center', sortable: true },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const getEstadoColor = (estado) => {
  switch (estado) {
    case 'PENDIENTE': return 'warning'
    case 'REALIZADA': return 'positive'
    case 'CANCELADA': return 'negative'
    default: return 'grey'
  }
}

const openDialog = (cita = null) => {
  if (cita) {
    isEditing.value = true
    editingId.value = cita.id
    form.value = {
      dni_estudiante: cita.dni_estudiante,
      fecha_cita: cita.fecha_cita.replace(' ', 'T').substring(0, 16),
      motivo: cita.motivo,
      estado: cita.estado
    }
  } else {
    isEditing.value = false
    editingId.value = null
    form.value = {
      dni_estudiante: '',
      fecha_cita: '',
      motivo: '',
      estado: 'PENDIENTE'
    }
  }
  showDialog.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  let result
  if (isEditing.value) {
    result = await citasStore.update(editingId.value, form.value)
  } else {
    result = await citasStore.create(form.value)
  }

  if (result.success) {
    showDialog.value = false
  }
}

const marcarRealizada = async (cita) => {
  $q.dialog({
    title: 'Confirmar',
    message: '¿Marcar esta cita como realizada?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await citasStore.marcarRealizada(cita.id)
  })
}

const confirmDelete = (cita) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: '¿Estás seguro de eliminar esta cita?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await citasStore.delete(cita.id)
  })
}

onMounted(async () => {
  citasStore.fetchAll()
  await estudiantesStore.fetchAll()

  estudiantesOptions.value = estudiantesStore.estudiantes.map(e => ({
    dni: e.dni,
    nombre_completo: `${e.nombres} ${e.apellidos} (${e.dni})`
  }))
})
</script>
