<?php
/**
 * Ortak, güvenli dosya yükleme yardımcı fonksiyonu.
 * Uzantı whitelist + MIME sniff (finfo) + boyut limiti + tahmin edilemez dosya adı.
 */

function handleImageUpload(
    array $file,
    string $destDir,
    string $publicPrefix,
    array $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    int $maxBytes = 5 * 1024 * 1024,
    string $filenamePrefix = ''
): array {
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Dosya yüklenemedi.'];
    }

    if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxBytes) {
        return ['ok' => false, 'path' => null, 'error' => 'Dosya boyutu izin verilen limiti aşıyor (maks. ' . round($maxBytes / 1024 / 1024, 1) . ' MB).'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtsLower = array_map('strtolower', $allowedExts);
    if (!in_array($ext, $allowedExtsLower, true)) {
        return ['ok' => false, 'path' => null, 'error' => 'İzin verilmeyen dosya uzantısı.'];
    }

    $extToMime = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!$realMime || empty($extToMime[$ext]) || !in_array($realMime, $extToMime[$ext], true)) {
        return ['ok' => false, 'path' => null, 'error' => 'Dosya içeriği beklenen görsel formatıyla eşleşmiyor.'];
    }

    if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
        return ['ok' => false, 'path' => null, 'error' => 'Hedef klasör oluşturulamadı.'];
    }

    $filename = $filenamePrefix . bin2hex(random_bytes(8)) . '.' . $ext;
    $destPath = rtrim($destDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok' => false, 'path' => null, 'error' => 'Dosya taşınamadı.'];
    }

    return ['ok' => true, 'path' => rtrim($publicPrefix, '/') . '/' . $filename, 'error' => null];
}
