<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <footer class="site-footer mt-5">
    <div class="container py-5">
      <div class="row gy-4">
        <div class="col-12 col-md-6">
          <h5 class="footer-title">Horaires</h5>
          <ul class="list-unstyled mb-0">
            <li>Du lundi au jeudi : 8h - 12h & 14h - 18h</li>
            <li>Le vendredi : 8h - 12h & 14h - 17h</li>
          </ul>
        </div>

        <div class="col-12 col-md-6">
          <h5 class="footer-title">Navigation</h5>
          <ul class="list-unstyled mb-0">
            <li><NuxtLink to="/about">l'OMS</NuxtLink></li>
            <li><NuxtLink to="/activity">Nos activités</NuxtLink></li>
            <li><NuxtLink to="/stages">Nos stages</NuxtLink></li>
            <li><NuxtLink to="/contact">Contact</NuxtLink></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom text-center py-3">
      <small>© {{ year }} OMS. Tous droits réservés.</small>
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background-color: #e9e3d5;
  color: black;
}

.footer-title {
  color: black;
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
}

.site-footer li {
  margin-bottom: 0.5rem;
}

.site-footer a {
  color: black;
  text-decoration: none;
}

.footer-bottom {
  border-top: 1px solid #343a40;
}
</style>