<?php
// 1. Démarrer la session et gérer la connexion
session_start();
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=oms_2026;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: http://localhost/oms/frontend/index.php');
                exit();
            } else {
                $erreur = "Identifiant ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $erreur = "Erreur de connexion à la base de données.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Connexion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/style.css">
  <link rel="stylesheet" href="../assets/styles/login.css">
</head>
<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  
  <main>
    <!-- Le formulaire conserve exactement ta structure d'origine alignée à gauche -->
    <form action="" method="POST" class="container mt-5 d-flex flex-column">
        
        <!-- Message d'erreur discret si les identifiants sont mauvais -->
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger mb-4" style="max-width: 400px;" role="alert">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-center">
          <div class="w-100" style="max-width: 600px;">
          </div>
        </div>
        
        <h1 class="text-left mb-5">
          Connexion Membres OMS
        </h1>
        
        <p>Identifiant ou email</p>
        <input type="text" name="username" class="rounded-3 mb-5 px-3 py-2 border" style="width: 400px;" placeholder="Email ou identifiant" required>
        
        <p>Mot De Passe</p>
        <input type="password" name="password" class="rounded-3 mb-5 px-3 py-2 border" style="width: 400px;" placeholder="Mot De Passe" required>
        
        <button type="submit" class="rounded-3 w-25 btn btn-primary py-2">Connexion</button>
    </form>
  </main>
</body>
</html>