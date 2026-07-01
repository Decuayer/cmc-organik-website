<?php
require_once __DIR__ . '/../../config/database.php';

// DB'den yazıları çek
function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

$title = getContent($pdo, 'company', 'title', 'CMC Organik Hakkında');
$para1 = getContent($pdo, 'company', 'paragraph1', '2010 yılında İzmir\'de kurulan CMC Organik Tarım Sanayi ve Ticaret Ltd. Şti., organik ve konvansiyonel tarım ürünleri ile çiftçilere sürdürülebilir tarımın kapılarını açmayı hedefleyen köklü bir kuruluştur.');
$para2 = getContent($pdo, 'company', 'paragraph2', 'Firmamız; güçlü satış ağı, deneyimli teknik kadrosu ve sürekli gelişen ürün yelpazesiyle Türkiye\'nin dört bir yanındaki üreticilere ulaşmaktadır.');
$para3 = getContent($pdo, 'company', 'paragraph3', 'Tarım sektörünün zorlu koşullarına çözüm üretebilen profesyonel bir yaklaşımla, ürünlerimizin etkinliği konusunda teknik departmanımızla sahada aktif olarak yer alıyoruz.');

// Fotoğrafları klasörden tara
$cinfoDir = __DIR__ . '/../../public/img/cinfo/';
$photos = [];
if (is_dir($cinfoDir)) {
    $allFiles = scandir($cinfoDir);
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    foreach ($allFiles as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts) && $f !== '.' && $f !== '..') {
            $photos[] = $f;
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="pb-2 border-bottom">Şirketimiz</h2>
    <hr class="featurette-divider">
    <div class="row featurette">
        <div class="col-md-12 text-justify">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($title) ?></h2>
            <?php if ($para1): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($para1)) ?></p>
            <?php endif; ?>
            <?php if ($para2): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($para2)) ?></p>
            <?php endif; ?>
            <?php if ($para3): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($para3)) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($photos) > 0): ?>
        <hr class="featurette-divider">
        <div class="row mt-4">
            <div id="owl-cinfo" class="owl-carousel owl-theme">
                <?php foreach ($photos as $photo): ?>
                    <div class="item" style="width:250px;">
                        <img src="/public/img/cinfo/<?= htmlspecialchars($photo) ?>"
                             alt="<?= htmlspecialchars($photo) ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>