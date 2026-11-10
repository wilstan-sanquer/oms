<?php
session_start();
require_once __DIR__ . '/../components/activites.php';
$activites = liste_activites();
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Activités</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/activity.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/activity.css') ?>">
</head>

<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main>
    <div class="activity container my-4">
      <h3 class="fw-normal mb-3">Nos activités</h3>
      <div class="row row-cols-4 g-4">
        <?php foreach ($activites as $nom => $activite): ?>
          <a href="../<?= htmlspecialchars($activite['page']) ?>">
            <div class="col">
              <div class="ratio ratio-4x3">
                <img src="../<?= src_image($activite['image']) ?>" alt="<?= htmlspecialchars($nom) ?>" class="rounded-3 object-fit-cover">
              </div>
              <p class="mt-2 mb-0"><?= htmlspecialchars($nom) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>

</html>