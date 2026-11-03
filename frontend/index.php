<?php
session_start();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/evenement.php';

// Événements créés depuis l'administration (table actualite), du plus récent au plus ancien
try {
    $evenements = db()->query('SELECT id, titre, image_path FROM actualite ORDER BY date_publication DESC')->fetchAll();
} catch (PDOException $e) {
    $evenements = [];
}

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Accueil</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/styles/style.css?v=<?= @filemtime(__DIR__ . '/assets/styles/style.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/components/navbar.php'; ?>
  <main><div class="search-wrapper">
    <div class="search-bar">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="search-icon" viewBox="0 0 16 16">
        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
      </svg>
      <input type="text" placeholder="Rechercher..." aria-label="Rechercher">
    </div>
  </div>

    <div class="events mb-5">
      <h3>Nos prochains événements</h3>
      <div class="defilement">
        <button type="button" class="defilement-precedent" aria-label="Événements précédents" hidden>‹</button>
        <div class="events-post defilement-piste">
          <?php foreach ($evenements as $evenement): ?>
            <a href="pages/evenement.php?id=<?= $evenement['id'] ?>" class="event-carte" id="evenement-<?= $evenement['id'] ?>">
              <img src="<?= htmlspecialchars($evenement['image_path']) ?>" alt="Affiche : <?= htmlspecialchars($evenement['titre']) ?>">
              <span class="event-date">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                <?= htmlspecialchars(periode_evenement($evenement['titre'])) ?>
              </span>
            </a>
          <?php endforeach; ?>
          <img src="assets/images/affiches/affiche1.png" alt="Affiche événement 1">
          <img src="assets/images/affiches/affiche2.jpg" alt="Affiche événement 2">
          <img src="assets/images/affiches/affiche3.jpg" alt="Affiche événement 3">
        </div>
        <button type="button" class="defilement-suivant" aria-label="Événements suivants" hidden>›</button>
      </div>
    </div>

  <div class="activity container my-4">
    <h3 class="fw-normal mb-3">Nos activités</h3>
    <?php
    // Les 8 sports du menu Activités, chacun mène à sa page
    $sports = [
        'Football' => ['football.php', 'football.jpg'],
        'Basketball' => ['basketball.php', 'basketball.jpg'],
        'Tennis' => ['tennis.php', 'tennis.jpg'],
        'Handball' => ['handball.php', 'handball.jpg'],
        'Disc golf' => ['disc-golf.php', 'Disc golf.jpg'],
        'Hobby horsing' => ['hobby-horsing.php', 'Hobby-Horsing.jpg'],
        'Cycle-ball' => ['cycle-ball.php', 'Cycle-Ball.jpg'],
        'Quidditch' => ['quidditch.php', 'Quidditch.jpg'],
    ];
    ?>
    <div class="defilement">
      <button type="button" class="defilement-precedent" aria-label="Activités précédentes" hidden>‹</button>
      <div class="activites-piste defilement-piste">
        <?php foreach ($sports as $nom => [$page, $image]): ?>
          <a href="pages/activity/<?= $page ?>" class="activite-carte">
            <div class="ratio ratio-4x3">
              <img src="assets/images/sports/<?= rawurlencode($image) ?>" alt="<?= $nom ?>" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0"><?= $nom ?></p>
          </a>
        <?php endforeach; ?>
      </div>
      <button type="button" class="defilement-suivant" aria-label="Activités suivantes" hidden>›</button>
    </div>

    <a href="pages/activity.php" class="d-inline-block mt-4 text-decoration-none text-black">
      Voir plus d'activités
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-up-right">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M17 7l-10 10" />
        <path d="M8 7l9 0l0 9" />
      </svg>
    </a>
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
            referrerpolicy="strict-origin-when-cross-origin"></iframe>
      </div>
      <p class="fs-4 lh-lg mt-5">
        Idéalement situé, notre OMS est à proximité immédiate de nombreuses salles et zones sportives. Un emplacement privilégié qui nous permet de vous offrir un large choix d'activités dans des infrastructures de qualité.
      </p>
    </div>  </main>
  <?php require __DIR__ . '/components/footer.php'; ?>
  <script src="assets/scripts/defilement.js?v=<?= @filemtime(__DIR__ . '/assets/scripts/defilement.js') ?>"></script>
</body>
</html>
