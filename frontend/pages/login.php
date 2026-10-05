<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Connexion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../app/assets/styles/style.css">
  <link rel="stylesheet" href="../app/assets/styles/login.css">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main>
<div class="container mt-5 d-flex flex-column">
    <div class="d-flex justify-content-center">
      <div class="w-100" style="max-width: 600px;">

      </div>
    </div>
    <h1 class="text-left mb-5" >
      Connexion Menbres OMS
    </h1>
    <p>Identifiant ou email</p>
    <input type="text" class="rounded-3 mb-5" style="width: 400px;" placeholder="Email">
    <p>Mot De Passe</p>
    <input type="text" class="rounded-3 mb-5" style="width: 400px;" placeholder="Mot De Passe">
    <button class="rounded-3 w-25">Connexion</button>
  </div>




  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

