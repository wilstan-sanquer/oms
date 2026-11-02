<?php
$sport = [
  'nom' => 'Basketball',
  'titre' => 'Nos activités avec le basketball',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants travaillent le dribble, le tir et la lecture du jeu à travers des ateliers ludiques, des exercices d\'adresse et des tournois en trois contre trois.',
  'photo' => ['fichier' => 'basketball.jpg', 'alt' => 'Match de basketball entre jeunes joueurs'],
  'vignettes' => [
    ['fichier' => 'Basketball1.jpg', 'alt' => 'Jeune joueuse au tir en suspension'],
    ['fichier' => 'Basketball2.jpg', 'alt' => 'Groupe d\'enfants à l\'entraînement de dribble'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';