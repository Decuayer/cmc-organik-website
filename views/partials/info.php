<?php
require_once __DIR__ . '/../../config/database.php';

function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

// Hoşgeldiniz
$welcomeTitle = getContent($pdo, 'about_welcome', 'title', 'CMC Organik\'e Hoş Geldiniz');
$welcomeText  = getContent($pdo, 'about_welcome', 'text', 'Doğadan gelen gücü toprakla buluşturarak sürdürülebilir tarımı destekleyen CMC Organik, 2010 yılından bu yana üreticilerin güvenilir çözüm ortağı olmuştur.');
$welcomeImg   = getContent($pdo, 'about_welcome', 'image_path', 'public/img/aboutus.jpg');

// Misyon
$missionTitle = getContent($pdo, 'about_mission', 'title', 'Misyonumuz');
$missionText  = getContent($pdo, 'about_mission', 'text', 'Tarımda daha kârlı ve sürdürülebilir mahsul üretimi için bitki besleme alanındaki teknolojik yenilikleri takip eden, doğaya saygılı bir yaklaşımı benimsemek.');
$missionImg   = getContent($pdo, 'about_mission', 'image_path', 'public/img/aboutus2.JPG');

// Vizyon
$visionTitle = getContent($pdo, 'about_vision', 'title', 'Vizyonumuz');
$visionText  = getContent($pdo, 'about_vision', 'text', 'Bitki besleme çözümlerinde güvenilir, yenilikçi ve çözüm odaklı hizmet anlayışıyla, Türkiye\'nin lider tarım markaları arasında yer almak.');
$visionImg   = getContent($pdo, 'about_vision', 'image_path', 'public/img/products.JPG');

// Alt Bölüm
$bottomTitle = getContent($pdo, 'about_bottom', 'title', 'CMC Organik ile Tarımda Güvenin ve Kalitenin Adresi');
$bottomP1    = getContent($pdo, 'about_bottom', 'paragraph1', '');
$bottomP2    = getContent($pdo, 'about_bottom', 'paragraph2', '');
$bottomP3    = getContent($pdo, 'about_bottom', 'paragraph3', '');
$bottomP4    = getContent($pdo, 'about_bottom', 'paragraph4', '');
?>

<div class="container mt-5">
    <h2 class="pb-2 border-bottom">Hakkımızda</h2>
    <hr class="featurette-divider">

    <!-- Hoşgeldiniz -->
    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($welcomeTitle) ?></h2>
            <p class="lead"><?= nl2br(htmlspecialchars($welcomeText)) ?></p>
        </div>
        <div class="col-md-5">
            <img src="/<?= htmlspecialchars($welcomeImg) ?>"
                 alt="<?= htmlspecialchars($welcomeTitle) ?>"
                 class="bd-placeholder-img rounded img-fluid" width="500" height="500">
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Misyon -->
    <div class="row featurette">
        <div class="col-md-7 order-md-2">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($missionTitle) ?></h2>
            <p class="lead"><?= nl2br(htmlspecialchars($missionText)) ?></p>
        </div>
        <div class="col-md-5 order-md-1">
            <img src="/<?= htmlspecialchars($missionImg) ?>"
                 alt="<?= htmlspecialchars($missionTitle) ?>"
                 class="bd-placeholder-img rounded img-fluid" width="500" height="500">
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Vizyon -->
    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($visionTitle) ?></h2>
            <p class="lead"><?= nl2br(htmlspecialchars($visionText)) ?></p>
        </div>
        <div class="col-md-5">
            <img src="/<?= htmlspecialchars($visionImg) ?>"
                 alt="<?= htmlspecialchars($visionTitle) ?>"
                 class="bd-placeholder-img rounded img-fluid" width="500" height="500">
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Alt Bölüm -->
    <div class="row featurette">
        <div class="col-md-12">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($bottomTitle) ?></h2>
            <?php if ($bottomP1): ?><p class="lead text-justify"><?= nl2br(htmlspecialchars($bottomP1)) ?></p><?php endif; ?>
            <?php if ($bottomP2): ?><p class="lead text-justify"><?= nl2br(htmlspecialchars($bottomP2)) ?></p><?php endif; ?>
            <?php if ($bottomP3): ?><p class="lead text-justify"><?= nl2br(htmlspecialchars($bottomP3)) ?></p><?php endif; ?>
            <?php if ($bottomP4): ?><p class="lead text-justify"><?= nl2br(htmlspecialchars($bottomP4)) ?></p><?php endif; ?>
        </div>
    </div>
</div>