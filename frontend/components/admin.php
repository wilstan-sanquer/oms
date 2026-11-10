<?php
// Éléments communs des pages d'administration (pages/admin/*.php).
session_start();
require_once __DIR__ . '/../../backend/db.php';

// Seuls les admins ont accès à ces pages (tous les comptes de la table users sont admins)
if (empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Jeton anti-CSRF pour les formulaires
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

function verifier_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Formulaire expiré, rechargez la page.');
    }
}

function champ_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

// Les colonnes id n'ont pas d'AUTO_INCREMENT : on calcule le prochain numéro.
// À appeler dans une transaction (FOR UPDATE bloque les insertions simultanées).
function prochain_id(PDO $pdo, string $table): int
{
    if (!in_array($table, ['stage', 'actualite', 'album', 'photo', 'users'], true)) {
        throw new InvalidArgumentException("Table inconnue : $table");
    }
    return (int) $pdo->query("SELECT COALESCE(MAX(id), 0) + 1 FROM `$table` FOR UPDATE")->fetchColumn();
}

// Nom affiché d'un animateur (ligne de la table users). Les comptes de connexion comme « Admin »
// n'ont pas forcément de nom ni de prénom : on affiche alors leur identifiant.
function nom_animateur(array $user): string
{
    $nom = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
    if ($nom !== '') {
        return $nom;
    }
    return trim($user['username'] ?? '') !== '' ? $user['username'] : 'Animateur n°' . $user['id'];
}

function date_fr(string $date): string
{
    return date('d/m/Y', strtotime($date));
}

// Enregistre une image envoyée par formulaire dans assets/images/uploads/<dossier>/.
// Retourne le chemin relatif au dossier frontend (stocké en base), ou null si invalide.
function enregistrer_image(array $fichier, string $dossier): ?string
{
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

    if ($fichier['error'] !== UPLOAD_ERR_OK || $fichier['size'] > 8 * 1024 * 1024) {
        return null;
    }
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
    if (!isset($extensions[$type])) {
        return null;
    }

    $relatif = 'assets/images/uploads/' . $dossier;
    $absolu = __DIR__ . '/../' . $relatif;
    if (!is_dir($absolu)) {
        mkdir($absolu, 0775, true);
    }
    $nom = bin2hex(random_bytes(8)) . '.' . $extensions[$type];
    if (!move_uploaded_file($fichier['tmp_name'], "$absolu/$nom")) {
        return null;
    }
    return "$relatif/$nom";
}

function supprimer_image(?string $chemin): void
{
    if ($chemin && str_starts_with($chemin, 'assets/images/uploads/')) {
        @unlink(__DIR__ . '/../' . $chemin);
    }
}

function admin_entete(string $titre): void
{
    ?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Administration - <?= htmlspecialchars($titre) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;700&family=Open+Sans:wght@600;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/styles/admin.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/admin.css') ?>">
</head>

<body>
  <header class="admin-entete">
    <a href="../../index.php" class="admin-retour" aria-label="Retour au site">
      <img src="../../assets/images/logo.png" alt="Logo OMS" height="70">
    </a>
    <span class="admin-titre">Administration</span>
    <a href="../logout.php" class="admin-deconnexion">Déconnexion</a>
  </header>
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center m-3" role="alert">
      <?= htmlspecialchars($_SESSION['flash']) ?>
      <button type="button" class="btn-close" aria-label="Fermer" onclick="this.parentElement.remove()"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>
    <?php
}

function admin_erreurs(array $erreurs): void
{
    if ($erreurs) {
        echo '<div class="alert alert-danger">' . implode('<br>', array_map('htmlspecialchars', $erreurs)) . '</div>';
    }
}

function admin_pied(): void
{
    ?>
  <script>
    // La date de fin ne peut pas être avant la date de début
    document.querySelectorAll('form').forEach((form) => {
      const debut = form.querySelector('input[name="debut"]');
      const fin = form.querySelector('input[name="fin"]');
      if (!debut || !fin) return;
      const maj = () => {
        fin.min = debut.value;
        if (debut.value && fin.value && fin.value < debut.value) {
          fin.value = debut.value;
        }
      };
      debut.addEventListener('change', maj);
      maj();
    });

    // Listes à cocher : affiche les choix dans le résumé
    document.querySelectorAll('details.choix').forEach((d) => {
      const resume = d.querySelector('summary');
      const vide = resume.textContent;
      const maj = () => {
        const choisis = [...d.querySelectorAll('input:checked')].map((c) => c.parentElement.textContent.trim());
        resume.textContent = choisis.length ? choisis.join(', ') : vide;
      };
      d.addEventListener('change', maj);
      maj();
    });
    // Ferme les listes quand on clique ailleurs
    document.addEventListener('click', (e) => {
      document.querySelectorAll('details.choix[open]').forEach((d) => {
        if (!d.contains(e.target)) d.open = false;
      });
    });
  </script>
</body>

</html>
    <?php
}
