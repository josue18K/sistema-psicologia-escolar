<template>
  <div class="fullscreen bg-negative text-white text-center q-pa-md flex flex-center">
    <div>
      <div style="font-size: 30vh">403</div>
      <div class="text-h2" style="opacity:.4">Acceso Denegado</div>
      <p class="text-h6">No tienes permisos para acceder a esta página</p>
      <q-btn
        class="q-mt-xl"
        color="white"
        text-color="negative"
        unelevated
        to="/"
        label="Volver al inicio"
        no-caps
        icon="arrow_back"
      />
    </div>
  </div>
</template>
