<?php
// Gabarit commun des pages sport. La page appelante définit $sport :
// nom, titre, description, photo ['fichier', 'alt'], vignettes (optionnel).
$imagesPath = '../../assets/images/sports/';
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - <?= htmlspecialchars($sport['nom']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/styles/style.css">
  <link rel="stylesheet" href="../../assets/styles/sport.css">
</head>

<body>
  <?php require_once __DIR__ . '/navbar.php'; ?>
  <main class="activite">
    <section class="galerie">

      <div class="photo-grande">
        <img src="<?= $imagesPath . rawurlencode($sport['photo']['fichier']) ?>" alt="<?= htmlspecialchars($sport['photo']['alt']) ?>">
      </div>

      <?php if (!empty($sport['vignettes'])): ?>
        <div class="vignettes">
          <?php foreach ($sport['vignettes'] as $vignette): ?>
            <div class="photo">
              <img src="<?= $imagesPath . rawurlencode($vignette['fichier']) ?>" alt="<?= htmlspecialchars($vignette['alt']) ?>">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </section>

    <section class="presentation">

      <h1><?= htmlspecialchars($sport['titre']) ?></h1>

      <p><?= htmlspecialchars($sport['description']) ?></p>

      <a class="bouton" href="../inscription.php">S’inscrire</a>

    </section>

  </main>
  <?php require_once __DIR__ . '/footer.php'; ?>
</body>

</html>
