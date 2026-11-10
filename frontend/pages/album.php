<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';

try {
    $pdo = db();
    // Chaque album avec sa première photo (couverture) et son nombre de photos
    $albums = $pdo->query('SELECT a.id, a.nom, a.date_publication, COUNT(p.id) AS nb_photos,
                                  (SELECT path FROM photo WHERE id_album = a.id ORDER BY id LIMIT 1) AS couverture
                           FROM album a LEFT JOIN photo p ON p.id_album = a.id
                           GROUP BY a.id, a.nom, a.date_publication
                           ORDER BY a.date_publication DESC')->fetchAll();
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
  <link rel="stylesheet" href="../assets/styles/album.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/album.css') ?>">
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

    <div class="albums-grille">
      <?php foreach ($albums ?? [] as $album): ?>
        <a href="album-detail.php?id=<?= $album['id'] ?>" class="album-carte" id="album-<?= $album['id'] ?>">
          <?php if ($album['couverture']): ?>
            <img src="../<?= htmlspecialchars($album['couverture']) ?>" alt="" loading="lazy">
          <?php else: ?>
            <span class="album-carte-vide">Aucune photo</span>
          <?php endif; ?>
          <h2><?= htmlspecialchars($album['nom']) ?></h2>
          <p class="album-date">
            <?= $album['nb_photos'] ?> photo<?= $album['nb_photos'] > 1 ? 's' : '' ?>
            · publié le <?= date('d/m/Y', strtotime($album['date_publication'])) ?>
          </p>
        </a>
      <?php endforeach; ?>
    </div>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
