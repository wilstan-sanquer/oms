<?php
// Page d'une activité créée depuis l'administration : même gabarit que les pages sport.
require_once __DIR__ . '/../../../backend/db.php';

try {
    $stmt = db()->prepare('SELECT nom, description, image_path FROM activite WHERE id = ?');
    $stmt->execute([(int) ($_GET['id'] ?? 0)]);
    $activite = $stmt->fetch();
} catch (PDOException $e) {
    $activite = false;
}

if (!$activite) {
    session_start();
    $_SESSION['flash'] = 'Cette activité n\'existe pas ou a été supprimée.';
    header('Location: ../activity.php');
    exit();
}

$sport = [
    'nom' => $activite['nom'],
    'titre' => $activite['nom'],
    'description' => $activite['description'],
    'photo' => ['src' => '../../' . $activite['image_path'], 'alt' => $activite['nom']],
];

require __DIR__ . '/../../components/sport-page.php';
