<?php
// Télécharge les photos sélectionnées d'un album : l'image seule s'il n'y en a qu'une, sinon un ZIP.
require_once __DIR__ . '/../../backend/db.php';

$idAlbum = (int) ($_POST['id_album'] ?? 0);
$ids = array_values(array_unique(array_map('intval', (array) ($_POST['photos'] ?? []))));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idAlbum || !$ids) {
    header('Location: album-detail.php?id=' . $idAlbum);
    exit();
}

$pdo = db();
$stmt = $pdo->prepare('SELECT nom FROM album WHERE id = ?');
$stmt->execute([$idAlbum]);
$nomAlbum = $stmt->fetchColumn();

// On ne garde que les photos qui appartiennent bien à cet album
$marques = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT path FROM photo WHERE id_album = ? AND id IN ($marques) ORDER BY id");
$stmt->execute(array_merge([$idAlbum], $ids));

$frontend = realpath(__DIR__ . '/..');
$fichiers = [];
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $chemin) {
    $absolu = realpath($frontend . '/' . $chemin);
    if ($absolu && str_starts_with($absolu, $frontend . '/assets/images/') && is_file($absolu)) {
        $fichiers[] = $absolu;
    }
}

if ($nomAlbum === false || !$fichiers) {
    http_response_code(404);
    exit('Aucune photo à télécharger.');
}

// Nom de fichier lisible et sans caractères spéciaux : « stage-de-football-ete-2026 »
$ascii = preg_replace('/[\'"`^~¨]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nomAlbum) ?: ''); // é -> 'e -> e selon le système
$base = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $ascii), '-')) ?: 'album';

if (count($fichiers) === 1) {
    $fichier = $fichiers[0];
    header('Content-Type: ' . (mime_content_type($fichier) ?: 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $base . '.' . pathinfo($fichier, PATHINFO_EXTENSION) . '"');
    header('Content-Length: ' . filesize($fichier));
    readfile($fichier);
    exit();
}

$zipChemin = tempnam(sys_get_temp_dir(), 'album');
$zip = new ZipArchive();
$zip->open($zipChemin, ZipArchive::OVERWRITE);
foreach ($fichiers as $i => $fichier) {
    $zip->addFile($fichier, sprintf('%s-%02d.%s', $base, $i + 1, pathinfo($fichier, PATHINFO_EXTENSION)));
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $base . '.zip"');
header('Content-Length: ' . filesize($zipChemin));
readfile($zipChemin);
unlink($zipChemin);
