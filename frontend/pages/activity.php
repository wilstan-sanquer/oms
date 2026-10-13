<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OMS - Activités</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/styles/activity.css">
</head>

<body>
  <?php require __DIR__ . '/../components/navbar.php'; ?>
  <main>
    <div class="activity container my-4">
      <h3 class="fw-normal mb-3">Nos activités</h3>
      <div class="row row-cols-4 g-4">

        <a href="activity/football.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/football.jpg" alt="Football" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Football</p>
          </div>
        </a>

        <a href="activity/basketball.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/basketball.jpg" alt="Basketball" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Basketball</p>
          </div>
        </a>

        <a href="activity/tennis.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/tennis.jpg" alt="Tennis" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Tennis</p>
          </div>
        </a>

        <a href="activity/handball.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/handball.jpg" alt="Handball" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Handball</p>
          </div>
        </a>

        <a href="activity/disc-golf.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/Disc golf.jpg" alt="Disc golf" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Disc-golf</p>
          </div>
        </a>

        <a href="activity/hobby-horsing.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/Hobby-Horsing.jpg" alt="Disc golf" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Hobby-Horsing</p>
          </div>
        </a>

        <a href="activity/cycle-ball.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/Cycle-Ball.jpg" alt="Disc golf" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Cycle-Ball</p>
          </div>
        </a>

        <a href="activity/quidditch.php">
          <div class="col">
            <div class="ratio ratio-4x3">
              <img src="../assets/images/sports/Quidditch.jpg" alt="Disc golf" class="rounded-3 object-fit-cover">
            </div>
            <p class="mt-2 mb-0">Quidditch</p>
          </div>
        </a>
        
      </div>
  </main>
  <?php require __DIR__ . '/../components/footer.php'; ?>
</body>

</html>