<script setup lang="ts">

</script>

<template>
  <nav class="d-flex justify-content-between border p-2 align-items-center">
    <NuxtLink to="/" class="text-decoration-none text-black">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
      </svg>
    </NuxtLink>
    <div class="d-flex gap-4 list-unstyled align-items-center">
      <NuxtLink to="/about" class="text-decoration-none text-black">l'OMS</NuxtLink>
      <NuxtLink to="/activity" class="text-decoration-none text-black">Activités</NuxtLink>
      <NuxtLink to="/stages" class="text-decoration-none text-black">Stages sportif</NuxtLink>
      <NuxtLink to="/contact" class="text-decoration-none text-black">Contact</NuxtLink>
      <NuxtLink to="/login" class="text-decoration-none text-black">Connexion</NuxtLink>
    </div>
  </nav>

</template>

<style scoped>

</style>