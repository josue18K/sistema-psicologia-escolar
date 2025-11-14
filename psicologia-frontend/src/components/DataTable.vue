<template>
  <q-table
    :rows="rows"
    :columns="columns"
    :loading="loading"
    :filter="filter"
    :pagination="pagination"
    row-key="id"
    flat
    bordered
    class="data-table"
  >
    <!-- Barra superior -->
    <template v-slot:top>
      <div class="row full-width items-center justify-between">
        <div class="text-h6">{{ title }}</div>
        <div class="row q-gutter-sm">
          <!-- Buscador -->
          <q-input v-model="filter" outlined dense debounce="300" placeholder="Buscar...">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>

          <!-- Botón agregar -->
          <q-btn
            v-if="showAddButton"
            color="primary"
            icon="add"
            :label="addButtonLabel"
            @click="$emit('add')"
            unelevated
          />
        </div>
      </div>
    </template>

    <!-- Slot para acciones personalizadas -->
    <template v-slot:body-cell-actions="props">
      <q-td :props="props">
        <slot name="actions" :row="props.row"></slot>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  rows: {
    type: Array,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: '',
  },
  showAddButton: {
    type: Boolean,
    default: true,
  },
  addButtonLabel: {
    type: String,
    default: 'Agregar',
  },
})

defineEmits(['add'])

const filter = ref('')
const pagination = ref({
  rowsPerPage: 10,
})
</script>

<style scoped lang="scss">
.data-table {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
</style>
