<template>
  <div class="login-container fullscreen bg-primary">
    <div class="row full-width full-height items-center justify-center">
      <div class="col-12 col-md-6 col-lg-4">
        <q-card class="login-card q-pa-lg shadow-10">
          <q-card-section class="text-center">
            <div class="row justify-center q-mb-md">
              <q-icon
                name="psychology"
                size="xl"
                color="primary"
                class="q-mb-sm"
              />
            </div>
            <div class="text-h4 text-weight-bold text-primary q-mb-xs">
              Sistema de Psicología
            </div>
            <div class="text-h6 text-grey-7 q-mb-lg">
              Iniciar Sesión
            </div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="onSubmit" class="q-gutter-md">
              <q-input
                v-model="form.email"
                label="Correo Electrónico"
                type="email"
                :rules="[
                  val => !!val || 'El correo es obligatorio',
                  val => isValidEmail(val) || 'Correo electrónico no válido'
                ]"
                lazy-rules
                outlined
                color="primary"
                class="q-mb-sm"
              >
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>

              <q-input
                v-model="form.password"
                label="Contraseña"
                :type="showPassword ? 'text' : 'password'"
                :rules="[val => !!val || 'La contraseña es obligatoria']"
                lazy-rules
                outlined
                color="primary"
                class="q-mb-sm"
              >
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="showPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <div class="row items-center justify-between q-mb-md">
                <q-checkbox
                  v-model="rememberMe"
                  label="Recordarme"
                  color="primary"
                />
                <a href="#" class="text-primary text-caption" @click.prevent="forgotPassword">
                  ¿Olvidaste tu contraseña?
                </a>
              </div>

              <q-btn
                label="Iniciar Sesión"
                type="submit"
                color="primary"
                size="lg"
                class="full-width q-mt-md"
                :loading="loading"
              >
                <template v-slot:loading>
                  <q-spinner-hourglass class="on-left" />
                  Iniciando sesión...
                </template>
              </q-btn>

              <!-- Demo Credentials -->
              <q-card flat class="bg-blue-1 q-mt-md">
                <q-card-section class="q-py-sm">
                  <div class="text-caption text-weight-medium text-blue-9">
                    Credenciales de Demo:
                  </div>
                  <div class="text-caption text-blue-8">
                    <strong>TOE:</strong> toe@colegio.com / password123
                  </div>
                  <div class="text-caption text-blue-8">
                    <strong>Psicólogo:</strong> psicologo@colegio.com / password123
                  </div>
                  <div class="text-caption text-blue-8">
                    <strong>Director:</strong> director@colegio.com / password123
                  </div>
                </q-card-section>
              </q-card>
            </q-form>
          </q-card-section>

          <q-card-section class="text-center q-pt-none">
            <div class="text-caption text-grey-6">
              &copy; 2024 Sistema de Psicología Escolar
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { isValidEmail } from 'src/utils/helpers'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const showPassword = ref(false)
const rememberMe = ref(false)
const loading = ref(false)

async function onSubmit() {
  loading.value = true

  try {
    const result = await authStore.login(form.value)

    if (result.success) {
      $q.notify({
        type: 'positive',
        message: `¡Bienvenido, ${result.user.name}!`,
        position: 'top',
        timeout: 2000
      })

      // Redirigir según el rol
      const roleRedirects = {
        'TOE': '/',
        'PSICOLOGO': '/',
        'DIRECTOR': '/reportes',
        'TUTOR': '/estudiantes'
      }

      const redirectTo = roleRedirects[result.user.rol] || '/'

      // Pequeño delay para que se vea el mensaje de bienvenida
      setTimeout(() => {
        router.push(redirectTo)
      }, 500)
    } else {
      $q.notify({
        type: 'negative',
        message: result.error,
        position: 'top'
      })
    }
  } catch (error) {
    console.error('Login error:', error)
    $q.notify({
      type: 'negative',
      message: 'Error de conexión con el servidor',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

function forgotPassword() {
  $q.dialog({
    title: 'Recuperar Contraseña',
    message: 'Por favor, contacta al administrador del sistema para recuperar tu contraseña.',
    cancel: false,
    persistent: true
  })
}
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

.login-card {
  border-radius: 16px;
  backdrop-filter: blur(10px);
}

:deep(.q-field__control) {
  border-radius: 8px;
}
</style>
