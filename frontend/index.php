<?php
session_start();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/evenement.php';
require_once __DIR__ . '/components/activites.php';

// Événements créés depuis l'administration (table actualite), du plus récent au plus ancien
try {
    $evenements = db()->query('SELECT id, titre, image_path FROM actualite ORDER BY date_publication DESC')->fetchAll();
} catch (PDOException $e) {
    $evenements = [];
}

// Avis des clients (formulaire en bas de page)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$erreursAvis = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $erreursAvis[] = 'Formulaire expiré, veuillez réessayer.';
    } elseif (isset($_POST['supprimer_avis']) && !empty($_SESSION['user_id'])) {
        db()->prepare('DELETE FROM avis WHERE id = ?')->execute([(int) $_POST['supprimer_avis']]);
        $_SESSION['flash'] = 'L\'avis a été supprimé.';
        header('Location: index.php#avis');
        exit();
    } elseif (($_POST['site'] ?? '') === '') { // champ piège invisible : rempli seulement par les robots
        $nomAvis = trim($_POST['nom'] ?? '');
        $messageAvis = trim($_POST['message'] ?? '');
        $noteAvis = (int) ($_POST['note'] ?? 0);
        if ($nomAvis === '' || mb_strlen($nomAvis) > 100) {
            $erreursAvis[] = 'Veuillez indiquer votre prénom (100 caractères maximum).';
        }
        if ($noteAvis < 1 || $noteAvis > 5) {
            $erreursAvis[] = 'Veuillez choisir une note de 1 à 5 étoiles.';
        }
        if ($messageAvis === '' || mb_strlen($messageAvis) > 1000) {
            $erreursAvis[] = 'Veuillez écrire votre avis (1000 caractères maximum).';
        }
        if (!$erreursAvis) {
            try {
                db()->prepare('INSERT INTO avis (nom, note, message, date_publication) VALUES (?, ?, ?, NOW())')
                    ->execute([$nomAvis, $noteAvis, $messageAvis]);
                $_SESSION['avis_ok'] = true; // message affiché dans la section avis
                header('Location: index.php#avis');
                exit();
            } catch (PDOException $e) {
                $erreursAvis[] = 'Votre avis n\'a pas pu être enregistré, veuillez réessayer plus tard.';
            }
        }
    }
}

try {
    $avis = db()->query('SELECT id, nom, note, message, date_publication FROM avis ORDER BY date_publication DESC, id DESC LIMIT 12')->fetchAll();
} catch (PDOException $e) {
    $avis = [];
}
// Tant qu'aucun client n'a laissé d'avis, on garde les témoignages d'origine
if (!$avis) {
    $avis = [
        ['id' => null, 'nom' => 'Stéphanie', 'note' => 5, 'message' => 'Mon enfant a passé d’excellentes vacances. Je recommande', 'date_publication' => null],
        ['id' => null, 'nom' => 'Patricia', 'note' => 5, 'message' => 'Une équipe d\'animateurs au top et à l\'écoute. Je réinscrirai mes enfants sans hésiter.', 'date_publication' => null],
        ['id' => null, 'nom' => 'Marc', 'note' => 5, 'message' => 'Inscriptions faciles et accueil chaleureux. Ma fille s\'est fait plein d\'amis cet été !', 'date_publication' => null],
    ];
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
    <?php $sports = liste_activites(); // sports de base + activités ajoutées depuis l'administration ?>
    <div class="defilement">
      <button type="button" class="defilement-precedent" aria-label="Activités précédentes" hidden>‹</button>
      <div class="activites-piste defilement-piste">
        <?php foreach ($sports as $nom => $activite): ?>
          <a href="<?= htmlspecialchars($activite['page']) ?>" class="activite-carte">
            <div class="ratio ratio-4x3">
              <img src="<?= src_image($activite['image']) ?>" alt="<?= htmlspecialchars($nom) ?>" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0"><?= htmlspecialchars($nom) ?></p>
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

    <section class="avis" id="avis">
      <div class="container-avis-fond">
        <h3 class="fw-normal text-center mb-4">Ils nous font confiance</h3>
        <div class="defilement">
          <button type="button" class="defilement-precedent" aria-label="Avis précédents" hidden>‹</button>
          <div class="container-avis defilement-piste">
            <?php foreach ($avis as $unAvis): ?>
              <article class="avis-carte bg-white p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <h5 class="mb-1"><?= htmlspecialchars($unAvis['nom']) ?></h5>
                  <?php if ($unAvis['id'] && !empty($_SESSION['user_id'])): ?>
                    <form method="POST" action="index.php#avis" onsubmit="return confirm('Supprimer cet avis ?');">
                      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                      <button type="submit" name="supprimer_avis" value="<?= $unAvis['id'] ?>" class="avis-supprimer" aria-label="Supprimer cet avis" title="Supprimer cet avis">×</button>
                    </form>
                  <?php endif; ?>
                </div>
                <p class="avis-etoiles mb-1" aria-label="<?= (int) $unAvis['note'] ?> sur 5"><?= str_repeat('★', (int) $unAvis['note']) . str_repeat('☆', 5 - (int) $unAvis['note']) ?></p>
                <p class="mb-1"><?= nl2br(htmlspecialchars($unAvis['message'])) ?></p>
                <?php if ($unAvis['date_publication']): ?>
                  <small class="text-muted">Le <?= date('d/m/Y', strtotime($unAvis['date_publication'])) ?></small>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
          <button type="button" class="defilement-suivant" aria-label="Avis suivants" hidden>›</button>
        </div>
      </div>

      <form method="POST" action="index.php#avis" class="avis-formulaire">
        <h4 class="fw-normal mb-3">Laissez-nous votre avis</h4>
        <?php if (!empty($_SESSION['avis_ok'])): ?>
          <div class="alert alert-success">Merci pour votre avis ! Il est maintenant visible ci-dessus.</div>
          <?php unset($_SESSION['avis_ok']); ?>
        <?php endif; ?>
        <?php if ($erreursAvis): ?>
          <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $erreursAvis)) ?></div>
        <?php endif; ?>
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <div class="avis-piege" aria-hidden="true">
          <label>Ne pas remplir <input type="text" name="site" tabindex="-1" autocomplete="off"></label>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="avis-nom" class="form-label">Votre prénom</label>
            <input type="text" id="avis-nom" name="nom" class="form-control" maxlength="100" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
          </div>
          <fieldset class="col-md-6">
            <legend class="form-label fs-6">Votre note</legend>
            <div class="avis-note">
              <?php for ($n = 5; $n >= 1; $n--): ?>
                <input type="radio" id="note-<?= $n ?>" name="note" value="<?= $n ?>" required <?= (int) ($_POST['note'] ?? 0) === $n ? 'checked' : '' ?>>
                <label for="note-<?= $n ?>" title="<?= $n ?> étoile<?= $n > 1 ? 's' : '' ?>">★</label>
              <?php endfor; ?>
            </div>
          </fieldset>
          <div class="col-12">
            <label for="avis-message" class="form-label">Votre avis</label>
            <textarea id="avis-message" name="message" class="form-control" rows="4" maxlength="1000" required placeholder="Racontez-nous votre expérience avec l’OMS…"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>
        </div>
        <button type="submit" class="avis-bouton mt-3">Publier mon avis</button>
      </form>
    </section>

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
