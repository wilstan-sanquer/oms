<?php
// Liste des activités affichées sur le site : les 8 sports historiques (pages dans pages/activity/)
// suivies de celles créées depuis l'administration (table activite).
// Chemins relatifs au dossier frontend.
require_once __DIR__ . '/../../backend/db.php';

function liste_activites(): array
{
    $activites = [
        'Football' => ['page' => 'pages/activity/football.php', 'image' => 'assets/images/sports/football.jpg'],
        'Basketball' => ['page' => 'pages/activity/basketball.php', 'image' => 'assets/images/sports/basketball.jpg'],
        'Tennis' => ['page' => 'pages/activity/tennis.php', 'image' => 'assets/images/sports/tennis.jpg'],
        'Handball' => ['page' => 'pages/activity/handball.php', 'image' => 'assets/images/sports/handball.jpg'],
        'Disc golf' => ['page' => 'pages/activity/disc-golf.php', 'image' => 'assets/images/sports/Disc golf.jpg'],
        'Hobby horsing' => ['page' => 'pages/activity/hobby-horsing.php', 'image' => 'assets/images/sports/Hobby-Horsing.jpg'],
        'Cycle-ball' => ['page' => 'pages/activity/cycle-ball.php', 'image' => 'assets/images/sports/Cycle-Ball.jpg'],
        'Quidditch' => ['page' => 'pages/activity/quidditch.php', 'image' => 'assets/images/sports/Quidditch.jpg'],
    ];

    try {
        foreach (db()->query('SELECT id, nom, image_path FROM activite ORDER BY date_publication, id') as $a) {
            $activites[$a['nom']] ??= ['page' => 'pages/activity/activite.php?id=' . $a['id'], 'image' => $a['image_path']];
        }
    } catch (PDOException $e) {
        // Table activite absente (script database/ajout_activite_avis.sql pas encore lancé) : sports de base seulement
    }
    return $activites;
}

// Chemin d'image utilisable dans un attribut src (les noms de fichiers peuvent contenir des espaces)
function src_image(string $chemin): string
{
    return htmlspecialchars(implode('/', array_map('rawurlencode', explode('/', $chemin))));
}
