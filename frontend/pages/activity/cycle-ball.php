<?php
$sport = [
  'nom' => 'Cycle-ball',
  'titre' => 'Nos activités avec le cycle-ball',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants apprennent l\'équilibre sur le vélo et la conduite du ballon à travers des ateliers ludiques, des exercices de maniabilité et des matchs à deux contre deux.',
  'photo' => ['fichier' => 'Cycle-Ball.jpg', 'alt' => 'Joueurs de cycle-ball se disputant le ballon'],
  'vignettes' => [
    ['fichier' => 'CycleBall1.jpg', 'alt' => 'Joueurs de cycle-ball pendant un corner'],
    ['fichier' => 'CycleBall2.jpg', 'alt' => 'Action de cycle-ball devant le but en gymnase'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
