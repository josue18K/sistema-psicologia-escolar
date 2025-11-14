<template>
  <q-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    persistent
  >
    <q-card :style="{ width: width, maxWidth: maxWidth }">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">{{ title }}</div>
        <q-space />
        <q-btn icon="close" flat round dense @click="$emit('update:modelValue', false)" />
      </q-card-section>

      <q-card-section>
        <slot></slot>
      </q-card-section>

      <q-card-actions align="right" class="q-px-md q-pb-md">
        <q-btn label="Cancelar" color="grey-7" flat @click="$emit('update:modelValue', false)" />
        <q-btn
          :label="submitLabel"
          color="primary"
          :loading="loading"
          @click="$emit('submit')"
          unelevated
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script setup>
defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, required: true },
  submitLabel: { type: String, default: 'Guardar' },
  loading: { type: Boolean, default: false },
  width: { type: String, default: '600px' },
  maxWidth: { type: String, default: '90vw' },
})
defineEmits(['update:modelValue', 'submit'])
</script>
