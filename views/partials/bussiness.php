<?php
require_once __DIR__ . '/../../config/database.php';

// Giriş metinlerini DB'den çek
function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    static $cache = [];
    $cacheKey = "$section|$key";
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cache[$cacheKey] = $row ? (string)$row['field_value'] : $default;
    return $cache[$cacheKey];
}

$introTitle = getContent($pdo, 'partners_intro', 'title', 'Gücümüzü Paylaştığımız İş Ortaklarımız');
$introPara1 = getContent($pdo, 'partners_intro', 'paragraph1', 'Başarımızın temelinde yalnızca kaliteli ürünler değil, güçlü iş birliklerimiz de yer alıyor.');
$introPara2 = getContent($pdo, 'partners_intro', 'paragraph2', 'Yerli ve uluslararası tedarikçilerimizle gerçekleştirdiğimiz stratejik iş birlikleri sayesinde, üreticilerimize her zaman yüksek kaliteli çözümler sunuyoruz.');
$introPara3 = getContent($pdo, 'partners_intro', 'paragraph3', 'Bizi tercih eden iş ortaklarımıza teşekkür eder, birlikte büyümeye devam edeceğimizi taahhüt ederiz.');

// Aktif ortakları al (logo için)
$partners = $pdo->query("SELECT * FROM business_partners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$logoPartners = array_filter($partners, fn($p) => !empty($p['logo_path']));
?>

<div class="container mt-5">
    <h2 class="pb-2 border-bottom">İş Ortaklarımız</h2>
    <hr class="featurette-divider">
    <div class="row featurette">
        <div class="col-md-12 text-justify">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($introTitle) ?></h2>
            <?php if ($introPara1): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($introPara1)) ?></p>
            <?php endif; ?>
            <?php if ($introPara2): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($introPara2)) ?></p>
            <?php endif; ?>
            <?php if ($introPara3): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($introPara3)) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <hr class="featurette-divider">

    <!-- Ortak Logoları Carousel -->
    <?php if (count($logoPartners) > 0): ?>
        <div class="row mt-4">
            <div id="owl-bussiness" class="owl-carousel owl-theme">
                <?php foreach ($logoPartners as $partner): ?>
                    <div class="item" style="width:250px; text-align:center;">
                        <img src="/<?= htmlspecialchars($partner['logo_path']) ?>"
                             alt="<?= htmlspecialchars($partner['name']) ?>"
                             style="max-height:120px; object-fit:contain;">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">İş ortağı logosu henüz eklenmemiş.</div>
    <?php endif; ?>
</div>