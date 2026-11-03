<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';
require_once __DIR__ . '/../components/evenement.php';

// Événement choisi sur l'accueil (evenement.php?id=3)
try {
    $stmt = db()->prepare('SELECT id, titre, description, image_path, date_publication FROM actualite WHERE id = ?');
    $stmt->execute([(int) ($_GET['id'] ?? 0)]);
    $evenement = $stmt->fetch() ?: null;
} catch (PDOException $e) {
    $evenement = null;
}

if (!$evenement) {
    http_response_code(404);
}
$places = $evenement ? places_evenement($evenement['description']) : null;
// Date de fin, enregistrée dans le titre ("... au 07/11/2026")
$termine = $evenement && preg_match('#au (\d{2})/(\d{2})/(\d{4})#', $evenement['titre'], $m)
    && "$m[3]-$m[2]-$m[1]" < date('Y-m-d');
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - <?= $evenement ? 'Événement' : 'Événement introuvable' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css?v=<?= filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
  <link rel="stylesheet" href="../assets/styles/stages.css?v=<?= filemtime(__DIR__ . '/../assets/styles/stages.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main class="evenement">
    <a href="../index.php#evenement-<?= $evenement['id'] ?? '' ?>" class="evenement-retour">‹ Retour aux événements</a>

    <?php if (!$evenement): ?>
      <h1>Événement introuvable</h1>
      <p class="stages-vide">Cet événement n'existe pas ou a été supprimé.</p>
    <?php else: ?>
      <div class="evenement-contenu">
        <img src="../<?= htmlspecialchars($evenement['image_path']) ?>" alt="Affiche de l'événement" class="evenement-affiche">
        <div class="evenement-infos">
          <h1>Événement</h1>
          <p class="evenement-date">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
            <?= htmlspecialchars(periode_evenement($evenement['titre'])) ?>
          </p>
          <?php if ($places !== null): ?>
            <p><?= $places ?> places</p>
          <?php endif; ?>
          <p class="evenement-publie">Publié le <?= date('d/m/Y', strtotime($evenement['date_publication'])) ?></p>
          <?php if ($termine): ?>
            <p class="stages-vide">Cet événement est terminé.</p>
          <?php elseif ($places !== null && $places <= 0): ?>
            <p class="stages-vide">Cet événement est complet.</p>
          <?php else: ?>
            <a href="inscription.php?evenement=<?= $evenement['id'] ?>" class="stage-bouton">S’inscrire</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
