<?php
session_start();
require_once __DIR__ . '/../../backend/db.php';
require_once __DIR__ . '/../components/evenement.php';

// On s'inscrit soit à un stage (inscription.php?stage=12), soit à un événement (inscription.php?evenement=3).
// $cible décrit ce qui a été choisi, quel que soit son type.
$cible = null;
try {
    if (isset($_GET['stage'])) {
        $stmt = db()->prepare('SELECT id, debut, fin, intitule, places_diponibles FROM stage WHERE id = ?');
        $stmt->execute([(int) $_GET['stage']]);
        if ($stage = $stmt->fetch()) {
            $cible = [
                'type' => 'stage',
                'id' => (int) $stage['id'],
                'nom' => $stage['intitule'],
                'periode' => 'Du ' . date('d/m/Y', strtotime($stage['debut'])) . ' au ' . date('d/m/Y', strtotime($stage['fin'])),
                'fin' => $stage['fin'],
                'places' => (int) $stage['places_diponibles'],
                'retour' => 'stages.php',
                'changer' => 'Changer de stage',
                'description' => null,
            ];
        }
    } elseif (isset($_GET['evenement'])) {
        $stmt = db()->prepare('SELECT id, titre, description FROM actualite WHERE id = ?');
        $stmt->execute([(int) $_GET['evenement']]);
        if ($evenement = $stmt->fetch()) {
            // Dates et places sont enregistrées dans le titre et la description (voir admin/evenements.php)
            $fin = preg_match('#au (\d{2})/(\d{2})/(\d{4})#', $evenement['titre'], $m) ? "$m[3]-$m[2]-$m[1]" : null;
            $cible = [
                'type' => 'evenement',
                'id' => (int) $evenement['id'],
                'nom' => 'Événement',
                'periode' => periode_evenement($evenement['titre']),
                'fin' => $fin,
                'places' => places_evenement($evenement['description']),
                'retour' => 'evenement.php?id=' . $evenement['id'],
                'changer' => 'Retour à l\'événement',
                'description' => $evenement['description'],
            ];
        }
    }
} catch (PDOException $e) {
    $cible = null;
}

$erreurs = [];
$champs = ['nom_enfant', 'prenom_enfant', 'age_enfant', 'telephone', 'nom_parent', 'prenom_parent', 'email'];
$valeurs = array_fill_keys($champs, '');

// Réserve une place. Retourne false si c'est complet.
function reserver_place(PDO $pdo, array $cible): bool
{
    if ($cible['type'] === 'stage') {
        $stmt = $pdo->prepare('UPDATE stage SET places_diponibles = places_diponibles - 1 WHERE id = ? AND places_diponibles > 0');
        $stmt->execute([$cible['id']]);
        return $stmt->rowCount() > 0;
    }
    if ($cible['places'] === null) {
        return true; // événement sans nombre de places : pas de limite
    }
    if ($cible['places'] <= 0) {
        return false;
    }
    // La condition sur l'ancienne description évite de compter deux fois la même place
    $stmt = $pdo->prepare('UPDATE actualite SET description = ? WHERE id = ? AND description = ?');
    $stmt->execute(['Places : ' . ($cible['places'] - 1), $cible['id'], $cible['description']]);
    return $stmt->rowCount() > 0;
}

function rendre_place(PDO $pdo, array $cible): void
{
    if ($cible['type'] === 'stage') {
        $pdo->prepare('UPDATE stage SET places_diponibles = places_diponibles + 1 WHERE id = ?')->execute([$cible['id']]);
    } elseif ($cible['places'] !== null) {
        $pdo->prepare('UPDATE actualite SET description = ? WHERE id = ?')->execute([$cible['description'], $cible['id']]);
    }
}

if ($cible && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
    if ($cible['fin'] !== null && $cible['fin'] < date('Y-m-d')) {
        $erreurs[] = ($cible['type'] === 'stage' ? 'Ce stage est terminé' : 'Cet événement est terminé') . ', les inscriptions sont fermées.';
    }

    if (!$erreurs) {
        $pdo = db();
        if (!reserver_place($pdo, $cible)) {
            $erreurs[] = $cible['type'] === 'stage' ? 'Désolé, ce stage est complet.' : 'Désolé, cet événement est complet.';
        } else {
            try {
                // La colonne ID n'a pas d'AUTO_INCREMENT : on calcule le prochain numéro
                $id = (int) $pdo->query('SELECT COALESCE(MAX(ID), 0) + 1 FROM inscription')->fetchColumn();
                // id_stage pour un stage, id_evenement pour un événement
                $colonneLien = $cible['type'] === 'stage' ? 'id_stage' : 'id_evenement';
                $pdo->prepare("INSERT INTO inscription (ID, Nom_enfant, Prenom_enfant, Age_enfant, Nom_parent, Prenom_parent, Mail__parent, Numero_tel_parent, $colonneLien)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([
                        $id,
                        $valeurs['nom_enfant'],
                        $valeurs['prenom_enfant'],
                        $age,
                        $valeurs['nom_parent'],
                        $valeurs['prenom_parent'],
                        $valeurs['email'],
                        $valeurs['telephone'],
                        $cible['id'],
                    ]);
                $_SESSION['flash'] = 'L\'inscription de ' . $valeurs['prenom_enfant']
                    . ($cible['type'] === 'stage' ? ' au stage' : ' à l\'événement')
                    . ' (' . mb_strtolower($cible['periode']) . ') est bien enregistrée.';
                header('Location: ' . ($cible['type'] === 'stage' ? 'stages.php' : '../index.php'));
                exit();
            } catch (PDOException $e) {
                // L'inscription a échoué : on rend la place réservée
                rendre_place($pdo, $cible);
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

          <?php if (!$cible): ?>
          <p class="text-center">
            Choisissez d'abord le stage ou l'événement auquel inscrire votre enfant.
          </p>
          <a href="stages.php" class="btn btn-light border rounded-pill d-block mx-auto mt-3 px-4" style="width: fit-content;">
            Voir les stages
          </a>
          <?php else: ?>
          <div class="bg-white rounded-3 p-3 mb-4 text-center">
            <strong><?= htmlspecialchars($cible['nom']) ?></strong><br>
            <?= htmlspecialchars($cible['periode']) ?>
            <?php if ($cible['places'] !== null): ?> · <?= $cible['places'] ?> places<?php endif; ?><br>
            <a href="<?= htmlspecialchars($cible['retour']) ?>" class="small text-black"><?= htmlspecialchars($cible['changer']) ?></a>
          </div>
          <?php if ($erreurs): ?>
          <div class="alert alert-danger" role="alert">
            <?= implode('<br>', array_map('htmlspecialchars', $erreurs)) ?>
          </div>
          <?php endif; ?>

          <?php if ($cible['fin'] !== null && $cible['fin'] < date('Y-m-d')): ?>
          <p class="text-center"><?= $cible['type'] === 'stage' ? 'Ce stage est terminé' : 'Cet événement est terminé' ?>, les inscriptions sont fermées.</p>
          <?php elseif ($cible['places'] !== null && $cible['places'] <= 0): ?>
          <p class="text-center">Désolé, <?= $cible['type'] === 'stage' ? 'ce stage est complet' : 'cet événement est complet' ?>.</p>
          <?php else: ?>
          <form method="POST" action="inscription.php?<?= $cible['type'] ?>=<?= $cible['id'] ?>">
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

