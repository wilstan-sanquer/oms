<?php
// Connexion PDO partagée.
// Les identifiants MySQL changent selon la machine (XAMPP/WAMP : root sans mot de passe,
// MAMP : root / root). Pour les changer chez soi sans toucher au code, créer
// backend/config.local.php (ignoré par git) qui retourne par exemple :
//   <?php return ['user' => 'root', 'password' => 'root'];
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $config = [
        'host' => 'localhost',
        'dbname' => 'oms_2026',
        'user' => 'root',
        'password' => '',
    ];
    $localConfig = __DIR__ . '/config.local.php';
    if (is_file($localConfig)) {
        $config = array_merge($config, require $localConfig);
    }

    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config['user'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    return $pdo;
}
