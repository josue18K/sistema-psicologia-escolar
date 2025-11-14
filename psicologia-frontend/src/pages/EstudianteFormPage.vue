<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          {{ esEdicion ? 'Editar Estudiante' : 'Nuevo Estudiante' }}
        </div>
        <div class="text-h6 text-grey-7">
          {{ esEdicion ? 'Actualiza la información del estudiante' : 'Registra un nuevo estudiante en el sistema' }}
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          flat
          icon="arrow_back"
          label="Volver a Estudiantes"
          @click="$router.push('/estudiantes')"
          color="grey"
          size="lg"
        >
          <q-tooltip>Volver a la lista de estudiantes</q-tooltip>
        </q-btn>
      </div>
    </div>

    <q-card class="shadow-1" flat bordered>
      <q-card-section class="q-pa-none">
        <!-- Progress Steps -->
        <q-stepper
          v-model="step"
          ref="stepper"
          color="primary"
          animated
          header-nav
          class="form-stepper"
        >
          <!-- Paso 1: Datos Personales -->
          <q-step
            :name="1"
            title="Datos Personales"
            icon="person"
            :done="step > 1"
            :error="errors.personales"
          >
            <div class="q-pa-xl">
              <div class="text-h5 text-weight-bold q-mb-lg">Información Personal del Estudiante</div>

              <div class="row q-col-gutter-xl">
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="form.dni"
                    label="DNI *"
                    :rules="[
                      val => !!val || 'El DNI es obligatorio',
                      val => val.length === 8 || 'El DNI debe tener 8 dígitos',
                      val => /^\d+$/.test(val) || 'Solo se permiten números'
                    ]"
                    maxlength="8"
                    :readonly="esEdicion"
                    lazy-rules
                    outlined
                    color="primary"
                    class="q-mb-lg"
                    :error="errors.dni"
                    :error-message="errors.dni"
                  >
                    <template v-slot:prepend>
                      <q-icon name="badge" color="primary" />
                    </template>
                    <template v-slot:hint>
                      Ingrese los 8 dígitos del DNI
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.nombres"
                    label="Nombres *"
                    :rules="[
                      val => !!val || 'Los nombres son obligatorios',
                      val => val.length <= 100 || 'Máximo 100 caracteres'
                    ]"
                    lazy-rules
                    outlined
                    color="primary"
                    class="q-mb-lg"
                  >
                    <template v-slot:prepend>
                      <q-icon name="drive_file_rename_outline" color="primary" />
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.apellidos"
                    label="Apellidos *"
                    :rules="[
                      val => !!val || 'Los apellidos son obligatorios',
                      val => val.length <= 100 || 'Máximo 100 caracteres'
                    ]"
                    lazy-rules
                    outlined
                    color="primary"
                    class="q-mb-lg"
                  >
                    <template v-slot:prepend>
                      <q-icon name="drive_file_rename_outline" color="primary" />
                    </template>
                  </q-input>
                </div>

                <div class="col-12 col-md-6">
                  <q-input
                    v-model="form.fecha_nacimiento"
                    label="Fecha de Nacimiento *"
                    type="date"
                    :rules="[val => !!val || 'La fecha de nacimiento es obligatoria']"
                    lazy-rules
                    outlined
                    color="primary"
                    class="q-mb-lg"
                    @update:model-value="calcularEdad"
                  >
                    <template v-slot:prepend>
                      <q-icon name="cake" color="primary" />
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.edad"
                    label="Edad *"
                    type="number"
                    :rules="[
                      val => !!val || 'La edad es obligatoria',
                      val => val >= 3 && val <= 20 || 'La edad debe estar entre 3 y 20 años'
                    ]"
                    lazy-rules
                    outlined
                    color="primary"
                    class="q-mb-lg"
                    readonly
                  >
                    <template v-slot:prepend>
                      <q-icon name="numbers" color="primary" />
                    </template>
                    <template v-slot:hint>
                      Edad calculada automáticamente
                    </template>
                  </q-input>

                  <div class="row q-col-gutter-md">
                    <div class="col-6">
                      <q-select
                        v-model="form.grado"
                        :options="GRADOS"
                        label="Grado *"
                        :rules="[val => !!val || 'El grado es obligatorio']"
                        lazy-rules
                        outlined
                        color="primary"
                        class="q-mb-lg"
                      >
                        <template v-slot:prepend>
                          <q-icon name="class" color="primary" />
                        </template>
                      </q-select>
                    </div>
                    <div class="col-6">
                      <q-select
                        v-model="form.seccion"
                        :options="SECCIONES"
                        label="Sección *"
                        :rules="[val => !!val || 'La sección es obligatoria']"
                        lazy-rules
                        outlined
                        color="primary"
                        class="q-mb-lg"
                      >
                        <template v-slot:prepend>
                          <q-icon name="category" color="primary" />
                        </template>
                      </q-select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <q-stepper-navigation class="q-px-xl q-pb-xl">
              <q-btn
                @click="validarPaso1"
                color="primary"
                label="Continuar a Datos Académicos"
                unelevated
                size="lg"
              />
              <q-btn
                flat
                @click="$router.push('/estudiantes')"
                color="grey"
                label="Cancelar"
                class="q-ml-sm"
                size="lg"
              />
            </q-stepper-navigation>
          </q-step>

          <!-- Paso 2: Datos Académicos y Apoderado -->
          <q-step
            :name="2"
            title="Datos Académicos y Apoderado"
            icon="school"
            :done="step > 2"
            :error="errors.academicos"
          >
            <div class="q-pa-xl">
              <div class="text-h5 text-weight-bold q-mb-lg">Información Académica y Apoderado</div>

              <div class="row q-col-gutter-xl">
                <div class="col-12 col-md-6">
                  <div class="text-h6 text-weight-medium q-mb-md">Asignación de Tutor</div>
                  <q-select
                    v-model="form.tutor_id"
                    :options="tutoresOptions"
                    label="Tutor Asignado"
                    clearable
                    map-options
                    emit-value
                    outlined
                    color="primary"
                    class="q-mb-lg"
                  >
                    <template v-slot:prepend>
                      <q-icon name="person" color="primary" />
                    </template>
                    <template v-slot:hint>
                      Seleccione un tutor para el estudiante (opcional)
                    </template>
                  </q-select>

                  <div class="text-h6 text-weight-medium q-mb-md">Apoderado Existente</div>
                  <q-select
                    v-model="form.apoderado_id"
                    :options="apoderadosOptions"
                    label="Seleccionar Apoderado Existente"
                    clearable
                    map-options
                    emit-value
                    outlined
                    color="primary"
                    class="q-mb-lg"
                    v-if="!form.crear_apoderado"
                  >
                    <template v-slot:prepend>
                      <q-icon name="family_restroom" color="primary" />
                    </template>
                  </q-select>

                  <q-checkbox
                    v-model="form.crear_apoderado"
                    label="Crear nuevo apoderado"
                    color="primary"
                    class="q-mb-lg"
                  />
                </div>

                <div class="col-12 col-md-6" v-if="form.crear_apoderado">
                  <div class="text-h6 text-weight-medium q-mb-md">Datos del Nuevo Apoderado</div>

                  <q-input
                    v-model="form.apoderado_nombre"
                    label="Nombre del Apoderado *"
                    :rules="form.crear_apoderado ? [val => !!val || 'El nombre es obligatorio'] : []"
                    outlined
                    color="primary"
                    class="q-mb-md"
                  >
                    <template v-slot:prepend>
                      <q-icon name="person" color="primary" />
                    </template>
                  </q-input>

                  <q-select
                    v-model="form.apoderado_parentesco"
                    :options="PARENTESCOS"
                    label="Parentesco *"
                    :rules="form.crear_apoderado ? [val => !!val || 'El parentesco es obligatorio'] : []"
                    outlined
                    color="primary"
                    class="q-mb-md"
                  >
                    <template v-slot:prepend>
                      <q-icon name="diversity" color="primary" />
                    </template>
                  </q-select>

                  <q-input
                    v-model="form.apoderado_correo"
                    label="Correo Electrónico"
                    type="email"
                    outlined
                    color="primary"
                    class="q-mb-md"
                  >
                    <template v-slot:prepend>
                      <q-icon name="email" color="primary" />
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.apoderado_telefono"
                    label="Teléfono *"
                    :rules="form.crear_apoderado ? [val => !!val || 'El teléfono es obligatorio'] : []"
                    outlined
                    color="primary"
                    class="q-mb-md"
                  >
                    <template v-slot:prepend>
                      <q-icon name="phone" color="primary" />
                    </template>
                  </q-input>
                </div>
              </div>

              <!-- Resumen del Estudiante -->
              <q-card flat bordered class="bg-blue-1 q-mt-lg">
                <q-card-section>
                  <div class="text-h6 text-weight-medium">Resumen del Estudiante</div>
                  <div class="row q-gutter-y-sm">
                    <div class="col-12 col-sm-6">
                      <strong>Nombre:</strong> {{ form.nombres }} {{ form.apellidos }}
                    </div>
                    <div class="col-12 col-sm-6">
                      <strong>DNI:</strong> {{ form.dni }}
                    </div>
                    <div class="col-12 col-sm-6">
                      <strong>Grado y Sección:</strong> {{ form.grado }} {{ form.seccion }}
                    </div>
                    <div class="col-12 col-sm-6">
                      <strong>Edad:</strong> {{ form.edad }} años
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>

            <q-stepper-navigation class="q-px-xl q-pb-xl">
              <q-btn
                @click="step = 1"
                color="grey"
                label="Volver"
                flat
                class="q-mr-sm"
                size="lg"
              />
              <q-btn
                @click="onSubmit"
                color="primary"
                :label="esEdicion ? 'Actualizar Estudiante' : 'Registrar Estudiante'"
                :loading="loading"
                unelevated
                size="lg"
              />
            </q-stepper-navigation>
          </q-step>
        </q-stepper>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { estudianteService, userService, apoderadoService } from 'src/services/api'
import { GRADOS, SECCIONES, PARENTESCOS } from 'src/utils/constants'
import { calculateAge } from 'src/utils/helpers'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()

const loading = ref(false)
const step = ref(1)
const tutores = ref([])
const apoderados = ref([])
const errors = ref({
  personales: false,
  academicos: false,
  dni: ''
})

const form = ref({
  dni: '',
  nombres: '',
  apellidos: '',
  fecha_nacimiento: '',
  edad: '',
  grado: '',
  seccion: '',
  tutor_id: null,
  apoderado_id: null,
  crear_apoderado: false,
  apoderado_nombre: '',
  apoderado_parentesco: '',
  apoderado_correo: '',
  apoderado_telefono: ''
})

// Computed
const esEdicion = computed(() => route.name === 'estudiante-editar')
const estudianteDni = computed(() => route.params.dni)

const tutoresOptions = computed(() =>
  tutores.value.map(t => ({ label: t.name, value: t.id }))
)

const apoderadosOptions = computed(() =>
  apoderados.value.map(a => ({ label: `${a.nombre} (${a.parentesco})`, value: a.id }))
)

// Methods
async function loadTutores() {
  try {
    const response = await userService.getTutores()
    tutores.value = response.data.tutores
  } catch (error) {
    console.error('Error cargando tutores:', error)
  }
}

async function loadApoderados() {
  try {
    const response = await apoderadoService.getApoderados()
    apoderados.value = response.data.apoderados
  } catch (error) {
    console.error('Error cargando apoderados:', error)
  }
}

async function loadEstudiante() {
  if (!esEdicion.value) return

  try {
    const response = await estudianteService.getEstudiante(estudianteDni.value)
    const estudiante = response.data.estudiante

    Object.keys(form.value).forEach(key => {
      if (estudiante[key] !== undefined) {
        form.value[key] = estudiante[key]
      }
    })
  } catch (error) {
    console.error('Error cargando estudiante:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando datos del estudiante',
      position: 'top'
    })
    router.push('/estudiantes')
  }
}

function calcularEdad() {
  if (form.value.fecha_nacimiento) {
    form.value.edad = calculateAge(form.value.fecha_nacimiento)
  }
}

function validarPaso1() {
  errors.value.personales = false
  errors.value.dni = ''

  if (!form.value.dni || !form.value.nombres || !form.value.apellidos ||
      !form.value.fecha_nacimiento || !form.value.edad || !form.value.grado || !form.value.seccion) {
    errors.value.personales = true
    $q.notify({
      type: 'warning',
      message: 'Por favor complete todos los campos obligatorios',
      position: 'top'
    })
    return
  }

  if (form.value.dni.length !== 8) {
    errors.value.dni = 'El DNI debe tener exactamente 8 dígitos'
    errors.value.personales = true
    return
  }

  if (!/^\d+$/.test(form.value.dni)) {
    errors.value.dni = 'El DNI solo debe contener números'
    errors.value.personales = true
    return
  }

  step.value = 2
}

async function onSubmit() {
  loading.value = true
  try {
    if (esEdicion.value) {
      await estudianteService.updateEstudiante(estudianteDni.value, form.value)
      $q.notify({
        type: 'positive',
        message: 'Estudiante actualizado exitosamente',
        position: 'top'
      })
    } else {
      await estudianteService.createEstudiante(form.value)
      $q.notify({
        type: 'positive',
        message: 'Estudiante registrado exitosamente',
        position: 'top'
      })
    }

    router.push('/estudiantes')
  } catch (error) {
    console.error('Error guardando estudiante:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error guardando estudiante',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

// Watchers
watch(() => form.value.crear_apoderado, (newVal) => {
  if (newVal) {
    form.value.apoderado_id = null
  }
})

// Lifecycle
onMounted(async () => {
  await Promise.all([loadTutores(), loadApoderados()])
  if (esEdicion.value) {
    await loadEstudiante()
  }
})
</script>

<style scoped>
.form-stepper {
  border-radius: 12px;
}

.form-stepper :deep(.q-stepper__step-inner) {
  padding: 0;
}

.form-stepper :deep(.q-stepper__tab) {
  padding: 20px;
}

.form-stepper :deep(.q-stepper__tab--active) {
  background-color: #e3f2fd;
}
</style>
