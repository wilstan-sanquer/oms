<?php
$sport = [
  'nom' => 'Hobby horsing',
  'titre' => 'Nos activités avec le hobby horsing',
  'description' => 'Ce stage accueille les jeunes passionnés, débutants comme pratiquants réguliers, pour une semaine d\'immersion sportive. Encadrés par des éducateurs diplômés d\'État, les participants découvrent le dressage et le saut d\'obstacles avec leur cheval bâton à travers des ateliers ludiques, des parcours chronométrés et des démonstrations.',
  'photo' => ['fichier' => 'Hobby-Horsing.jpg', 'alt' => 'Jeune pratiquante de hobby horsing sautant un obstacle'],
  'vignettes' => [
    ['fichier' => 'HobbyHorsing1.jpg', 'alt' => 'Jeune fille sautant un obstacle avec son cheval bâton'],
    ['fichier' => 'HobbyHorsing2.jpg', 'alt' => 'Tête de cheval bâton tachetée'],
  ],
];

require __DIR__ . '/../../components/sport-page.php';
