<template>
  <q-form @submit="onSubmit" class="q-gutter-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.name"
          label="Nombre Completo *"
          :rules="[val => !!val || 'El nombre es obligatorio']"
          lazy-rules
          outlined
        />
      </div>

      <div class="col-12 col-md-6">
        <q-input
          v-model="form.email"
          label="Correo Electrónico *"
          type="email"
          :rules="[
            val => !!val || 'El correo es obligatorio',
            val => isValidEmail(val) || 'Correo electrónico no válido'
          ]"
          lazy-rules
          outlined
        />
      </div>

      <div class="col-12 col-md-6">
        <q-select
          v-model="form.rol"
          :options="ROLES_OPTIONS"
          label="Rol *"
          :rules="[val => !!val || 'El rol es obligatorio']"
          lazy-rules
          outlined
          map-options
          emit-value
        />
      </div>

      <div class="col-12 col-md-6" v-if="!esEdicion">
        <q-input
          v-model="form.password"
          label="Contraseña *"
          :type="showPassword ? 'text' : 'password'"
          :rules="[val => !!val || 'La contraseña es obligatoria']"
          lazy-rules
          outlined
        >
          <template v-slot:append>
            <q-icon
              :name="showPassword ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="showPassword = !showPassword"
            />
          </template>
        </q-input>
      </div>

      <div class="col-12 col-md-6" v-if="!esEdicion">
        <q-input
          v-model="form.password_confirmation"
          label="Confirmar Contraseña *"
          :type="showPassword ? 'text' : 'password'"
          :rules="[
            val => !!val || 'La confirmación es obligatoria',
            val => val === form.password || 'Las contraseñas no coinciden'
          ]"
          lazy-rules
          outlined
        />
      </div>

      <div class="col-12" v-if="esEdicion">
        <q-checkbox
          v-model="form.cambiar_password"
          label="Cambiar contraseña"
        />
      </div>

      <template v-if="esEdicion && form.cambiar_password">
        <div class="col-12 col-md-6">
          <q-input
            v-model="form.password"
            label="Nueva Contraseña"
            :type="showPassword ? 'text' : 'password'"
            outlined
          />
        </div>

        <div class="col-12 col-md-6">
          <q-input
            v-model="form.password_confirmation"
            label="Confirmar Nueva Contraseña"
            :type="showPassword ? 'text' : 'password'"
            :rules="[
              val => !form.password || val === form.password || 'Las contraseñas no coinciden'
            ]"
            outlined
          />
        </div>
      </template>
    </div>

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
import { ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import { userService } from 'src/services/api'
import { isValidEmail } from 'src/utils/helpers'

const $q = useQuasar()
const props = defineProps({
  usuario: {
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
const showPassword = ref(false)

const form = ref({
  name: '',
  email: '',
  rol: '',
  password: '',
  password_confirmation: '',
  cambiar_password: false
})

const ROLES_OPTIONS = [
  { label: 'TOE', value: 'TOE' },
  { label: 'Psicólogo', value: 'PSICOLOGO' },
  { label: 'Tutor', value: 'TUTOR' },
  { label: 'Director', value: 'DIRECTOR' }
]

// Watchers
watch(() => props.usuario, (newUsuario) => {
  if (newUsuario && props.esEdicion) {
    form.value = {
      name: newUsuario.name,
      email: newUsuario.email,
      rol: newUsuario.rol,
      password: '',
      password_confirmation: '',
      cambiar_password: false
    }
  }
}, { immediate: true })

// Methods
async function onSubmit() {
  loading.value = true
  try {
    const data = { ...form.value }

    // Si es edición y no se quiere cambiar la contraseña, eliminar campos de password
    if (props.esEdicion && !data.cambiar_password) {
      delete data.password
      delete data.password_confirmation
    }
    delete data.cambiar_password

    if (props.esEdicion) {
      await userService.updateUser(props.usuario.id, data)
      $q.notify({
        type: 'positive',
        message: 'Usuario actualizado exitosamente'
      })
    } else {
      await userService.createUser(data)
      $q.notify({
        type: 'positive',
        message: 'Usuario registrado exitosamente'
      })
    }

    emit('guardado')

  } catch (error) {
    console.error('Error guardando usuario:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error guardando usuario'
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
</script>
