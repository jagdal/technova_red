<?php

/**
 * Routeur pour le serveur PHP intégré (php -S).
 * Redirige toutes les requêtes vers index.php sauf les fichiers statiques.
 */
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $path;

    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

require __DIR__ . '/index.php';
