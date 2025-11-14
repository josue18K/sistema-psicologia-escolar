<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
      <q-page class="flex flex-center bg-gradient">
        <q-card class="login-card q-pa-md">
          <q-card-section class="text-center q-pb-none">
            <div class="text-h4 text-weight-bold text-primary q-mb-sm">
              Sistema de Gestión Psicológica
            </div>
            <div class="text-subtitle2 text-grey-7">Iniciar Sesión</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit="handleLogin" class="q-gutter-md">
              <!-- Email -->
              <q-input
                v-model="form.email"
                type="email"
                label="Correo Electrónico"
                outlined
                dense
                :rules="[
                  (val) => !!val || 'El correo es obligatorio',
                  (val) => /.+@.+\..+/.test(val) || 'Ingresa un correo válido',
                ]"
              >
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>

              <!-- Password -->
              <q-input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                label="Contraseña"
                outlined
                dense
                :rules="[
                  (val) => !!val || 'La contraseña es obligatoria',
                  (val) => val.length >= 6 || 'Mínimo 6 caracteres',
                ]"
              >
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="showPassword ? 'visibility' : 'visibility_off'"
                    class="cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <!-- Botón Login -->
              <q-btn
                type="submit"
                color="primary"
                label="Iniciar Sesión"
                class="full-width"
                :loading="authStore.loading"
                :disable="authStore.loading"
                unelevated
                rounded
              />
            </q-form>
          </q-card-section>
        </q-card>
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
const form = ref({ email: '', password: '' })
const showPassword = ref(false)
const handleLogin = async () => {
  const result = await authStore.login(form.value)
  if (result.success) {
    // Redirigir según rol o a la página solicitada
    const redirect = route.query.redirect || getDashboardByRole(result.user.rol)
    router.push(redirect)
  }
}

const getDashboardByRole = (rol) => {
  switch (rol) {
    case 'TOE':
      return '/usuarios'
    case 'PSICOLOGO':
      return '/estudiantes'
    case 'TUTOR':
      return '/mis-estudiantes'
    case 'DIRECTOR':
      return '/estadisticas'
    default:
      return '/'
  }
}

</script>

<style scoped lang="scss">
.bg-gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}
.login-card {
  width: 100%;
  max-width: 420px;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}
</style>
