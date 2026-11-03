<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';

// ?activite=Football : seulement les stages de cette activité (bouton S'inscrire des pages sport)
$activite = trim($_GET['activite'] ?? '');

// Stages pas encore terminés, du plus proche au plus lointain.
// intitule contient la liste des activités séparées par ", " (ex. "Football, Tennis").
$sql = 'SELECT id, debut, fin, intitule, description, places_diponibles, image_path FROM stage WHERE fin >= CURDATE()';
$params = [];
if ($activite !== '') {
    $sql .= " AND FIND_IN_SET(?, REPLACE(intitule, ', ', ',')) > 0";
    $params[] = $activite;
}
$sql .= ' ORDER BY debut, fin';

try {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $stages = $stmt->fetchAll();
} catch (PDOException $e) {
    $stages = null;
}

// Chemin d'image stocké en base (relatif à frontend/) vers une URL depuis pages/
function url_image(string $chemin): string
{
    return '../' . implode('/', array_map('rawurlencode', explode('/', $chemin)));
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Stages sportif</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
  <link rel="stylesheet" href="../assets/styles/stages.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/stages.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main class="stages">
    <?php if ($activite !== ''): ?>
      <h1>Nos stages de <?= htmlspecialchars(mb_strtolower($activite)) ?></h1>
      <p class="stages-intro">Choisissez un stage pour vous inscrire. <a href="stages.php">Voir tous les stages</a></p>
    <?php else: ?>
      <h1>Nos stages sportifs</h1>
    <?php endif; ?>

    <?php if ($stages === null): ?>
      <p class="stages-vide">Les stages ne peuvent pas être affichés pour le moment.</p>
    <?php elseif (!$stages && $activite !== ''): ?>
      <p class="stages-vide">Aucun stage de <?= htmlspecialchars(mb_strtolower($activite)) ?> n'est prévu pour le moment.</p>
    <?php elseif (!$stages): ?>
      <p class="stages-vide">Aucun stage n'est prévu pour le moment.</p>
    <?php endif; ?>

    <?php if ($stages): ?>
      <div class="defilement">
        <button type="button" class="defilement-precedent" aria-label="Stages précédents" hidden>‹</button>
        <div class="stages-liste defilement-piste">
          <?php foreach ($stages as $stage): ?>
            <article class="stage-carte" id="stage-<?= $stage['id'] ?>">
              <img src="<?= htmlspecialchars(url_image($stage['image_path'])) ?>" alt="<?= htmlspecialchars($stage['intitule']) ?>">
              <h2><?= htmlspecialchars($stage['intitule']) ?></h2>
              <p class="stage-dates">Du <?= date('d/m/Y', strtotime($stage['debut'])) ?> au <?= date('d/m/Y', strtotime($stage['fin'])) ?></p>
              <p class="stage-description"><?= htmlspecialchars($stage['description']) ?></p>
              <p class="stage-places"><?= (int) $stage['places_diponibles'] ?> places</p>
              <a class="stage-bouton" href="inscription.php?stage=<?= $stage['id'] ?>">S’inscrire</a>
            </article>
          <?php endforeach; ?>
        </div>
        <button type="button" class="defilement-suivant" aria-label="Stages suivants" hidden>›</button>
      </div>
    <?php endif; ?>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
  <script src="../assets/scripts/defilement.js?v=<?= @filemtime(__DIR__ . '/../assets/scripts/defilement.js') ?>"></script>
</body>
</html>
