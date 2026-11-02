<?php
$sport = [
  'nom' => 'Tennis',
  'titre' => 'Nos activités avec le tennis',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants perfectionnent leur coup droit, leur revers et leur service à travers des ateliers ludiques, des exercices de déplacement et des matchs amicaux.',
  'photo' => ['fichier' => 'tennis.jpg', 'alt' => 'Jeune joueur de tennis frappant la balle'],
  'vignettes' => [
    ['fichier' => 'Tennis1.jpg', 'alt' => 'Jeune joueuse de tennis avec sa raquette sur le court'],
    ['fichier' => 'Tennis2.jpg', 'alt' => 'Raquette et balles de tennis posées sur le court'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
