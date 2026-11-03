<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';

// Stage choisi sur la page des stages (inscription.php?stage=12)
$stage = null;
if (isset($_GET['stage'])) {
    try {
        $stmt = db()->prepare('SELECT id, debut, fin, intitule, places_diponibles FROM stage WHERE id = ?');
        $stmt->execute([(int) $_GET['stage']]);
        $stage = $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        $stage = null;
    }
}

$erreurs = [];
$champs = ['nom_enfant', 'prenom_enfant', 'age_enfant', 'telephone', 'nom_parent', 'prenom_parent', 'email'];
$valeurs = array_fill_keys($champs, '');

if ($stage && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($champs as $champ) {
        $valeurs[$champ] = trim($_POST[$champ] ?? '');
    }
    $age = filter_var($valeurs['age_enfant'], FILTER_VALIDATE_INT);

    if (in_array('', $valeurs, true)) {
        $erreurs[] = 'Veuillez remplir tous les champs.';
    }
    if ($valeurs['age_enfant'] !== '' && ($age === false || $age < 3 || $age > 17)) {
        $erreurs[] = 'L\'âge de l\'enfant doit être compris entre 3 et 17 ans.';
    }
    if ($valeurs['email'] !== '' && !filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L\'adresse email n\'est pas valide.';
    }
    if ($valeurs['telephone'] !== '' && !preg_match('/^[0-9 +().-]{10,20}$/', $valeurs['telephone'])) {
        $erreurs[] = 'Le numéro de téléphone n\'est pas valide.';
    }
    if ($stage['fin'] < date('Y-m-d')) {
        $erreurs[] = 'Ce stage est terminé, les inscriptions sont fermées.';
    }

    if (!$erreurs) {
        $pdo = db();
        // Réserve une place : ne fait rien si le stage est déjà complet
        $reserve = $pdo->prepare('UPDATE stage SET places_diponibles = places_diponibles - 1 WHERE id = ? AND places_diponibles > 0');
        $reserve->execute([$stage['id']]);

        if ($reserve->rowCount() === 0) {
            $erreurs[] = 'Désolé, ce stage est complet.';
        } else {
            try {
                // La colonne ID n'a pas d'AUTO_INCREMENT : on calcule le prochain numéro
                $id = (int) $pdo->query('SELECT COALESCE(MAX(ID), 0) + 1 FROM inscription')->fetchColumn();
                $pdo->prepare('INSERT INTO inscription (ID, Nom_enfant, Prenom_enfant, Age_enfant, Nom_parent, Prenom_parent, Mail__parent, Numero_tel_parent, id_stage)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
                    ->execute([
                        $id,
                        $valeurs['nom_enfant'],
                        $valeurs['prenom_enfant'],
                        $age,
                        $valeurs['nom_parent'],
                        $valeurs['prenom_parent'],
                        $valeurs['email'],
                        $valeurs['telephone'],
                        $stage['id'],
                    ]);
                $_SESSION['flash'] = 'L\'inscription de ' . $valeurs['prenom_enfant'] . ' au stage du '
                    . date('d/m/Y', strtotime($stage['debut'])) . ' au ' . date('d/m/Y', strtotime($stage['fin'])) . ' est bien enregistrée.';
                header('Location: stages.php');
                exit();
            } catch (PDOException $e) {
                // L'inscription a échoué : on rend la place réservée
                $pdo->prepare('UPDATE stage SET places_diponibles = places_diponibles + 1 WHERE id = ?')->execute([$stage['id']]);
                error_log('Inscription impossible : ' . $e->getMessage());
                $erreurs[] = 'L\'inscription n\'a pas pu être enregistrée. Veuillez réessayer plus tard.';
            }
        }
    }
}

function valeur(array $valeurs, string $champ): string
{
    return htmlspecialchars($valeurs[$champ]);
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Inscription</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
  <link rel="stylesheet" href="../assets/styles/inscription.css?v=<?= @filemtime(__DIR__ . '/../assets/styles/inscription.css') ?>">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main>
<div class="Formulaire">
    <div class="rounded-3 p-4 my-4 mx-auto" style="max-width: 600px;">
      <div class="ustify-content-center">
        <div class="w-100" style="max-width: 600px;">

          <h1 class="text-center fs-4 fw-gras mb-4">
            Formulaire d'Inscription
          </h1>

          <?php if (!$stage): ?>
          <p class="text-center">
            Choisissez d'abord le stage auquel inscrire votre enfant.
          </p>
          <a href="stages.php" class="btn btn-light border rounded-pill d-block mx-auto mt-3 px-4" style="width: fit-content;">
            Voir les stages
          </a>
          <?php else: ?>
          <div class="bg-white rounded-3 p-3 mb-4 text-center">
            <strong><?= htmlspecialchars($stage['intitule']) ?></strong><br>
            Du <?= date('d/m/Y', strtotime($stage['debut'])) ?> au <?= date('d/m/Y', strtotime($stage['fin'])) ?>
            · <?= (int) $stage['places_diponibles'] ?> places<br>
            <a href="stages.php" class="small text-black">Changer de stage</a>
          </div>
          <?php if ($erreurs): ?>
          <div class="alert alert-danger" role="alert">
            <?= implode('<br>', array_map('htmlspecialchars', $erreurs)) ?>
          </div>
          <?php endif; ?>

          <?php if ((int) $stage['places_diponibles'] <= 0): ?>
          <p class="text-center">Désolé, ce stage est complet.</p>
          <?php else: ?>
          <form method="POST" action="inscription.php?stage=<?= $stage['id'] ?>">
            <div class="row g-3">

              <div class="col-6">
                <label class="form-label small" for="nom_enfant">Nom de l'enfant</label>
                <input type="text" id="nom_enfant" name="nom_enfant" maxlength="100" required value="<?= valeur($valeurs, 'nom_enfant') ?>" class="form-control form-control-sm" placeholder="Entrez le nom">
              </div>

              <div class="col-6">
                <label class="form-label small" for="prenom_enfant">Prénom de l'enfant</label>
                <input type="text" id="prenom_enfant" name="prenom_enfant" maxlength="100" required value="<?= valeur($valeurs, 'prenom_enfant') ?>" class="form-control form-control-sm" placeholder="Entrez le prénom">
              </div>

              <div class="col-6">
                <label class="form-label small" for="age_enfant">Âge de l'enfant</label>
                <input type="number" id="age_enfant" name="age_enfant" min="3" max="17" required value="<?= valeur($valeurs, 'age_enfant') ?>" class="form-control form-control-sm" placeholder="Entrez l'âge">
              </div>

              <div class="col-6">
                <label class="form-label small" for="telephone">Numéro téléphone</label>
                <input type="tel" id="telephone" name="telephone" maxlength="20" required value="<?= valeur($valeurs, 'telephone') ?>" class="form-control form-control-sm" placeholder="Votre numéro">
              </div>

              <div class="col-6">
                <label class="form-label small" for="nom_parent">Nom du tuteur légal</label>
                <input type="text" id="nom_parent" name="nom_parent" maxlength="100" required value="<?= valeur($valeurs, 'nom_parent') ?>" class="form-control form-control-sm" placeholder="Votre nom">
              </div>

              <div class="col-6">
                <label class="form-label small" for="prenom_parent">Prénom du tuteur légal</label>
                <input type="text" id="prenom_parent" name="prenom_parent" maxlength="100" required value="<?= valeur($valeurs, 'prenom_parent') ?>" class="form-control form-control-sm" placeholder="Votre prénom">
              </div>

              <div class="col-12">
                <label class="form-label small" for="email">Email du tuteur légal</label>
                <input type="email" id="email" name="email" maxlength="100" required value="<?= valeur($valeurs, 'email') ?>" class="form-control form-control-sm" placeholder="Votre email">
              </div>

            </div>

            <button type="submit" class="btn btn-light border rounded-pill
                                               d-block mx-auto mt-4 px-4">
              Inscription
            </button>
          </form>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>




  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

