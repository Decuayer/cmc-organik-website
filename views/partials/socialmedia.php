<?php
/**
 * Sosyal medya bölümü — admin panelinden (social_media.php) yönetilen
 * site_content ('social_media' section) ayarlarına göre dinamik render edilir.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/site_content.php';

$sectionVisible  = '1';
$facebookVisible = '1';
$instagramVisible = '1';
$fbUrl   = 'https://www.facebook.com/cmcorganikizmir';
$igUrl   = 'https://www.instagram.com/cmcorganik';
$fbDesc  = 'Facebook sayfamızı takip ederek güncel haberler ve kampanyalarımızdan haberdar olun.';
$igDesc  = 'Instagram hesabımızı takip ederek tarla fotoğraflarımızı ve güncel içeriklerimizi görün.';
$igEmbed = '';

try {
    $sectionVisible    = getSiteContent($pdo, 'social_media', 'section_visible',   $sectionVisible);
    $facebookVisible   = getSiteContent($pdo, 'social_media', 'facebook_visible',  $facebookVisible);
    $instagramVisible  = getSiteContent($pdo, 'social_media', 'instagram_visible', $instagramVisible);
    $fbUrl             = getSiteContent($pdo, 'social_media', 'facebook_url',      $fbUrl);
    $igUrl             = getSiteContent($pdo, 'social_media', 'instagram_url',     $igUrl);
    $fbDesc            = getSiteContent($pdo, 'social_media', 'facebook_desc',     $fbDesc);
    $igDesc            = getSiteContent($pdo, 'social_media', 'instagram_desc',    $igDesc);
    $igEmbed           = getSiteContent($pdo, 'social_media', 'instagram_embed',   $igEmbed);
} catch (PDOException $e) {
    // Tablo yoksa (migration çalıştırılmamışsa) varsayılan değerlerle devam et
}
?>

<?php if ($sectionVisible === '1'): ?>
<section class="social-media-section py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Bizi Sosyal Medyada Takip Edin</h2>
      <hr class="featurette-divider mx-auto" style="max-width:120px;">
    </div>
    <div class="row justify-content-center g-4">

      <?php if ($facebookVisible === '1'): ?>
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 h-100 text-center p-4">
          <div class="mb-3">
            <i class="fab fa-facebook" style="font-size:2.5rem; color:#1877f2;"></i>
          </div>
          <h4 class="fw-bold mb-2">Facebook</h4>
          <p class="text-muted flex-grow-1"><?= nl2br(htmlspecialchars($fbDesc)) ?></p>
          <div>
            <a href="<?= htmlspecialchars($fbUrl) ?>" target="_blank" rel="noopener" class="btn btn-success px-4">
              Sayfayı Ziyaret Et
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($instagramVisible === '1'): ?>
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 h-100 text-center p-4">
          <div class="mb-3">
            <i class="fab fa-instagram" style="font-size:2.5rem; color:#dc2743;"></i>
          </div>
          <h4 class="fw-bold mb-2">Instagram</h4>
          <p class="text-muted"><?= nl2br(htmlspecialchars($igDesc)) ?></p>
          <?php if (trim($igEmbed) !== ''): ?>
            <div class="d-flex justify-content-center mb-3">
              <!-- Güvenilir kaynak: bu HTML sadece admin panelinden (yetkili kullanıcı) girilebilir,
                   Instagram'ın kendi embed kodu olduğu için kasıtlı olarak escape edilmemiştir. -->
              <?= $igEmbed ?>
            </div>
          <?php endif; ?>
          <div>
            <a href="<?= htmlspecialchars($igUrl) ?>" target="_blank" rel="noopener" class="btn btn-success px-4">
              Sayfayı Ziyaret Et
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php if ($facebookVisible === '1'): ?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/tr_TR/sdk.js#xfbml=1&version=v19.0"></script>
<?php endif; ?>

<?php if ($instagramVisible === '1' && trim($igEmbed) !== ''): ?>
<script async src="https://www.instagram.com/embed.js"></script>
<?php endif; ?>

<?php endif; // sectionVisible ?>
