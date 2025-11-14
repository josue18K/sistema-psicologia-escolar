<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Tabla de estudiantes -->
      <data-table
        :rows="estudiantesStore.estudiantes"
        :columns="columns"
        :loading="estudiantesStore.loading"
        title="Gestión de Estudiantes"
        add-button-label="Nuevo Estudiante"
        @add="openDialog()"
      >
        <template v-slot:actions="{ row }">
          <q-btn
            flat
            dense
            round
            icon="visibility"
            color="primary"
            @click="viewStudent(row)"
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
      <q-dialog
        v-model="showDialog"
        persistent
        maximized
        transition-show="slide-up"
        transition-hide="slide-down"
      >
        <q-card>
          <q-bar class="bg-primary text-white">
            <q-icon name="school" />
            <div>{{ isEditing ? 'Editar Estudiante' : 'Nuevo Estudiante' }}</div>
            <q-space />
            <q-btn dense flat icon="close" @click="closeDialog" />
          </q-bar>

          <q-card-section>
            <q-form ref="formRef" class="q-gutter-md">
              <div class="row q-col-gutter-md">
                <!-- DATOS DEL ESTUDIANTE -->
                <div class="col-12">
                  <div class="text-h6 text-primary">Datos del Estudiante</div>
                  <q-separator class="q-my-sm" />
                </div>

                <!-- DNI -->
                <div class="col-xs-12 col-sm-6 col-md-4">
                  <q-input
                    v-model="form.dni"
                    label="DNI *"
                    outlined
                    dense
                    mask="########"
                    :disable="isEditing"
                    :rules="[
                      val => !!val || 'El DNI es obligatorio',
                      val => val.length === 8 || 'El DNI debe tener 8 dígitos'
                    ]"
                  />
                </div>

                <!-- Nombres -->
                <div class="col-xs-12 col-sm-6 col-md-4">
                  <q-input
                    v-model="form.nombres"
                    label="Nombres *"
                    outlined
                    dense
                    :rules="[val => !!val || 'Los nombres son obligatorios']"
                  />
                </div>

                <!-- Apellidos -->
                <div class="col-xs-12 col-sm-6 col-md-4">
                  <q-input
                    v-model="form.apellidos"
                    label="Apellidos *"
                    outlined
                    dense
                    :rules="[val => !!val || 'Los apellidos son obligatorios']"
                  />
                </div>

                <!-- Fecha de nacimiento -->
                <div class="col-xs-12 col-sm-6 col-md-4">
                  <q-input
                    v-model="form.fecha_nacimiento"
                    label="Fecha de Nacimiento *"
                    outlined
                    dense
                    type="date"
                    :rules="[val => !!val || 'La fecha es obligatoria']"
                  />
                </div>

                <!-- Edad -->
                <div class="col-xs-12 col-sm-6 col-md-2">
                  <q-input
                    v-model.number="form.edad"
                    label="Edad *"
                    outlined
                    dense
                    type="number"
                    :rules="[
                      val => !!val || 'La edad es obligatoria',
                      val => val >= 3 && val <= 20 || 'Edad entre 3 y 20 años'
                    ]"
                  />
                </div>

                <!-- Grado -->
                <div class="col-xs-12 col-sm-6 col-md-3">
                  <q-select
                    v-model="form.grado"
                    :options="gradosOptions"
                    label="Grado *"
                    outlined
                    dense
                    emit-value
                    map-options
                    :rules="[val => !!val || 'El grado es obligatorio']"
                  />
                </div>

                <!-- Sección -->
                <div class="col-xs-12 col-sm-6 col-md-3">
                  <q-select
                    v-model="form.seccion"
                    :options="seccionesOptions"
                    label="Sección *"
                    outlined
                    dense
                    emit-value
                    map-options
                    :rules="[val => !!val || 'La sección es obligatoria']"
                  />
                </div>

                <!-- Tutor -->
                <div class="col-xs-12 col-sm-6 col-md-6">
                  <q-select
                    v-model="form.tutor_id"
                    :options="tutoresOptions"
                    label="Tutor (opcional)"
                    outlined
                    dense
                    emit-value
                    map-options
                    option-value="id"
                    option-label="name"
                    clearable
                  />
                </div>

                <!-- DATOS DEL APODERADO -->
                <div class="col-12 q-mt-md">
                  <div class="text-h6 text-primary">Datos del Apoderado</div>
                  <q-separator class="q-my-sm" />
                </div>

                <!-- Opción: Crear nuevo o seleccionar existente -->
                <div class="col-12">
                  <q-radio
                    v-model="apoderadoOption"
                    val="nuevo"
                    label="Crear nuevo apoderado"
                  />
                  <q-radio
                    v-model="apoderadoOption"
                    val="existente"
                    label="Seleccionar apoderado existente"
                    class="q-ml-md"
                  />
                </div>

                <!-- Seleccionar apoderado existente -->
                <div v-if="apoderadoOption === 'existente'" class="col-12">
                  <q-select
                    v-model="form.apoderado_id"
                    :options="apoderadosOptions"
                    label="Apoderado"
                    outlined
                    dense
                    emit-value
                    map-options
                    option-value="id"
                    option-label="nombre"
                    clearable
                  />
                </div>

                <!-- Crear nuevo apoderado -->
                <template v-if="apoderadoOption === 'nuevo'">
                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <q-input
                      v-model="form.apoderado_nombre"
                      label="Nombre del Apoderado *"
                      outlined
                      dense
                      :rules="[val => !!val || 'El nombre es obligatorio']"
                    />
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <q-select
                      v-model="form.apoderado_parentesco"
                      :options="parentescoOptions"
                      label="Parentesco *"
                      outlined
                      dense
                      emit-value
                      map-options
                      :rules="[val => !!val || 'El parentesco es obligatorio']"
                    />
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <q-input
                      v-model="form.apoderado_telefono"
                      label="Teléfono *"
                      outlined
                      dense
                      mask="### ### ###"
                      :rules="[val => !!val || 'El teléfono es obligatorio']"
                    />
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6">
                    <q-input
                      v-model="form.apoderado_correo"
                      label="Correo (opcional)"
                      outlined
                      dense
                      type="email"
                    />
                  </div>
                </template>
              </div>
            </q-form>
          </q-card-section>

          <q-card-actions align="right" class="q-px-md q-pb-md">
            <q-btn
              label="Cancelar"
              color="grey-7"
              flat
              @click="closeDialog"
            />
            <q-btn
              label="Guardar"
              color="primary"
              :loading="estudiantesStore.loading"
              @click="handleSubmit"
              unelevated
            />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useApoderadosStore } from 'stores/apoderados'
import { useUsuariosStore } from 'stores/usuarios'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'

const estudiantesStore = useEstudiantesStore()
const apoderadosStore = useApoderadosStore()
const usuariosStore = useUsuariosStore()
const $q = useQuasar()
const formRef = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const apoderadoOption = ref('nuevo')

const tutoresOptions = ref([])
const apoderadosOptions = ref([])

const form = ref({
  dni: '',
  nombres: '',
  apellidos: '',
  fecha_nacimiento: '',
  edad: null,
  grado: '',
  seccion: '',
  tutor_id: null,
  apoderado_id: null,
  crear_apoderado: true,
  apoderado_nombre: '',
  apoderado_parentesco: '',
  apoderado_telefono: '',
  apoderado_correo: ''
})

const gradosOptions = [
  { label: '1ro', value: '1ro' },
  { label: '2do', value: '2do' },
  { label: '3ro', value: '3ro' },
  { label: '4to', value: '4to' },
  { label: '5to', value: '5to' },
  { label: '6to', value: '6to' }
]

const seccionesOptions = [
  { label: 'A', value: 'A' },
  { label: 'B', value: 'B' },
  { label: 'C', value: 'C' },
  { label: 'D', value: 'D' }
]

const parentescoOptions = [
  { label: 'Madre', value: 'Madre' },
  { label: 'Padre', value: 'Padre' },
  { label: 'Tutor Legal', value: 'Tutor Legal' },
  { label: 'Abuelo/a', value: 'Abuelo/a' },
  { label: 'Tío/a', value: 'Tío/a' },
  { label: 'Otro', value: 'Otro' }
]

const columns = [
  { name: 'dni', label: 'DNI', field: 'dni', align: 'left', sortable: true },
  { name: 'nombres', label: 'Nombres', field: 'nombres', align: 'left', sortable: true },
  { name: 'apellidos', label: 'Apellidos', field: 'apellidos', align: 'left', sortable: true },
  { name: 'edad', label: 'Edad', field: 'edad', align: 'center', sortable: true },
  { name: 'grado', label: 'Grado', field: 'grado', align: 'center', sortable: true },
  { name: 'seccion', label: 'Sección', field: 'seccion', align: 'center', sortable: true },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const openDialog = (estudiante = null) => {
  if (estudiante) {
    isEditing.value = true
    form.value = {
      dni: estudiante.dni,
      nombres: estudiante.nombres,
      apellidos: estudiante.apellidos,
      fecha_nacimiento: estudiante.fecha_nacimiento,
      edad: estudiante.edad,
      grado: estudiante.grado,
      seccion: estudiante.seccion,
      tutor_id: estudiante.tutor_id,
      apoderado_id: estudiante.apoderado_id
    }
    apoderadoOption.value = 'existente'
  } else {
    isEditing.value = false
    apoderadoOption.value = 'nuevo'
    resetForm()
  }
  showDialog.value = true
}

const closeDialog = () => {
  showDialog.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    dni: '',
    nombres: '',
    apellidos: '',
    fecha_nacimiento: '',
    edad: null,
    grado: '',
    seccion: '',
    tutor_id: null,
    apoderado_id: null,
    crear_apoderado: true,
    apoderado_nombre: '',
    apoderado_parentesco: '',
    apoderado_telefono: '',
    apoderado_correo: ''
  }
  formRef.value?.reset()
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  const submitData = { ...form.value }

  // Configurar si crear apoderado nuevo
  if (apoderadoOption.value === 'nuevo') {
    submitData.crear_apoderado = true
  } else {
    submitData.crear_apoderado = false
    delete submitData.apoderado_nombre
    delete submitData.apoderado_parentesco
    delete submitData.apoderado_telefono
    delete submitData.apoderado_correo
  }

  let result
  if (isEditing.value) {
    result = await estudiantesStore.update(form.value.dni, submitData)
  } else {
    result = await estudiantesStore.create(submitData)
  }

  if (result.success) {
    closeDialog()
  }
}

const viewStudent = (student) => {
  $q.dialog({
    title: 'Detalles del Estudiante',
    message: `
      <strong>DNI:</strong> ${student.dni}<br>
      <strong>Nombre:</strong> ${student.nombres} ${student.apellidos}<br>
      <strong>Edad:</strong> ${student.edad} años<br>
      <strong>Grado:</strong> ${student.grado} - Sección ${student.seccion}
    `,
    html: true
  })
}

const confirmDelete = (student) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar al estudiante "${student.nombres} ${student.apellidos}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await estudiantesStore.delete(student.dni)
  })
}

onMounted(async () => {
  estudiantesStore.fetchAll()
  apoderadosStore.fetchAll()

  // Cargar tutores
  tutoresOptions.value = await usuariosStore.fetchTutores()
  apoderadosOptions.value = apoderadosStore.apoderados
})
</script>
