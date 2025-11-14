<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="row no-wrap">
        <!-- Panel izquierdo - Formulario -->
        <div class="col-12 col-md-6 login-left-panel">
          <div class="login-form-container">
            <!-- Logo y título -->
            <div class="q-mb-xl">
              <div class="text-h3 text-primary text-weight-bold q-mb-sm">
                Login
              </div>
              <div class="text-subtitle1 text-grey-7">
                Inicia sesión en tu cuenta.
              </div>
            </div>

            <!-- Formulario -->
            <q-form @submit="handleLogin" class="q-gutter-md">
              <!-- Email -->
              <div>
                <div class="text-subtitle2 text-weight-medium q-mb-sm text-primary">
                  Correo Electrónico
                </div>
                <q-input
                  v-model="form.email"
                  type="email"
                  outlined
                  dense
                  bg-color="white"
                  class="modern-input"
                  :rules="[
                    val => !!val || 'El correo es obligatorio',
                    val => /.+@.+\..+/.test(val) || 'Ingresa un correo válido'
                  ]"
                />
              </div>

              <!-- Password -->
              <div>
                <div class="text-subtitle2 text-weight-medium q-mb-sm text-primary">
                  Contraseña
                </div>
                <q-input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  outlined
                  dense
                  bg-color="white"
                  class="modern-input"
                  :rules="[
                    val => !!val || 'La contraseña es obligatoria',
                    val => val.length >= 6 || 'Mínimo 6 caracteres'
                  ]"
                >
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'visibility' : 'visibility_off'"
                      class="cursor-pointer"
                      @click="showPassword = !showPassword"
                      color="grey-6"
                    />
                  </template>
                </q-input>
              </div>

              <!-- Remember me y Reset Password -->
              <div class="row items-center justify-between">
                <q-checkbox
                  v-model="rememberMe"
                  label="Recordarme"
                  color="primary"
                  dense
                />
                <a href="#" class="text-primary text-weight-bold text-decoration-none">
                  ¿Olvidaste tu contraseña?
                </a>
              </div>

              <!-- Botón Sign In -->
              <q-btn
                type="submit"
                label="Iniciar Sesión"
                color="primary"
                size="lg"
                class="full-width q-mt-md"
                :loading="authStore.loading"
                :disable="authStore.loading"
                unelevated
                no-caps
                style="border-radius: 8px; padding: 14px; font-weight: 600;"
              >
                <template v-slot:loading>
                  <q-spinner size="20px" />
                </template>
              </q-btn>
            </q-form>

            <!-- Footer -->
            <div class="text-center q-mt-xl text-grey-7">
              ¿No tienes cuenta?
              <a href="#" class="text-primary text-weight-bold text-decoration-none">
                Regístrate aquí
              </a>
            </div>
          </div>
        </div>

        <!-- Panel derecho - Imagen y mensaje -->
        <div class="col-md-6 login-right-panel gt-sm">
          <div class="right-panel-content">
            <div class="right-panel-overlay">
              <div class="text-center">
                <div class="text-h4 text-white text-weight-bold q-mb-md" style="line-height: 1.3;">
                  Gestiona todas las
                  <span class="text-yellow-4">Operaciones Psicológicas</span>
                  <br>desde la comodidad de tu institución.
                </div>
                <div class="text-h6 text-white q-mt-xl">
                  SISTEMA DE GESTIÓN PSICOLÓGICA M.M
                </div>
              </div>
            </div>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from 'stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const showPassword = ref(false)
const rememberMe = ref(false)

const handleLogin = async () => {
  const result = await authStore.login(form.value)

  if (result.success) {
    const redirect = route.query.redirect || getDashboardByRole(result.user.rol)
    router.push(redirect)
  }
}

const getDashboardByRole = (rol) => {
  switch (rol) {
    case 'TOE': return '/usuarios'
    case 'PSICOLOGO': return '/estudiantes'
    case 'TUTOR': return '/mis-estudiantes'
    case 'DIRECTOR': return '/estadisticas'
    default: return '/'
  }
}
</script>

<style scoped lang="scss">
.login-left-panel {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #f8f9fa;
  padding: 40px 20px;
}

.login-form-container {
  width: 100%;
  max-width: 440px;
}

.modern-input {
  :deep(.q-field__control) {
    border-radius: 8px;
    height: 50px;

    &::before {
      border-color: #e0e0e0;
    }

    &:hover::before {
      border-color: #3949ab;
    }
  }

  :deep(.q-field__native) {
    padding: 12px 16px;
  }
}

.login-right-panel {
  position: relative;
  min-height: 100vh;
  background-image: url('https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=2787');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.right-panel-content {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.right-panel-overlay {
  position: relative;
  z-index: 2;
  padding: 60px 40px;
  max-width: 600px;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(57, 73, 171, 0.95), rgba(102, 126, 234, 0.85));
    border-radius: 24px;
    z-index: -1;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }
}

// Responsive
@media (max-width: 1023px) {
  .login-left-panel {
    min-height: 100vh;
  }
}

@media (max-width: 599px) {
  .login-form-container {
    padding: 20px;
  }

  .text-h3 {
    font-size: 2rem !important;
  }
}
</style>
