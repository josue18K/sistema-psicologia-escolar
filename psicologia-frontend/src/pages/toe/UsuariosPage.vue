<template>
  <q-page padding>
    <div class="q-pa-md">
      <!-- Tabla de usuarios -->
      <data-table
        :rows="usuariosStore.usuarios"
        :columns="columns"
        :loading="usuariosStore.loading"
        title="Gestión de Usuarios"
        add-button-label="Nuevo Usuario"
        @add="openDialog()"
      >
        <template v-slot:actions="{ row }">
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
            :icon="row.estado ? 'check_circle' : 'cancel'"
            :color="row.estado ? 'positive' : 'negative'"
            @click="toggleEstado(row)"
          >
            <q-tooltip>{{ row.estado ? 'Activo' : 'Inactivo' }}</q-tooltip>
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
        :title="isEditing ? 'Editar Usuario' : 'Nuevo Usuario'"
        :loading="usuariosStore.loading"
        @submit="handleSubmit"
      >
        <q-form ref="formRef" class="q-gutter-md">
          <q-input
            v-model="form.name"
            label="Nombre completo *"
            outlined
            dense
            :rules="[val => !!val || 'El nombre es obligatorio']"
          />

          <q-input
            v-model="form.email"
            type="email"
            label="Correo electrónico *"
            outlined
            dense
            :rules="[
              val => !!val || 'El correo es obligatorio',
              val => /.+@.+\..+/.test(val) || 'Correo inválido'
            ]"
          />

          <q-input
            v-if="!isEditing"
            v-model="form.password"
            type="password"
            label="Contraseña *"
            outlined
            dense
            :rules="[
              val => !!val || 'La contraseña es obligatoria',
              val => val.length >= 6 || 'Mínimo 6 caracteres'
            ]"
          />

          <q-input
            v-if="!isEditing"
            v-model="form.password_confirmation"
            type="password"
            label="Confirmar contraseña *"
            outlined
            dense
            :rules="[
              val => !!val || 'Confirma la contraseña',
              val => val === form.password || 'Las contraseñas no coinciden'
            ]"
          />

          <q-select
            v-model="form.rol"
            :options="rolesOptions"
            label="Rol *"
            outlined
            dense
            emit-value
            map-options
            :rules="[val => !!val || 'El rol es obligatorio']"
          />
        </q-form>
      </dialog-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUsuariosStore } from 'stores/usuarios'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'
import DialogForm from 'components/DialogForm.vue'

const usuariosStore = useUsuariosStore()
const $q = useQuasar()
const formRef = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  rol: ''
})

const rolesOptions = [
  { label: 'TOE', value: 'TOE' },
  { label: 'Psicólogo', value: 'PSICOLOGO' },
  { label: 'Tutor', value: 'TUTOR' },
  { label: 'Director', value: 'DIRECTOR' }
]

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
  { name: 'email', label: 'Correo', field: 'email', align: 'left', sortable: true },
  { name: 'rol', label: 'Rol', field: 'rol', align: 'center', sortable: true },
  {
    name: 'estado',
    label: 'Estado',
    field: 'estado',
    align: 'center',
    sortable: true,
    format: val => val ? 'Activo' : 'Inactivo'
  },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const openDialog = (user = null) => {
  if (user) {
    isEditing.value = true
    editingId.value = user.id
    form.value = {
      name: user.name,
      email: user.email,
      rol: user.rol
    }
  } else {
    isEditing.value = false
    editingId.value = null
    form.value = {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      rol: ''
    }
  }
  showDialog.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()

  if (!valid) return

  let result
  if (isEditing.value) {
    result = await usuariosStore.update(editingId.value, form.value)
  } else {
    result = await usuariosStore.create(form.value)
  }

  if (result.success) {
    showDialog.value = false
    formRef.value.reset()
  }
}

const toggleEstado = async (user) => {
  await usuariosStore.toggleEstado(user.id)
}

const confirmDelete = (user) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar al usuario "${user.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await usuariosStore.delete(user.id)
  })
}

onMounted(() => {
  usuariosStore.fetchAll()
})
</script>
