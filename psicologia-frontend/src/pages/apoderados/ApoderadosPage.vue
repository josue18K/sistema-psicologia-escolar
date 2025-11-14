<template>
  <q-page padding>
    <div class="q-pa-md fade-in">
      <data-table
        :rows="apoderadosStore.apoderados"
        :columns="columns"
        :loading="apoderadosStore.loading"
        title="Gestión de Apoderados"
        add-button-label="Nuevo Apoderado"
        @add="openDialog()"
        class="modern-table"
      >
        <template v-slot:actions="{ row }">
          <q-btn
            flat
            dense
            round
            icon="visibility"
            color="primary"
            @click="viewApoderado(row)"
            class="btn-action"
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
            class="btn-action"
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
            class="btn-action"
          >
            <q-tooltip>Eliminar</q-tooltip>
          </q-btn>
        </template>
      </data-table>

      <!-- Dialog formulario -->
      <dialog-form
        v-model="showDialog"
        :title="isEditing ? 'Editar Apoderado' : 'Nuevo Apoderado'"
        :loading="apoderadosStore.loading"
        @submit="handleSubmit"
      >
        <q-form ref="formRef" class="q-gutter-md">
          <q-input
            v-model="form.nombre"
            label="Nombre completo *"
            outlined
            dense
            :rules="[val => !!val || 'El nombre es obligatorio']"
          >
            <template v-slot:prepend>
              <q-icon name="person" />
            </template>
          </q-input>

          <q-select
            v-model="form.parentesco"
            :options="parentescoOptions"
            label="Parentesco *"
            outlined
            dense
            emit-value
            map-options
            :rules="[val => !!val || 'El parentesco es obligatorio']"
          >
            <template v-slot:prepend>
              <q-icon name="family_restroom" />
            </template>
          </q-select>

          <q-input
            v-model="form.telefono"
            label="Teléfono *"
            outlined
            dense
            mask="### ### ###"
            :rules="[val => !!val || 'El teléfono es obligatorio']"
          >
            <template v-slot:prepend>
              <q-icon name="phone" />
            </template>
          </q-input>

          <q-input
            v-model="form.correo"
            label="Correo electrónico"
            outlined
            dense
            type="email"
          >
            <template v-slot:prepend>
              <q-icon name="email" />
            </template>
          </q-input>
        </q-form>
      </dialog-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApoderadosStore } from 'stores/apoderados'
import { useQuasar } from 'quasar'
import DataTable from 'components/DataTable.vue'
import DialogForm from 'components/DialogForm.vue'

const apoderadosStore = useApoderadosStore()
const $q = useQuasar()
const formRef = ref(null)

const showDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = ref({
  nombre: '',
  parentesco: '',
  telefono: '',
  correo: ''
})

const parentescoOptions = [
  { label: 'Madre', value: 'Madre' },
  { label: 'Padre', value: 'Padre' },
  { label: 'Tutor Legal', value: 'Tutor Legal' },
  { label: 'Abuelo/a', value: 'Abuelo/a' },
  { label: 'Tío/a', value: 'Tío/a' },
  { label: 'Otro', value: 'Otro' }
]

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
  { name: 'parentesco', label: 'Parentesco', field: 'parentesco', align: 'center', sortable: true },
  { name: 'telefono', label: 'Teléfono', field: 'telefono', align: 'center', sortable: true },
  { name: 'correo', label: 'Correo', field: 'correo', align: 'left', sortable: true },
  { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
]

const openDialog = (apoderado = null) => {
  if (apoderado) {
    isEditing.value = true
    editingId.value = apoderado.id
    form.value = { ...apoderado }
  } else {
    isEditing.value = false
    editingId.value = null
    form.value = {
      nombre: '',
      parentesco: '',
      telefono: '',
      correo: ''
    }
  }
  showDialog.value = true
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  let result
  if (isEditing.value) {
    result = await apoderadosStore.update(editingId.value, form.value)
  } else {
    result = await apoderadosStore.create(form.value)
  }

  if (result.success) {
    showDialog.value = false
  }
}

const viewApoderado = (apoderado) => {
  $q.dialog({
    title: 'Detalles del Apoderado',
    message: `
      <strong>Nombre:</strong> ${apoderado.nombre}<br>
      <strong>Parentesco:</strong> ${apoderado.parentesco}<br>
      <strong>Teléfono:</strong> ${apoderado.telefono}<br>
      <strong>Correo:</strong> ${apoderado.correo || 'No registrado'}
    `,
    html: true
  })
}

const confirmDelete = (apoderado) => {
  $q.dialog({
    title: 'Confirmar eliminación',
    message: `¿Estás seguro de eliminar al apoderado "${apoderado.nombre}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await apoderadosStore.delete(apoderado.id)
  })
}

onMounted(() => {
  apoderadosStore.fetchAll()
})
</script>
