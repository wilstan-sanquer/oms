<?php
$sport = [
  'nom' => 'Handball',
  'titre' => 'Nos activités avec le handball',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants travaillent la passe, le tir et le jeu collectif à travers des ateliers ludiques, des exercices de coordination et des tournois.',
  'photo' => ['fichier' => 'handball.jpg', 'alt' => 'Jeunes joueurs de handball en action'],
  'vignettes' => [
    ['fichier' => 'Handball1.jpg', 'alt' => 'Joueur de handball en suspension avant le tir'],
    ['fichier' => 'Handball2.jpg', 'alt' => 'Joueurs de handball au contact pendant un match'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
