<?php
// Gestion des animateurs (table users). Les animateurs ajoutés ici n'ont ni identifiant ni mot de passe :
// ils ne peuvent pas se connecter. Les comptes de connexion (ex. « Admin ») sont listés mais pas supprimables ici.
require_once __DIR__ . '/../../components/admin.php';

$pdo = db();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    if (isset($_POST['supprimer'])) {
        $stmt = $pdo->prepare('SELECT id, username, prenom, nom FROM users WHERE id = ?');
        $stmt->execute([(int) $_POST['supprimer']]);
        $animateur = $stmt->fetch();

        if (!$animateur) {
            $_SESSION['flash'] = 'Cet animateur a déjà été retiré.';
        } elseif (trim($animateur['username']) !== '') {
            $erreurs[] = 'Un compte de connexion ne peut pas être retiré depuis cette page.';
        } else {
            try {
                $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$animateur['id']]);
                $_SESSION['flash'] = nom_animateur($animateur) . ' a été retiré(e) des animateurs.';
            } catch (PDOException $e) {
                // Clé étrangère : l'animateur est l'auteur d'un stage, d'un album, d'un événement…
                $erreurs[] = nom_animateur($animateur) . ' ne peut pas être retiré(e) : des contenus du site sont enregistrés à son nom.';
            }
        }
        if (!$erreurs) {
            header('Location: animateurs.php');
            exit();
        }
    } else {
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($prenom === '' || $nom === '') {
            $erreurs[] = 'Veuillez indiquer le prénom et le nom de l\'animateur.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'L\'adresse e-mail n\'est pas valide.';
        }
        if (!$erreurs) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE LOWER(prenom) = LOWER(?) AND LOWER(nom) = LOWER(?)');
            $stmt->execute([$prenom, $nom]);
            if ($stmt->fetchColumn()) {
                $erreurs[] = $prenom . ' ' . $nom . ' fait déjà partie des animateurs.';
            }
        }

        if (!$erreurs) {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO users (id, username, password, nom, prenom, email) VALUES (?, \'\', \'\', ?, ?, ?)')
                ->execute([prochain_id($pdo, 'users'), $nom, $prenom, $email]);
            $pdo->commit();
            $_SESSION['flash'] = $prenom . ' ' . $nom . ' a été ajouté(e) aux animateurs.';
            header('Location: animateurs.php');
            exit();
        }
    }
}

$animateurs = $pdo->query('SELECT id, username, prenom, nom, email FROM users ORDER BY prenom, nom, username')->fetchAll();

admin_entete('Animateurs');
?>
<main class="admin-colonnes">
  <section class="admin-liste">
    <h2>Nos animateurs</h2>
    <?php if (!$animateurs): ?>
      <p class="admin-vide">Aucun animateur pour le moment.</p>
    <?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($animateurs as $animateur): ?>
        <li title="<?= htmlspecialchars($animateur['email']) ?>">
          <span>
            <?= htmlspecialchars(nom_animateur($animateur)) ?>
            <?php if (trim($animateur['username']) !== ''): ?>
              <small class="admin-vide">(compte administrateur)</small>
            <?php endif; ?>
          </span>
          <?php if (trim($animateur['username']) === ''): ?>
            <form method="POST" onsubmit="return confirm('Retirer cet animateur ?');">
              <?= champ_csrf() ?>
              <button type="submit" name="supprimer" value="<?= $animateur['id'] ?>" class="admin-supprimer" aria-label="Retirer <?= htmlspecialchars(nom_animateur($animateur)) ?>">×</button>
            </form>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="admin-vide mt-4">Les animateurs choisis dans un stage restent affichés sur ce stage même s'ils sont retirés ensuite.</p>
  </section>

  <section class="admin-formulaire">
    <h2>Ajouter un animateur</h2>
    <?php admin_erreurs($erreurs); ?>
    <form method="POST">
      <?= champ_csrf() ?>
      <div class="admin-ligne">
        <label>Prénom
          <input type="text" name="prenom" maxlength="200" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
        </label>
        <label>Nom
          <input type="text" name="nom" maxlength="200" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
        </label>
      </div>
      <label>E-mail (facultatif)
        <input type="email" name="email" maxlength="200" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </label>
      <button type="submit" class="admin-bouton">Ajouter l’animateur</button>
    </form>
  </section>
</main>
<?php admin_pied(); ?>
