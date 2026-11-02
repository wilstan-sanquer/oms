<?php
$sport = [
  'nom' => 'Quidditch',
  'titre' => 'Nos activités avec le quidditch',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants découvrent les postes de poursuiveur, batteur et gardien à travers des ateliers ludiques, des exercices de passe et des matchs mixtes.',
  'photo' => ['fichier' => 'Quidditch.jpg', 'alt' => 'Équipes de quidditch en plein match'],
  'vignettes' => [
    ['fichier' => 'Quidditch1.jpg', 'alt' => 'Joueurs de quidditch courant avec leur balai'],
    ['fichier' => 'Quidditch2.jpg', 'alt' => 'Match de quidditch en compétition'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
