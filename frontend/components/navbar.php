<?php
$frontendPath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if (in_array(basename($frontendPath), ['activity', 'admin'], true)) {
    $frontendPath = dirname($frontendPath);
}
if (basename($frontendPath) === 'pages') {
    $frontendPath = dirname($frontendPath);
}
$frontendPath = rtrim(str_replace('\\', '/', $frontendPath), '/');
?>
<nav class="d-flex justify-content-between border p-1 align-items-center">
  <div class="d-flex align-items-center gap-1">
    <img src="<?= $frontendPath ?>/assets/images/logo.png" alt="Logo OMS" height="70">
    <a href="<?= $frontendPath ?>/index.php" class="text-decoration-none text-black" style="margin-left: -10px;" aria-label="Accueil"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" /></svg></a>
  </div>
  <div class="d-flex gap-4 list-unstyled align-items-center">
    <a href="<?= $frontendPath ?>/pages/about.php" class="text-decoration-none text-black">l'OMS</a>
    <a href="<?= $frontendPath ?>/pages/activity.php" class="text-decoration-none text-black">Activités</a>
    <a href="<?= $frontendPath ?>/pages/stages.php" class="text-decoration-none text-black">Stages sportif</a>
    <a href="<?= $frontendPath ?>/pages/album.php" class="text-decoration-none text-black">Albums photo</a>
    <a href="<?= $frontendPath ?>/pages/contact.php" class="text-decoration-none text-black">Contact</a>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <div class="admin-menu position-relative">
        <button type="button" class="btn btn-link p-0 text-black text-decoration-none dropdown-toggle" aria-expanded="false" aria-haspopup="true"
          onclick="const m = this.nextElementSibling; const open = m.hidden; m.hidden = !open; this.setAttribute('aria-expanded', open);">
          Admin
        </button>
        <ul class="admin-menu-liste list-unstyled position-absolute end-0 mt-2 py-2 bg-white border rounded-3 shadow-sm" hidden>
          <li><a href="<?= $frontendPath ?>/pages/admin/stages.php" class="d-block px-3 py-1 text-black text-decoration-none text-nowrap">Créer un stage</a></li>
          <li><a href="<?= $frontendPath ?>/pages/admin/evenements.php" class="d-block px-3 py-1 text-black text-decoration-none text-nowrap">Créer un événement</a></li>
          <li><a href="<?= $frontendPath ?>/pages/admin/album.php" class="d-block px-3 py-1 text-black text-decoration-none text-nowrap">Créer un album photo</a></li>
          <li><a href="<?= $frontendPath ?>/pages/admin/activites.php" class="d-block px-3 py-1 text-black text-decoration-none text-nowrap">Ajouter une activité</a></li>
          <li><a href="<?= $frontendPath ?>/pages/admin/animateurs.php" class="d-block px-3 py-1 text-black text-decoration-none text-nowrap">Gérer les animateurs</a></li>
        </ul>
      </div>
      <script>
        // Ferme le menu Admin quand on clique ailleurs
        document.addEventListener('click', (e) => {
          const menu = document.querySelector('.admin-menu');
          if (menu && !menu.contains(e.target)) {
            menu.querySelector('.admin-menu-liste').hidden = true;
            menu.querySelector('button').setAttribute('aria-expanded', 'false');
          }
        });
      </script>
      <style>
        .admin-menu-liste { z-index: 1000; min-width: 210px; }
        .admin-menu-liste a:hover { background-color: #e9e3d5; }
      </style>
    <?php endif; ?>
    <?php if (!empty($_SESSION['username'])): ?>
      <span class="d-flex align-items-center gap-1 fw-semibold" title="Vous êtes connecté">
        <span class="rounded-circle bg-success d-inline-block" style="width: 10px; height: 10px;"></span>
        <?= htmlspecialchars($_SESSION['username']) ?>
      </span>
      <a href="<?= $frontendPath ?>/pages/logout.php" class="text-decoration-none text-black">Déconnexion</a>
    <?php else: ?>
      <a href="<?= $frontendPath ?>/pages/login.php" class="text-decoration-none text-black">Connexion</a>
    <?php endif; ?>
  </div>
</nav>
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success d-flex justify-content-between align-items-center m-3" role="alert">
    <?= htmlspecialchars($_SESSION['flash']) ?>
    <button type="button" class="btn-close" aria-label="Fermer" onclick="this.parentElement.remove()"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>