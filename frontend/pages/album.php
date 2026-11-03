<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';

try {
    $pdo = db();
    $albums = $pdo->query('SELECT id, nom, date_publication FROM album ORDER BY date_publication DESC')->fetchAll();
    $photos = [];
    foreach ($pdo->query('SELECT path, id_album FROM photo ORDER BY id') as $photo) {
        $photos[$photo['id_album']][] = $photo['path'];
    }
} catch (PDOException $e) {
    $albums = null;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Albums photo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
  <link rel="stylesheet" href="../assets/styles/stages.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/stages.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main class="albums">
    <h1>Nos albums photo</h1>

    <?php if ($albums === null): ?>
      <p class="stages-vide">Les albums ne peuvent pas être affichés pour le moment.</p>
    <?php elseif (!$albums): ?>
      <p class="stages-vide">Aucun album pour le moment.</p>
    <?php endif; ?>

    <?php foreach ($albums ?? [] as $album): ?>
      <section class="album" id="album-<?= $album['id'] ?>">
        <h2><?= htmlspecialchars($album['nom']) ?></h2>
        <p class="album-date">Publié le <?= date('d/m/Y', strtotime($album['date_publication'])) ?></p>
        <div class="album-photos">
          <?php foreach ($photos[$album['id']] ?? [] as $chemin): ?>
            <img src="../<?= htmlspecialchars($chemin) ?>" alt="Photo de l'album <?= htmlspecialchars($album['nom']) ?>" loading="lazy">
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
