<template>
  <div class="contact container my-5" style="color: #2C302E">
    <h3 class="mb-4">Contact</h3>

    <div class="row g-5">
      <!-- Formulaire -->
      <div class="col-12 col-lg-6">
        <div class="p-4 rounded-4" style="background-color: #E9E3D5">
          <h5 class="mb-4">Formulaire de contact</h5>

          <form>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="nom" class="form-label">Nom</label>
                <input id="nom" type="text" class="form-control rounded-3" placeholder="Entrez votre nom...">
              </div>

              <div class="col-12 col-md-6">
                <label for="prenom" class="form-label">Prénom</label>
                <input id="prenom" type="text" class="form-control rounded-3" placeholder="Entrez votre prénom...">
              </div>

              <div class="col-12">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input id="email" type="email" class="form-control rounded-3" placeholder="Entrez votre e-mail...">
              </div>

              <div class="col-12">
                <label for="sujet" class="form-label">Sujet</label>
                <input id="sujet" type="text" class="form-control rounded-3" placeholder="Objet de votre message...">
              </div>

              <div class="col-12">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" class="form-control rounded-3" rows="5" placeholder="Écrivez votre message..."></textarea>
              </div>

              <div class="col-12 text-center mt-4">
                <button type="button" class="btn rounded-pill px-5 text-white" style="background-color: #FF7E70">
                  Envoyer
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Carte + infos -->
      <div class="col-12 col-lg-6">
        <div class="ratio ratio-16x9 mb-4">
          <iframe
              class="rounded-4"
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2709.230255197307!2d-1.5629043878461653!3d47.23164181367442!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805ee9d6a19a6e5%3A0x9c7a7eb54241e847!2sSaint%20F%C3%A9lix-La%20Salle!5e0!3m2!1sfr!2sfr!4v1789719890614!5m2!1sfr!2sfr"
              style="border: 0"
              allowfullscreen
              loading="lazy"
              referrerpolicy="strict-origin-when-cross-origin"
          />
        </div>

        <div class="row g-4">
          <div class="col-12 col-md-6">
            <h5 class="mb-3">Nous joindre</h5>
            <p class="mb-1"><strong>Téléphone :</strong> 00 00 00 00 00</p>
            <p class="mb-1"><strong>E-mail :</strong> contact@oms-nantes.fr</p>
            <p class="mb-0"><strong>Adresse :</strong> 27 rue du ballet, 44000 Nantes</p>
          </div>

          <div class="col-12 col-md-6">
            <h5 class="mb-3">Horaires d'ouverture</h5>
            <ul class="list-unstyled mb-0">
              <li>Lundi : 8h-12h / 14h-18h</li>
              <li>Mardi : 8h-12h / 14h-18h</li>
              <li>Mercredi : 8h-12h / 14h-18h</li>
              <li>Jeudi : 8h-12h / 14h-18h</li>
              <li>Vendredi : 8h-12h / 14h-17h</li>
              <li>Samedi : fermé</li>
              <li>Dimanche : fermé</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style src="../assets/styles/contact.css"></style>