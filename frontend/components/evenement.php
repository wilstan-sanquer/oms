<?php
// Affichage des événements (table actualite).
// Le titre est enregistré sous la forme "Événement du 04/11/2026 au 07/11/2026" :
// on le réécrit en "Du 4 au 7 novembre 2026", ou "Le 2 novembre 2026" pour un seul jour.
function periode_evenement(string $titre): string
{
    if (!preg_match('#(\d{2})/(\d{2})/(\d{4}) au (\d{2})/(\d{2})/(\d{4})#', $titre, $m)) {
        return $titre;
    }
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    [, $j1, $m1, $a1, $j2, $m2, $a2] = $m;
    $jour = fn(string $j) => $j === '01' ? '1er' : (string) (int) $j;
    $fin = $jour($j2) . ' ' . $mois[(int) $m2 - 1] . ' ' . $a2;

    if ("$j1$m1$a1" === "$j2$m2$a2") {
        return 'Le ' . $fin;
    }
    if ("$m1$a1" === "$m2$a2") {
        return 'Du ' . $jour($j1) . ' au ' . $fin;
    }
    if ($a1 === $a2) {
        return 'Du ' . $jour($j1) . ' ' . $mois[(int) $m1 - 1] . ' au ' . $fin;
    }
    return 'Du ' . $jour($j1) . ' ' . $mois[(int) $m1 - 1] . ' ' . $a1 . ' au ' . $fin;
}

// La description est enregistrée sous la forme "Places : 30"
function places_evenement(string $description): ?int
{
    return preg_match('/Places\s*:\s*(\d+)/', $description, $m) ? (int) $m[1] : null;
}
