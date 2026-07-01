<?php
/**
 * site_content tablosu için ortak anahtar-değer okuma/yazma yardımcıları.
 * Hem admin panel hem anasayfa partialleri tarafından kullanılır.
 */

function getSiteContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

function setSiteContent(PDO $pdo, string $section, string $key, string $value): void {
    $stmt = $pdo->prepare(
        "INSERT INTO site_content (section, field_key, field_value) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)"
    );
    $stmt->execute([$section, $key, $value]);
}
