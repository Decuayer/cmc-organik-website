<?php
require_once __DIR__ . '/../../config/database.php';

// İş ortaklarını DB'den çek (aktif, sıralı)
$partners = $pdo->query("SELECT * FROM business_partners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Giriş metinlerini DB'den çek
function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

$introTitle = getContent($pdo, 'partners_intro', 'title', 'Gücümüzü Paylaştığımız İş Ortaklarımız');
$introPara1 = getContent($pdo, 'partners_intro', 'paragraph1', 'Başarımızın temelinde yalnızca kaliteli ürünler değil, güçlü iş birliklerimiz de yer alıyor.');
$introPara2 = getContent($pdo, 'partners_intro', 'paragraph2', 'Yerli ve uluslararası tedarikçilerimizle gerçekleştirdiğimiz stratejik iş birlikleri sayesinde, üreticilerimize her zaman yüksek kaliteli çözümler sunuyoruz.');
$introPara3 = getContent($pdo, 'partners_intro', 'paragraph3', 'Bizi tercih eden iş ortaklarımıza teşekkür eder, birlikte büyümeye devam edeceğimizi taahhüt ederiz.');
?>

<div class="container mt-5">
    <h2 class="pb-2 border-bottom">İş Ortaklarımız</h2>
    <hr class="featurette-divider">

    <!-- Giriş Yazısı -->
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

    <?php if (count($partners) > 0): ?>
        <!-- Her ortak için detay bölümü (alternating layout) -->
        <?php foreach ($partners as $index => $partner): ?>
            <hr class="featurette-divider">
            <?php
            $isEven = $index % 2 === 0;
            $textClass = $isEven ? 'col-md-7' : 'col-md-7 order-md-2';
            $imgClass  = $isEven ? 'col-md-5' : 'col-md-5 order-md-1';
            ?>
            <div class="row featurette">
                <div class="<?= $textClass ?>">
                    <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($partner['name']) ?></h2>
                    <?php if ($partner['description']): ?>
                        <?php foreach (explode("\n", $partner['description']) as $para): ?>
                            <?php if (trim($para) !== ''): ?>
                                <p class="lead"><?= htmlspecialchars(trim($para)) ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="<?= $imgClass ?>">
                    <?php if ($partner['image_path']): ?>
                        <img src="/<?= htmlspecialchars($partner['image_path']) ?>"
                             alt="<?= htmlspecialchars($partner['name']) ?>"
                             class="bd-placeholder-img rounded img-fluid"
                             width="500" height="500">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:200px;">
                            <span style="font-size:4rem;">🤝</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Logo Carousel -->
        <?php
        $logos = array_filter($partners, fn($p) => !empty($p['logo_path']));
        if (count($logos) > 0):
        ?>
        <hr class="featurette-divider">
        <div class="row mt-4">
            <div id="owl-partners" class="owl-carousel owl-theme">
                <?php foreach ($logos as $partner): ?>
                    <div class="item" style="width:250px; text-align:center;">
                        <img src="/<?= htmlspecialchars($partner['logo_path']) ?>"
                             alt="<?= htmlspecialchars($partner['name']) ?>"
                             style="max-height:120px; object-fit:contain;">
                        <p class="mt-1" style="font-size:0.8rem; color:#666;"><?= htmlspecialchars($partner['name']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <hr class="featurette-divider">
        <div class="alert alert-info">Henüz iş ortağı eklenmemiş.</div>
    <?php endif; ?>
</div>