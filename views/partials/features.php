<?php
require_once __DIR__ . '/../../config/database.php';

function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

// Misyon
$missionTitle = getContent($pdo, 'homepage_mission', 'title', 'Misyonumuz');
$missionText  = getContent($pdo, 'homepage_mission', 'text', 'Firmamız tarımda daha kârlı mahsul üretmek için bitki besleme alanlarındaki teknolojik yenilikleri takip ederek, doğaya saygılı yenilikçi, büyüme odaklı olmayı kendine görev edinmiştir.');
$missionImg   = getContent($pdo, 'homepage_mission', 'image_path', 'public/img/aboutus.jpg');

// Vizyon
$visionTitle = getContent($pdo, 'homepage_vision', 'title', 'Vizyonumuz');
$visionText  = getContent($pdo, 'homepage_vision', 'text', 'Bitki besleme alanında çözüm odaklı çalışan, ülkemizde lider ve aranılır firmalar arasında yer almaktır.');
$visionImg   = getContent($pdo, 'homepage_vision', 'image_path', 'public/img/aboutus2.JPG');

// Yol Haritası
$roadmapSlogan = getContent($pdo, 'homepage_roadmap', 'slogan', '"Bilimden Doğaya"');
$roadmapDesc   = getContent($pdo, 'homepage_roadmap', 'description', '"Bilimden Doğaya" sloganı, CMC Organik Tarım\'ın bilimsel yenilikleri doğayla uyum içinde tarımsal üretime entegre etme vizyonunu yansıtmaktadır.');

// Roadmap Maddeleri
$roadmapItems = $pdo->query("SELECT * FROM roadmap_items WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container marketing mt-5">    
    <h2 class="pb-2">Hakkımızda</h2>
    <hr class="featurette-divider">

    <!-- Misyon -->
    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($missionTitle) ?></h2>
            <p class="lead"><?= nl2br(htmlspecialchars($missionText)) ?></p>
        </div>
        <div class="col-md-5">
            <img src="/<?= htmlspecialchars($missionImg) ?>"
                 alt="<?= htmlspecialchars($missionTitle) ?>"
                 class="bd-placeholder-img rounded img-fluid" width="500" height="500">
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- Vizyon -->
    <div class="row featurette">
        <div class="col-md-7 order-md-2">
            <h2 class="featurette-heading fw-normal lh-1"><?= htmlspecialchars($visionTitle) ?></h2>
            <p class="lead"><?= nl2br(htmlspecialchars($visionText)) ?></p>
        </div>
        <div class="col-md-5 order-md-1">
            <img src="/<?= htmlspecialchars($visionImg) ?>"
                 alt="<?= htmlspecialchars($visionTitle) ?>"
                 class="bd-placeholder-img rounded img-fluid" width="500" height="500">
        </div>
    </div>

    <!-- Yol Haritası -->
    <h2 class="mt-5" style="margin-top: 100px !important;">Yol Haritamız</h2>
    <hr class="featurette-divider">
    <div class="row row-cols-1 row-cols-md-2 align-items-md-center g-4 py-5">
        <div class="col d-flex flex-column align-items-start gap-2">
            <h2 class="fw-bold text-body-emphasis"><?= htmlspecialchars($roadmapSlogan) ?></h2>
            <p class="text-body-secondary"><?= nl2br(htmlspecialchars($roadmapDesc)) ?></p>
            <a href="editing.php" class="btn btn-success btn-lg">Hakkımızda</a>
        </div>
        <div class="col">
            <div class="row row-cols-1 row-cols-sm-2 g-4">
                <?php foreach ($roadmapItems as $item): ?>
                    <div class="col d-flex flex-column gap-2">
                        <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-success bg-gradient fs-4 rounded-3">
                            <svg class="bi" width="1em" height="1em">
                                <use xlink:href="#<?= htmlspecialchars($item['icon_id'] ?? 'gear-fill') ?>" />
                            </svg>
                        </div>
                        <h4 class="fw-semibold mb-0 text-body-emphasis"><?= htmlspecialchars($item['title']) ?></h4>
                        <p class="text-body-secondary"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (count($roadmapItems) === 0): ?>
                    <div class="col">
                        <p class="text-muted">Henüz yol haritası maddesi eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>