<?php
/**
 * Ortam ayarları. Prod'da APP_DEBUG false olmalı (hata detaylarını gizler).
 * Local geliştirme için config/env.local.php dosyası oluşturup APP_DEBUG'ı true yapabilirsiniz
 * (bu dosya .gitignore'a eklenmiştir, repoya işlenmez).
 */

$envLocal = __DIR__ . '/env.local.php';
if (file_exists($envLocal)) {
    require_once $envLocal;
}

if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
}
