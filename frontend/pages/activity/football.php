<?php
$sport = [
  'nom' => 'Football',
  'titre' => 'Nos activités avec le football',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants développent leurs compétences techniques et tactiques à travers des ateliers ludiques, des exercices d\'agilité et des tournois.',
  'photo' => ['fichier' => 'football.jpg', 'alt' => 'Jeunes joueurs en plein match de football'],
  'vignettes' => [
    ['fichier' => 'Football1.jpg', 'alt' => 'Trois enfants se disputant le ballon devant le but'],
    ['fichier' => 'Football2.jpg', 'alt' => 'Éducateur donnant des consignes à de jeunes footballeurs'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
