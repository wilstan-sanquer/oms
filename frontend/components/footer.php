<footer class="site-footer mt-5">
  <div class="container py-5">
    <div class="row gy-4">
      <div class="col-12 col-md-6">
        <h5 class="footer-title">Horaires</h5>
        <ul class="list-unstyled mb-0">
          <li>Du lundi au jeudi : 8h - 12h &amp; 14h - 18h</li>
          <li>Le vendredi : 8h - 12h &amp; 14h - 17h</li>
        </ul>
      </div>
      <div class="col-12 col-md-6">
        <h5 class="footer-title">Navigation</h5>
        <ul class="list-unstyled mb-0">
          <li><a href="<?= $frontendPath ?>/pages/about.php">l'OMS</a></li>
          <li><a href="<?= $frontendPath ?>/pages/activity.php">Nos activités</a></li>
          <li><a href="<?= $frontendPath ?>/pages/stages.php">Nos stages</a></li>
          <li><a href="<?= $frontendPath ?>/pages/contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom text-center py-3">
    <small>&copy; <?= date('Y') ?> OMS. Tous droits réservés.</small>
  </div>
</footer>

<style>
  .site-footer {
    background-color: #e9e3d5;
    color: #212529;
  }

  .site-footer a {
    color: #212529;
    text-decoration: none;
  }
</style>