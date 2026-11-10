<?php
// Retire d'un album les photos sélectionnées depuis la page de l'album (admins uniquement).
require_once __DIR__ . '/../../components/admin.php';

$idAlbum = (int) ($_POST['id_album'] ?? 0);
$ids = array_values(array_unique(array_map('intval', (array) ($_POST['photos'] ?? []))));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idAlbum && $ids) {
    verifier_csrf();
    $pdo = db();
    $marques = implode(',', array_fill(0, count($ids), '?'));
    $params = array_merge([$idAlbum], $ids);

    $stmt = $pdo->prepare("SELECT path FROM photo WHERE id_album = ? AND id IN ($marques)");
    $stmt->execute($params);
    $chemins = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $pdo->prepare("DELETE FROM photo WHERE id_album = ? AND id IN ($marques)")->execute($params);
    array_map('supprimer_image', $chemins);

    $n = count($chemins);
    $_SESSION['flash'] = $n . ($n > 1 ? ' photos ont été retirées' : ' photo a été retirée') . ' de l\'album.';
}

header('Location: ../album-detail.php?id=' . $idAlbum);
exit();
