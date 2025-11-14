<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Tabla de anamnesis -->
      <data-table
        :rows="anamnesisStore.anamnesisList"
        :columns="columns"
        :loading="anamnesisStore.loading"
        title="Registro de Anamnesis"
        add-button-label="Nueva Anamnesis"
        @add="openDialog()"
      >
        <template v-slot:actions="{ row }">
          <q-btn
            flat
            dense
            round
            icon="visibility"
            color="primary"
            @click="viewAnamnesis(row)"
          >
            <q-tooltip>Ver completo</q-tooltip>
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

      <!-- Dialog formulario multi-step -->
      <q-dialog
        v-model="showDialog"
        persistent
        maximized
        transition-show="slide-up"
        transition-hide="slide-down"
      >
        <q-card>
          <q-bar class="bg-primary text-white">
            <q-icon name="description" />
            <div>{{ isEditing ? 'Editar Anamnesis' : 'Nueva Anamnesis' }}</div>
            <q-space />
            <q-btn dense flat icon="close" @click="closeDialog" />
          </q-bar>

          <q-card-section>
            <q-stepper
              v-model="step"
              ref="stepperRef"
              color="primary"
              animated
              header-nav
              :contracted="$q.screen.lt.md"
            >
              <!-- PASO 1: Datos Generales -->
              <q-step
                :name="1"
                title="Datos Generales"
                icon="person"
                :done="step > 1"
              >
                <q-form ref="form1Ref" class="q-gutter-md">
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
                        @update:model-value="checkExistingAnamnesis"
                      >
                        <template v-slot:prepend>
                          <q-icon name="school" />
                        </template>
                      </q-select>
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
                      >
                        <template v-slot:prepend>
                          <q-icon name="psychology" />
                        </template>
                      </q-select>
                    </div>

                    <!-- Fecha de evaluación -->
                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.fecha_evaluacion"
                        label="Fecha de Evaluación *"
                        outlined
                        dense
                        type="date"
                        :rules="[val => !!val || 'La fecha es obligatoria']"
                      >
                        <template v-slot:prepend>
                          <q-icon name="event" />
                        </template>
                      </q-input>
                    </div>
                  </div>
                </q-form>
              </q-step>

              <!-- PASO 2: Datos Familiares -->
              <q-step
                :name="2"
                title="Datos Familiares"
                icon="family_restroom"
                :done="step > 2"
              >
                <q-form ref="form2Ref" class="q-gutter-md">
                  <div class="row q-col-gutter-md">
                    <div class="col-12">
                      <div class="text-h6 text-primary">Composición Familiar</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.numero_hijos"
                        label="Número de hijos en la familia"
                        outlined
                        dense
                        type="number"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.lugar_ocupa"
                        label="Lugar que ocupa el alumno"
                        outlined
                        dense
                        type="number"
                        hint="Orden de nacimiento"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.personas_vive_con"
                        label="Personas con quien vive"
                        outlined
                        dense
                      />
                    </div>

                    <!-- Datos del Padre -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Datos del Padre</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.padre_nombre"
                        label="Nombre del padre"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-2">
                      <q-input
                        v-model.number="form.padre_edad"
                        label="Edad"
                        outlined
                        dense
                        type="number"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.padre_escolaridad"
                        label="Escolaridad"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.padre_ocupacion"
                        label="Ocupación"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-select
                        v-model="form.relacion_padre"
                        :options="relacionOptions"
                        label="Relación con el padre"
                        outlined
                        dense
                        emit-value
                        map-options
                      />
                    </div>

                    <!-- Datos de la Madre -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Datos de la Madre</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.madre_nombre"
                        label="Nombre de la madre"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-2">
                      <q-input
                        v-model.number="form.madre_edad"
                        label="Edad"
                        outlined
                        dense
                        type="number"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.madre_escolaridad"
                        label="Escolaridad"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.madre_ocupacion"
                        label="Ocupación"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-select
                        v-model="form.relacion_madre"
                        :options="relacionOptions"
                        label="Relación con la madre"
                        outlined
                        dense
                        emit-value
                        map-options
                      />
                    </div>

                    <!-- Relaciones Familiares -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Otras Relaciones Familiares</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-select
                        v-model="form.relacion_hermanos"
                        :options="relacionOptions"
                        label="Relación con hermanos"
                        outlined
                        dense
                        emit-value
                        map-options
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-select
                        v-model="form.relacion_otros_familiares"
                        :options="relacionOptions"
                        label="Relación con otros familiares"
                        outlined
                        dense
                        emit-value
                        map-options
                      />
                    </div>
                  </div>
                </q-form>
              </q-step>

              <!-- PASO 3: Antecedentes -->
              <q-step
                :name="3"
                title="Antecedentes"
                icon="history"
                :done="step > 3"
              >
                <q-form ref="form3Ref" class="q-gutter-md">
                  <div class="row q-col-gutter-md">
                    <!-- Antecedentes Escolares -->
                    <div class="col-12">
                      <div class="text-h6 text-primary">Antecedentes Escolares</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.inicial_edad_ingreso"
                        label="Inicial - Edad de ingreso"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.primaria_edad_ingreso"
                        label="Primaria - Edad de ingreso"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model="form.secundaria_edad_ingreso"
                        label="Secundaria - Edad de ingreso"
                        outlined
                        dense
                      />
                    </div>

                    <!-- Antecedentes Prenatales -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Antecedentes Prenatales</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.madre_enfermedades_embarazo"
                        label="Enfermedades de la madre durante el embarazo"
                        outlined
                        dense
                        type="textarea"
                        rows="2"
                      />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.madre_medicamentos_embarazo"
                        label="Medicamentos durante el embarazo"
                        outlined
                        dense
                        type="textarea"
                        rows="2"
                      />
                    </div>

                    <!-- Datos del Parto -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Datos del Parto</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-3">
                      <q-input
                        v-model.number="form.parto_semanas"
                        label="Semanas de gestación"
                        outlined
                        dense
                        type="number"
                      />
                    </div>

                    <div class="col-12 col-md-3">
                      <q-input
                        v-model.number="form.parto_peso"
                        label="Peso (kg)"
                        outlined
                        dense
                        type="number"
                        step="0.1"
                      />
                    </div>

                    <div class="col-12 col-md-3">
                      <q-input
                        v-model.number="form.parto_talla"
                        label="Talla (cm)"
                        outlined
                        dense
                        type="number"
                        step="0.1"
                      />
                    </div>

                    <div class="col-12 col-md-3">
                      <q-input
                        v-model.number="form.parto_apgar"
                        label="Apgar (0-10)"
                        outlined
                        dense
                        type="number"
                        min="0"
                        max="10"
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.estado_nutricional"
                        label="Estado nutricional"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-input
                        v-model="form.estado_emocional"
                        label="Estado emocional"
                        outlined
                        dense
                      />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.tratamientos_parto"
                        label="Tratamientos posteriores al parto"
                        outlined
                        dense
                        type="textarea"
                        rows="2"
                      />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.enfermedades_importantes"
                        label="Enfermedades importantes"
                        outlined
                        dense
                        type="textarea"
                        rows="2"
                      />
                    </div>
                  </div>
                </q-form>
              </q-step>

              <!-- PASO 4: Desarrollo -->
              <q-step
                :name="4"
                title="Desarrollo"
                icon="child_care"
                :done="step > 4"
              >
                <q-form ref="form4Ref" class="q-gutter-md">
                  <div class="row q-col-gutter-md">
                    <!-- Desarrollo Psicomotor -->
                    <div class="col-12">
                      <div class="text-h6 text-primary">Desarrollo Psicomotor (edad en meses)</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_controlo_cabeza"
                        label="Controló la cabeza"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_se_sento"
                        label="Se sentó"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_se_paro"
                        label="Se paró"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_camino"
                        label="Caminó"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_controlo_esfinter"
                        label="Controló esfínter"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_durmio_solo"
                        label="Durmió solo"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <!-- Desarrollo del Lenguaje -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Desarrollo del Lenguaje (edad en meses)</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_primeras_palabras"
                        label="Primeras palabras"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_oraciones"
                        label="Dijo oraciones"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>

                    <div class="col-12 col-md-4">
                      <q-input
                        v-model.number="form.edad_comprendio_instrucciones"
                        label="Comprendió instrucciones"
                        outlined
                        dense
                        type="number"
                        suffix="meses"
                      />
                    </div>
                  </div>
                </q-form>
              </q-step>

              <!-- PASO 5: Evaluaciones y Hábitos -->
              <q-step
                :name="5"
                title="Evaluaciones y Hábitos"
                icon="assessment"
              >
                <q-form ref="form5Ref" class="q-gutter-md">
                  <div class="row q-col-gutter-md">
                    <!-- Evaluaciones Anteriores -->
                    <div class="col-12">
                      <div class="text-h6 text-primary">Evaluaciones Anteriores</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.evaluacion_neurologica"
                        label="Evaluación Neurológica"
                      />
                      <q-input
                        v-if="form.evaluacion_neurologica"
                        v-model="form.evaluacion_neurologica_fecha"
                        label="Fecha aproximada"
                        outlined
                        dense
                        type="date"
                        class="q-mt-sm"
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.evaluacion_psicologica"
                        label="Evaluación Psicológica"
                      />
                      <q-input
                        v-if="form.evaluacion_psicologica"
                        v-model="form.evaluacion_psicologica_fecha"
                        label="Fecha aproximada"
                        outlined
                        dense
                        type="date"
                        class="q-mt-sm"
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.evaluacion_psiquiatrica"
                        label="Evaluación Psiquiátrica"
                      />
                      <q-input
                        v-if="form.evaluacion_psiquiatrica"
                        v-model="form.evaluacion_psiquiatrica_fecha"
                        label="Fecha aproximada"
                        outlined
                        dense
                        type="date"
                        class="q-mt-sm"
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.evaluacion_psicopedagogica"
                        label="Evaluación Psicopedagógica"
                      />
                      <q-input
                        v-if="form.evaluacion_psicopedagogica"
                        v-model="form.evaluacion_psicopedagogica_fecha"
                        label="Fecha aproximada"
                        outlined
                        dense
                        type="date"
                        class="q-mt-sm"
                      />
                    </div>

                    <!-- Hábitos -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Hábitos</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.problemas_dormir"
                        label="¿Tiene problemas para dormir?"
                      />
                      <q-input
                        v-model.number="form.horas_sueno"
                        label="Horas de sueño"
                        outlined
                        dense
                        type="number"
                        class="q-mt-sm"
                      />
                    </div>

                    <div class="col-12 col-md-6">
                      <q-checkbox
                        v-model="form.habitos_estudio"
                        label="¿Posee hábitos de estudio?"
                      />
                      <q-input
                        v-if="form.habitos_estudio"
                        v-model="form.cuales_habitos_estudio"
                        label="¿Cuáles?"
                        outlined
                        dense
                        type="textarea"
                        rows="2"
                        class="q-mt-sm"
                      />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.actividades_intereses"
                        label="Actividades habituales e intereses"
                        outlined
                        dense
                        type="textarea"
                        rows="3"
                      />
                    </div>

                    <!-- Otros -->
                    <div class="col-12 q-mt-md">
                      <div class="text-h6 text-primary">Otros</div>
                      <q-separator class="q-my-sm" />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.autoestima_tolerancia_frustracion"
                        label="Autoestima y tolerancia a la frustración"
                        outlined
                        dense
                        type="textarea"
                        rows="3"
                      />
                    </div>

                    <div class="col-12">
                      <q-input
                        v-model="form.observaciones"
                        label="Observaciones generales"
                        outlined
                        dense
                        type="textarea"
                        rows="4"
                      />
                    </div>
                  </div>
                </q-form>
              </q-step>

              <!-- Navegación -->
              <template v-slot:navigation>
                <q-stepper-navigation>
                  <q-btn
                    v-if="step < 5"
                    @click="nextStep"
                    color="primary"
                    label="Continuar"
                  />
                  <q-btn
                    v-if="step === 5"
                    @click="handleSubmit"
                    color="primary"
                    label="Guardar Anamnesis"
                    :loading="anamnesisStore.loading"
                  />
                  <q-btn
                    v-if="step > 1"
                    flat
                    color="primary"
                    @click="step--"
                    label="Atrás"
                    class="q-ml-sm"
                  />
                </q-stepper-navigation>
              </template>
            </q-stepper>
          </q-card-section>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAnamnesisStore } from 'stores/anamnesis'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useUsuariosStore } from 'stores/usuarios'
import { useAuthStore } from 'stores/auth'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'

const anamnesisStore = useAnamnesisStore()
const estudiantesStore = useEstudiantesStore()
const usuariosStore = useUsuariosStore()
const authStore = useAuthStore()
const $q = useQuasar()

const stepperRef = ref(null)
const form1Ref = ref(null)
const form2Ref = ref(null)
const form3Ref = ref(null)
const form4Ref = ref(null)
const form5Ref = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const step = ref(1)

const estudiantesOptions = ref([])
const psicologosOptions = ref([])

const form = ref({
  dni_estudiante: '',
  psicologo_id: null,
  fecha_evaluacion: '',
  // Datos familiares
  numero_hijos: null,
  personas_vive_con: '',
  lugar_ocupa: null,
  // Padre
  padre_nombre: '',
  padre_edad: null,
  padre_escolaridad: '',
  padre_ocupacion: '',
  relacion_padre: null,
  // Madre
  madre_nombre: '',
  madre_edad: null,
  madre_escolaridad: '',
  madre_ocupacion: '',
  relacion_madre: null,
  // Relaciones
  relacion_hermanos: null,
  relacion_otros_familiares: null,
  // Antecedentes escolares
  inicial_edad_ingreso: '',
  primaria_edad_ingreso: '',
  secundaria_edad_ingreso: '',
  // Antecedentes prenatales
  madre_enfermedades_embarazo: '',
  madre_medicamentos_embarazo: '',
  // Parto
  sintomas_perdida: '',
  estado_nutricional: '',
  estado_emocional: '',
  parto_semanas: null,
  parto_peso: null,
  parto_talla: null,
  parto_apgar: null,
  tratamientos_parto: '',
  enfermedades_importantes: '',
  // Desarrollo psicomotor
  edad_controlo_cabeza: null,
  edad_se_sento: null,
  edad_se_paro: null,
  edad_camino: null,
  edad_controlo_esfinter: null,
  edad_durmio_solo: null,
  // Desarrollo del lenguaje
  edad_primeras_palabras: null,
  edad_oraciones: null,
  edad_comprendio_instrucciones: null,
  // Evaluaciones anteriores
  evaluacion_neurologica: false,
  evaluacion_neurologica_fecha: null,
  evaluacion_psicologica: false,
  evaluacion_psicologica_fecha: null,
  evaluacion_psiquiatrica: false,
  evaluacion_psiquiatrica_fecha: null,
  evaluacion_psicopedagogica: false,
  evaluacion_psicopedagogica_fecha: null,
  // Hábitos
  problemas_dormir: false,
  horas_sueno: null,
  habitos_estudio: false,
  cuales_habitos_estudio: '',
  actividades_intereses: '',
  // Otros
  autoestima_tolerancia_frustracion: '',
  observaciones: ''
})

const relacionOptions = [
  { label: 'Excelente', value: 'EXCELENTE' },
  { label: 'Buena', value: 'BUENA' },
  { label: 'Regular', value: 'REGULAR' },
  { label: 'Mala', value: 'MALA' }
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
    name: 'psicologo',
    label: 'Psicólogo',
    field: row => row.psicologo?.name || 'N/A',
    align: 'left',
    sortable: true
  },
  {
    name: 'fecha_evaluacion',
    label: 'Fecha',
    field: 'fecha_evaluacion',
    align: 'center',
    sortable: true
  },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const checkExistingAnamnesis = async (dni) => {
  if (!dni) return

  const result = await anamnesisStore.verificarExistencia(dni)
  if (result.existe) {
    $q.dialog({
      title: 'Anamnesis existente',
      message: `Este estudiante ya tiene una anamnesis registrada el ${result.anamnesis.fecha_evaluacion}. ¿Deseas crear una nueva de todas formas?`,
      cancel: true,
      persistent: true
    }).onCancel(() => {
      form.value.dni_estudiante = ''
    })
  }
}

const nextStep = async () => {
  let valid = false

  switch(step.value) {
    case 1:
      valid = await form1Ref.value.validate()
      break
    case 2:
      valid = await form2Ref.value?.validate() ?? true
      break
    case 3:
      valid = await form3Ref.value?.validate() ?? true
      break
    case 4:
      valid = await form4Ref.value?.validate() ?? true
      break
  }

  if (valid) {
    step.value++
  }
}

const openDialog = (anamnesis = null) => {
  if (anamnesis) {
    isEditing.value = true
    editingId.value = anamnesis.id
    Object.assign(form.value, anamnesis)
  } else {
    isEditing.value = false
    editingId.value = null
    resetForm()
    // Auto-seleccionar psicólogo actual si es psicólogo
    if (authStore.isPsicologo) {
      form.value.psicologo_id = authStore.user.id
    }
  }
  step.value = 1
  showDialog.value = true
}

const closeDialog = () => {
  showDialog.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    dni_estudiante: '',
    psicologo_id: null,
    fecha_evaluacion: '',
    numero_hijos: null,
    personas_vive_con: '',
    lugar_ocupa: null,
    padre_nombre: '',
    padre_edad: null,
    padre_escolaridad: '',
    padre_ocupacion: '',
    relacion_padre: null,
    madre_nombre: '',
    madre_edad: null,
    madre_escolaridad: '',
    madre_ocupacion: '',
    relacion_madre: null,
    relacion_hermanos: null,
    relacion_otros_familiares: null,
    inicial_edad_ingreso: '',
    primaria_edad_ingreso: '',
    secundaria_edad_ingreso: '',
    madre_enfermedades_embarazo: '',
    madre_medicamentos_embarazo: '',
    sintomas_perdida: '',
    estado_nutricional: '',
    estado_emocional: '',
    parto_semanas: null,
    parto_peso: null,
    parto_talla: null,
    parto_apgar: null,
    tratamientos_parto: '',
    enfermedades_importantes: '',
    edad_controlo_cabeza: null,
    edad_se_sento: null,
    edad_se_paro: null,
    edad_camino: null,
    edad_controlo_esfinter: null,
    edad_durmio_solo: null,
    edad_primeras_palabras: null,
    edad_oraciones: null,
    edad_comprendio_instrucciones: null,
    evaluacion_neurologica: false,
    evaluacion_neurologica_fecha: null,
    evaluacion_psicologica: false,
    evaluacion_psicologica_fecha: null,
    evaluacion_psiquiatrica: false,
    evaluacion_psiquiatrica_fecha: null,
    evaluacion_psicopedagogica: false,
    evaluacion_psicopedagogica_fecha: null,
    problemas_dormir: false,
    horas_sueno: null,
    habitos_estudio: false,
    cuales_habitos_estudio: '',
    actividades_intereses: '',
    autoestima_tolerancia_frustracion: '',
    observaciones: ''
  }
}

const handleSubmit = async () => {
  const valid = await form5Ref.value?.validate() ?? true
  if (!valid) return

  let result
  if (isEditing.value) {
    result = await anamnesisStore.update(editingId.value, form.value)
  } else {
    result = await anamnesisStore.create(form.value)
  }

  if (result.success) {
    closeDialog()
  }
}

const viewAnamnesis = (anamnesis) => {
  // Implementar vista detallada
  $q.dialog({
    title: 'Anamnesis Completa',
    message: `Estudiante: ${anamnesis.estudiante?.nombre_completo || 'N/A'}\nFecha: ${anamnesis.fecha_evaluacion}\n\nFunción de visualización en desarrollo`,
    html: true
  })
}

const confirmDelete = (anamnesis) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar esta anamnesis?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await anamnesisStore.delete(anamnesis.id)
  })
}

onMounted(async () => {
  anamnesisStore.fetchAll()
  await estudiantesStore.fetchAll()

  // Preparar opciones de estudiantes
  estudiantesOptions.value = estudiantesStore.estudiantes.map(e => ({
    dni: e.dni,
    nombre_completo: `${e.nombres} ${e.apellidos} (${e.dni})`
  }))

  // Cargar psicólogos
  psicologosOptions.value = await usuariosStore.fetchPsicologos()
})
</script>
