<?php
/**
 * Carousel partial — veritabanından dinamik olarak yüklenir.
 * Tablo: carousel_slides (id, title, description, button_text, button_link, image_path, text_align, sort_order, is_active)
 */
require_once __DIR__ . '/../../config/database.php';

$slides = [];
try {
    $slides = $pdo->query(
        "SELECT * FROM carousel_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tablo yoksa statik fallback
    $slides = [];
}
?>

<div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">

    <?php if (!empty($slides)): ?>

    <div class="carousel-indicators">
        <?php foreach ($slides as $i => $slide): ?>
            <button type="button"
                    data-bs-target="#myCarousel"
                    data-bs-slide-to="<?= $i ?>"
                    <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>
                    aria-label="Slide <?= $i + 1 ?>">
            </button>
        <?php endforeach; ?>
    </div>

    <div class="carousel-inner">
        <?php foreach ($slides as $i => $slide):
            $imgSrc     = htmlspecialchars($slide['image_path'] ?? '');
            $title      = htmlspecialchars($slide['title'] ?? '');
            $desc       = htmlspecialchars($slide['description'] ?? '');
            $btnText    = htmlspecialchars($slide['button_text'] ?? '');
            $btnLink    = htmlspecialchars($slide['button_link'] ?? '#');
            $textAlign  = in_array($slide['text_align'], ['start','center','end']) ? $slide['text_align'] : 'start';
            $marginTop  = ($i === 3) ? '0px' : '-450px'; // Slayt 4 farklı konumda (orijinal davranış)
        ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
            <?php if ($imgSrc): ?>
                <img class="first-slide img-responsive"
                     style="margin-top: <?= $marginTop ?>; text-shadow: 2px 2px #000000;"
                     src="<?= $imgSrc ?>"
                     alt="<?= $title ?>">
            <?php endif; ?>
            <div class="container">
                <div class="carousel-caption text-<?= $textAlign ?>">
                    <?php if ($title): ?>
                        <h1 style="text-shadow: 2px 2px #000000; color: #fff"><?= $title ?></h1>
                    <?php endif; ?>
                    <?php if ($desc): ?>
                        <p style="text-shadow: 2px 2px #000000; color: #fff"><?= $desc ?></p>
                    <?php endif; ?>
                    <?php if ($btnText && $btnLink): ?>
                        <p><a class="btn btn-lg btn-success" href="<?= $btnLink ?>"><?= $btnText ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- Fallback: Tablo boşsa veya hata varsa statik slayt göster -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="first-slide img-responsive" style="margin-top: -450px;" src="public/img/carousel-1.jpg" alt="CMC Organik">
            <div class="container">
                <div class="carousel-caption text-start">
                    <h1 style="text-shadow: 2px 2px #000000; color: #fff">İzmir Merkezli Köklü Firma</h1>
                    <p style="text-shadow: 2px 2px #000000; color: #fff">CMC Organik Tarım merkezi İzmir'de olup tarım sektöründe 2010 yılından bu yana faaliyet göstermektedir.</p>
                    <p><a class="btn btn-lg btn-success" href="about.php">Hakkımızda</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Önceki</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sonraki</span>
    </button>
</div>