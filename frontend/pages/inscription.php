<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Inscription</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css">
  <link rel="stylesheet" href="../assets/styles/inscription.css">
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
          <form>
            <div class="row g-3">

              <div class="col-6">
                <label class="form-label small">Nom de l'enfant</label>
                <input type="text" class="form-control form-control-sm" placeholder="Entrez le nom">
              </div>

              <div class="col-6">
                <label class="form-label small">Prénom de l'enfant</label>
                <input type="text" class="form-control form-control-sm" placeholder="Entrez le prénom">
              </div>

              <div class="col-6">
                <label class="form-label small">Âge de l'enfant</label>
                <input type="number" class="form-control form-control-sm" placeholder="Entrez l'âge">
              </div>

              <div class="col-6">
                <label class="form-label small">Numéro téléphone</label>
                <input type="tel" class="form-control form-control-sm" placeholder="Votre numéro">
              </div>

              <div class="col-6">
                <label class="form-label small">Nom du tuteur légal</label>
                <input type="text" class="form-control form-control-sm" placeholder="Votre nom">
              </div>

              <div class="col-6">
                <label class="form-label small">Prénom du tuteur légal</label>
                <input type="text" class="form-control form-control-sm" placeholder="Votre prénom">
              </div>

            </div>

            <button type="submit" class="btn btn-light border rounded-pill
                                               d-block mx-auto mt-4 px-4">
              Inscription
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>




  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

