<?php
// Page d'un album : toutes ses photos, agrandissement au clic, et mode sélection
// (télécharger les photos choisies en ZIP ; les admins peuvent aussi les retirer de l'album).
session_start();
require_once __DIR__ . '/../../backend/db.php';

$id = (int) ($_GET['id'] ?? 0);
try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, nom, date_publication FROM album WHERE id = ?');
    $stmt->execute([$id]);
    $album = $stmt->fetch();
    $photos = [];
    if ($album) {
        $stmt = $pdo->prepare('SELECT id, path FROM photo WHERE id_album = ? ORDER BY id');
        $stmt->execute([$id]);
        $photos = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $album = null;
}

if (!$album) {
    http_response_code(404);
}

$estAdmin = !empty($_SESSION['user_id']);
if ($estAdmin && empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - <?= $album ? htmlspecialchars($album['nom']) : 'Album introuvable' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
  <link rel="stylesheet" href="../assets/styles/stages.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/stages.css') ?>">
  <link rel="stylesheet" href="../assets/styles/album.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/album.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main class="albums">
    <a href="album.php" class="evenement-retour">‹ Tous les albums</a>

    <?php if (!$album): ?>
      <p class="stages-vide">Cet album n'existe pas ou a été supprimé.</p>
    <?php else: ?>
      <div class="album-entete">
        <div>
          <h1 class="mb-1"><?= htmlspecialchars($album['nom']) ?></h1>
          <p class="album-date">
            <?= count($photos) ?> photo<?= count($photos) > 1 ? 's' : '' ?>
            · publié le <?= date('d/m/Y', strtotime($album['date_publication'])) ?>
          </p>
        </div>
        <?php if ($photos): ?>
          <button type="button" class="album-bouton" id="mode-selection" aria-pressed="false">Sélectionner</button>
        <?php endif; ?>
      </div>

      <?php if (!$photos): ?>
        <p class="stages-vide">Cet album ne contient aucune photo.</p>
      <?php endif; ?>

      <form method="POST" id="form-selection" class="album-galerie">
        <?php foreach ($photos as $i => $photo): ?>
          <label class="album-photo" data-index="<?= $i ?>">
            <input type="checkbox" name="photos[]" value="<?= $photo['id'] ?>" class="album-case" tabindex="-1">
            <img src="../<?= htmlspecialchars($photo['path']) ?>" alt="Photo <?= $i + 1 ?> de l'album <?= htmlspecialchars($album['nom']) ?>" loading="lazy">
            <span class="album-coche" aria-hidden="true">✓</span>
          </label>
        <?php endforeach; ?>
        <input type="hidden" name="id_album" value="<?= $album['id'] ?>">
        <?php if ($estAdmin): ?>
          <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <?php endif; ?>
      </form>

      <div class="album-barre" id="barre-selection" hidden>
        <span id="compteur">0 photo sélectionnée</span>
        <button type="button" class="album-lien" id="tout-selectionner">Tout sélectionner</button>
        <span class="ms-auto d-flex gap-2">
          <button type="submit" form="form-selection" formaction="album-telecharger.php" class="album-bouton" data-action disabled>Télécharger</button>
          <?php if ($estAdmin): ?>
            <button type="submit" form="form-selection" formaction="admin/photos-supprimer.php" class="album-bouton album-bouton-danger" data-action disabled
              onclick="return confirm('Retirer définitivement les photos sélectionnées de l\'album ?');">Retirer de l’album</button>
          <?php endif; ?>
          <button type="button" class="album-bouton album-bouton-clair" id="annuler-selection">Annuler</button>
        </span>
      </div>

      <dialog class="album-visionneuse" id="visionneuse" aria-label="Photo agrandie">
        <button type="button" class="visionneuse-fermer" aria-label="Fermer">×</button>
        <button type="button" class="visionneuse-precedent" aria-label="Photo précédente">‹</button>
        <img src="" alt="">
        <button type="button" class="visionneuse-suivant" aria-label="Photo suivante">›</button>
      </dialog>
    <?php endif; ?>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>

  <?php if ($album && $photos): ?>
  <script>
    const galerie = document.getElementById('form-selection');
    const cases = [...galerie.querySelectorAll('.album-case')];
    const boutonMode = document.getElementById('mode-selection');
    const barre = document.getElementById('barre-selection');
    const compteur = document.getElementById('compteur');
    const actions = barre.querySelectorAll('[data-action]');
    const toutSelectionner = document.getElementById('tout-selectionner');
    let enSelection = false;

    function majCompteur() {
      const n = cases.filter((c) => c.checked).length;
      compteur.textContent = n + (n > 1 ? ' photos sélectionnées' : ' photo sélectionnée');
      actions.forEach((b) => (b.disabled = n === 0));
      toutSelectionner.textContent = n === cases.length ? 'Tout désélectionner' : 'Tout sélectionner';
      cases.forEach((c) => c.parentElement.classList.toggle('choisie', c.checked));
    }

    function basculerMode(actif) {
      enSelection = actif;
      galerie.classList.toggle('en-selection', actif);
      barre.hidden = !actif;
      boutonMode.setAttribute('aria-pressed', actif);
      boutonMode.textContent = actif ? 'Terminer' : 'Sélectionner';
      cases.forEach((c) => {
        c.tabIndex = actif ? 0 : -1;
        if (!actif) c.checked = false;
      });
      majCompteur();
    }

    boutonMode.addEventListener('click', () => basculerMode(!enSelection));
    document.getElementById('annuler-selection').addEventListener('click', () => basculerMode(false));
    toutSelectionner.addEventListener('click', () => {
      const tout = cases.every((c) => c.checked);
      cases.forEach((c) => (c.checked = !tout));
      majCompteur();
    });
    galerie.addEventListener('change', majCompteur);

    // Hors mode sélection, un clic agrandit la photo
    const visionneuse = document.getElementById('visionneuse');
    const grande = visionneuse.querySelector('img');
    const images = cases.map((c) => c.nextElementSibling);
    let courante = 0;
    function afficher(i) {
      courante = (i + images.length) % images.length;
      grande.src = images[courante].src;
      grande.alt = images[courante].alt;
    }
    galerie.addEventListener('click', (e) => {
      const photo = e.target.closest('.album-photo');
      if (!photo || enSelection) return;
      e.preventDefault(); // n'active pas la case à cocher
      afficher(+photo.dataset.index);
      visionneuse.showModal();
    });
    visionneuse.querySelector('.visionneuse-fermer').addEventListener('click', () => visionneuse.close());
    visionneuse.querySelector('.visionneuse-precedent').addEventListener('click', () => afficher(courante - 1));
    visionneuse.querySelector('.visionneuse-suivant').addEventListener('click', () => afficher(courante + 1));
    visionneuse.addEventListener('click', (e) => { if (e.target === visionneuse) visionneuse.close(); });
    visionneuse.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') afficher(courante - 1);
      if (e.key === 'ArrowRight') afficher(courante + 1);
    });
    if (images.length < 2) visionneuse.querySelectorAll('.visionneuse-precedent, .visionneuse-suivant').forEach((b) => b.hidden = true);
  </script>
  <?php endif; ?>
</body>
</html>
