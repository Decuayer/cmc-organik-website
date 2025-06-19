<?php
$dir = __DIR__ . '/../../data/registration';
$baseUrl = '/data/registration/';
$pdfs = glob($dir . '/*.pdf');
?>

<section class="container py-5">
    <h2 class="mb-4">Ürün Tescil Dosyalarımız</h2>
    <hr class="featurette-divider">
    <div class="row">
        <?php if (count($pdfs) === 0): ?>
            <div class="col-12">
                <p>Henüz tescil dosyası eklenmemiş.</p>
            </div>
        <?php else: ?>
        <?php foreach ($pdfs as $pdf): 
            $filename = basename($pdf);
            $displayName = preg_replace('/\.pdf$/i', '', $filename);
            $fileUrl = $baseUrl . rawurlencode($filename);        ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title mb-3"><?= htmlspecialchars($displayName) ?></h5>
                        <embed src="<?= htmlspecialchars($fileUrl) ?>" type="application/pdf" width="100%" height="250px" style="border:1px solid #eee; border-radius:4px;" />
                        <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" class="btn btn-success mt-3">PDF'yi Aç</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>