<?php
require_once __DIR__ . '/../../components/admin.php';

$pdo = db();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();

    $nom = trim($_POST['nom'] ?? '');
    $photos = $_FILES['photos'] ?? null;
    $nbPhotos = $photos ? count(array_filter($photos['name'])) : 0;

    if ($nom === '') {
        $erreurs[] = 'Veuillez donner un nom à l\'album.';
    }
    if ($nbPhotos === 0) {
        $erreurs[] = 'Veuillez importer au moins une photo.';
    }

    if (!$erreurs) {
        $pdo->beginTransaction();
        $idAlbum = prochain_id($pdo, 'album');
        $pdo->prepare('INSERT INTO album (id, nom, date_publication, id_user) VALUES (?, ?, NOW(), ?)')
            ->execute([$idAlbum, $nom, $_SESSION['user_id']]);

        $idPhoto = prochain_id($pdo, 'photo');
        $ajoutPhoto = $pdo->prepare('INSERT INTO photo (id, path, id_album) VALUES (?, ?, ?)');
        $enregistrees = [];
        $refusees = 0;
        foreach (array_keys($photos['name']) as $i) {
            $fichier = [
                'name' => $photos['name'][$i],
                'tmp_name' => $photos['tmp_name'][$i],
                'error' => $photos['error'][$i],
                'size' => $photos['size'][$i],
            ];
            $chemin = enregistrer_image($fichier, 'albums/' . $idAlbum);
            if ($chemin === null) {
                $refusees++;
                continue;
            }
            $ajoutPhoto->execute([$idPhoto++, $chemin, $idAlbum]);
            $enregistrees[] = $chemin;
        }

        if (!$enregistrees) {
            $pdo->rollBack();
            $erreurs[] = 'Aucune photo valide : utilisez des images JPG, PNG, WebP ou GIF de moins de 8 Mo.';
        } else {
            $pdo->commit();
            $message = 'L\'album « ' . $nom . ' » a été publié avec ' . count($enregistrees) . ' photo(s).';
            if ($refusees) {
                $message .= " $refusees fichier(s) ignoré(s) car ce n'étaient pas des images valides.";
            }
            $_SESSION['flash'] = $message;
            header('Location: ../album-detail.php?id=' . $idAlbum);
            exit();
        }
    }
}

admin_entete('Album photo');
?>
<main class="admin-album">
  <?php admin_erreurs($erreurs); ?>
  <form method="POST" enctype="multipart/form-data" class="d-flex flex-column">
    <?= champ_csrf() ?>

    <label class="admin-depot">
      <input type="file" name="photos[]" accept="image/*" multiple required id="photos">
      <span>Importer des photos dans l’album</span>
    </label>

    <label class="admin-nom-album">Nom de l’album
      <input type="text" name="nom" maxlength="200" placeholder="Ex. : Stage de football – été 2026" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
    </label>

    <p class="mb-2">Photos importées :</p>
    <div class="admin-apercus" id="apercus">
      <span class="admin-vide">Aucune photo pour le moment.</span>
    </div>

    <button type="submit" class="admin-bouton">Publier</button>
  </form>
</main>
<script>
  // Aperçu des photos choisies avant l'envoi
  const champ = document.getElementById('photos');
  const apercus = document.getElementById('apercus');
  const depot = champ.parentElement;

  function afficherApercus() {
    apercus.innerHTML = '';
    if (!champ.files.length) {
      apercus.innerHTML = '<span class="admin-vide">Aucune photo pour le moment.</span>';
      return;
    }
    [...champ.files].forEach((fichier) => {
      const img = document.createElement('img');
      img.src = URL.createObjectURL(fichier);
      img.alt = fichier.name;
      apercus.appendChild(img);
    });
  }

  champ.addEventListener('change', afficherApercus);
  ['dragenter', 'dragover'].forEach((t) => depot.addEventListener(t, () => depot.classList.add('survol')));
  ['dragleave', 'drop'].forEach((t) => depot.addEventListener(t, () => depot.classList.remove('survol')));
</script>
<?php admin_pied(); ?>
