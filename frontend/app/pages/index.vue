<template>

  <div class="search-wrapper">
    <div class="search-bar">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="search-icon" viewBox="0 0 16 16">
        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
      </svg>
      <input type="text" placeholder="Rechercher..." aria-label="Rechercher">
    </div>
  </div>



  <div class="events mb-5">
      <h3>Nos prochains événements</h3>
      <div class="events-post">
        <img src="../assets/images/affiches/affiche1.png" alt="Affiche événement 1">
        <img src="../assets/images/affiches/affiche2.jpg" alt="Affiche événement 2">
        <img src="../assets/images/affiches/affiche3.jpg" alt="Affiche événement 3">
      </div>
    </div>

  <div class="activity container my-4">
    <h3 class="fw-normal mb-3">Nos activités</h3>

    <div class="row row-cols-4 g-4">
      <div class="col">
        <div class="ratio ratio-4x3">
          <img src="../assets/images/sports/football.jpg" alt="Football" class="rounded-3 object-fit-cover">
        </div>
        <p class="mt-2 mb-0">Football</p>
      </div>

      <div class="col">
        <div class="ratio ratio-4x3">
          <img src="../assets/images/sports/basketball.jpg" alt="Basketball" class="rounded-3 object-fit-cover">
        </div>
        <p class="mt-2 mb-0">Basketball</p>
      </div>

      <div class="col">
        <div class="ratio ratio-4x3">
          <img src="../assets/images/sports/tennis.jpg" alt="Tennis" class="rounded-3 object-fit-cover">
        </div>
        <p class="mt-2 mb-0">Tennis</p>
      </div>

      <div class="col">
        <div class="ratio ratio-4x3">
          <img src="../assets/images/sports/handball.jpg" alt="Handball" class="rounded-3 object-fit-cover">
        </div>
        <p class="mt-2 mb-0">Handball</p>
      </div>
    </div>

    <NuxtLink to="/activity" class="d-inline-block mt-4 text-decoration-none text-black">
      Voir plus d'activités
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-up-right">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M17 7l-10 10" />
        <path d="M8 7l9 0l0 9" />
      </svg>
    </NuxtLink>
  </div>

    <div class="avis">
      <div class="container-fluid m-0 p-0">
        <div class="container-avis">
          <div class="w-25 bg-white p-2 rounded-3">
            <h5>Stéphanie</h5>
            <p>Mon enfant a passe d’excellent vacances. Je recommande</p>
          </div>
          <div class="w-25 bg-white p-2 rounded-3">
            <h5>Patricia</h5>
            <p>Une équipe d'animateurs au top et à l'écoute. Je réinscrirai mes enfants sans hésiter.</p>
          </div>
          <div class="w-25 bg-white p-2 rounded-3">
            <h5>Marc</h5>
            <p>Inscriptions faciles et accueil chaleureux. Ma fille s'est fait plein d'amis cet été !</p>
          </div>
        </div>
      </div>
    </div>

    <div class="map">
      <div>
        <iframe
            class="rounded-3"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2709.230255197307!2d-1.5629043878461653!3d47.23164181367442!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805ee9d6a19a6e5%3A0x9c7a7eb54241e847!2sSaint%20F%C3%A9lix-La%20Salle!5e0!3m2!1sfr!2sfr!4v1789719890614!5m2!1sfr!2sfr"
            width="600"
            height="450"
            style="border: 0"
            allowfullscreen
            loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin"
        />
      </div>
      <p class="fs-4 lh-lg mt-5">
        Idéalement situé, notre OMS est à proximité immédiate de nombreuses salles et zones sportives. Un emplacement privilégié qui nous permet de vous offrir un large choix d'activités dans des infrastructures de qualité.
      </p>
    </div>
</template>