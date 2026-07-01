<?php
// Bakım modu durumunu kontrol et
$maintenanceFlag = realpath(__DIR__ . '/../../') . '/maintenance.flag';
$isMaintenanceActive = file_exists($maintenanceFlag);

// Aktif sayfa tespiti
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobil Navbar (sadece md altı görünür) -->
<nav class="navbar navbar-light bg-light d-md-none">
  <div class="container-fluid">
    <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
      <i class="bi bi-list"></i> Menü
    </button>
    <span class="navbar-brand mb-0 h1">CMC Organik Admin</span>
    <?php if ($isMaintenanceActive): ?>
      <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Bakım</span>
    <?php endif; ?>
  </div>
</nav>

<div id="sidebar" class="d-none d-md-flex flex-column flex-shrink-0 p-3 bg-white border-end position-fixed" style="width: 260px; height: 100vh; overflow-y: auto;">
  <h5 class="d-flex align-items-center mb-1 text-success fw-bold fs-5">CMC Organik Admin</h5>
  <?php if ($isMaintenanceActive): ?>
    <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:0.78rem;">
      <i class="bi bi-exclamation-triangle-fill"></i> Bakım Modu Aktif
    </div>
  <?php endif; ?>
  <hr/>
  <ul class="nav nav-pills flex-column mb-auto" style="font-size:0.9rem;">

    <!-- Genel -->
    <li class="nav-item">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">Genel</span>
    </li>
    <li><a href="dashboard.php" class="nav-link <?= $currentAdminPage === 'dashboard.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
    <li>
      <a href="maintenance.php" class="nav-link <?= $currentAdminPage === 'maintenance.php' ? 'active' : ($isMaintenanceActive ? 'text-danger fw-semibold' : 'text-dark') ?>">
        <i class="bi bi-tools me-1"></i> Bakım Modu
        <?php if ($isMaintenanceActive): ?>
          <span class="badge bg-danger ms-1" style="font-size:0.65rem;">AKTİF</span>
        <?php endif; ?>
      </a>
    </li>

    <!-- Ürünler -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">Ürünler</span>
    </li>
    <li><a href="products.php" class="nav-link <?= $currentAdminPage === 'products.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-box-seam me-1"></i> Ürünler</a></li>
    <li><a href="best_products.php" class="nav-link <?= $currentAdminPage === 'best_products.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-star-fill me-1"></i> Öne Çıkan Ürünler</a></li>
    <li><a href="categories.php" class="nav-link <?= $currentAdminPage === 'categories.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-tags me-1"></i> Kategoriler</a></li>

    <!-- İçerik Yönetimi -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">İçerik</span>
    </li>
    <li><a href="content_manager.php" class="nav-link <?= $currentAdminPage === 'content_manager.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-pencil-square me-1"></i> İçerik Yönetimi</a></li>
    <li><a href="company_content.php" class="nav-link <?= $currentAdminPage === 'company_content.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-building me-1"></i> Şirketimiz</a></li>
    <li><a href="carousel.php" class="nav-link <?= $currentAdminPage === 'carousel.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-images me-1"></i> Carousel</a></li>
    <li><a href="social_media.php" class="nav-link <?= $currentAdminPage === 'social_media.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-share me-1"></i> Sosyal Medya</a></li>
    <li><a href="partners.php" class="nav-link <?= $currentAdminPage === 'partners.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-people me-1"></i> İş Ortakları</a></li>
    <li><a href="gallery.php" class="nav-link <?= $currentAdminPage === 'gallery.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-collection me-1"></i> Galeri</a></li>
    <li><a href="documents.php" class="nav-link <?= $currentAdminPage === 'documents.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-file-earmark-text me-1"></i> Tescil Belgeleri</a></li>

    <!-- İletişim -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">İletişim & Kullanıcılar</span>
    </li>
    <li><a href="contact.php" class="nav-link <?= $currentAdminPage === 'contact.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-envelope me-1"></i> İletişim Mesajları</a></li>
    <li><a href="comments.php" class="nav-link <?= $currentAdminPage === 'comments.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-chat-dots me-1"></i> Yorumlar</a></li>
    <li><a href="newsletter.php" class="nav-link <?= $currentAdminPage === 'newsletter.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-envelope-paper me-1"></i> Bülten Mailleri</a></li>
    <li><a href="user.php" class="nav-link <?= $currentAdminPage === 'user.php' ? 'active' : 'text-dark' ?>"><i class="bi bi-person me-1"></i> Kullanıcılar</a></li>

    <!-- Çıkış -->
    <li class="nav-item mt-2">
      <hr/>
    </li>
    <li><a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-1"></i> Çıkış</a></li>
  </ul>
</div>

<!-- Mobil Offcanvas -->
<div class="offcanvas offcanvas-start d-md-none p-3" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-success fw-bold" id="sidebarMobileLabel">CMC Organik Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <?php if ($isMaintenanceActive): ?>
      <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:0.78rem;">
        <i class="bi bi-exclamation-triangle-fill"></i> Bakım Modu Aktif
      </div>
    <?php endif; ?>
    <hr/>
    <div class="offcanvas-body p-0">
        <ul class="nav nav-pills flex-column mb-auto" style="font-size:0.9rem;">
          <li><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">Genel</span></li>
          <li><a href="dashboard.php" class="nav-link text-dark"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
          <li>
            <a href="maintenance.php" class="nav-link <?= $isMaintenanceActive ? 'text-danger fw-semibold' : 'text-dark' ?>">
              <i class="bi bi-tools me-1"></i> Bakım Modu <?php if ($isMaintenanceActive): ?><span class="badge bg-danger" style="font-size:0.65rem;">AKTİF</span><?php endif; ?>
            </a>
          </li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">Ürünler</span></li>
          <li><a href="products.php" class="nav-link text-dark"><i class="bi bi-box-seam me-1"></i> Ürünler</a></li>
          <li><a href="best_products.php" class="nav-link text-dark"><i class="bi bi-star-fill me-1"></i> Öne Çıkan Ürünler</a></li>
          <li><a href="categories.php" class="nav-link text-dark"><i class="bi bi-tags me-1"></i> Kategoriler</a></li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">İçerik</span></li>
          <li><a href="content_manager.php" class="nav-link text-dark"><i class="bi bi-pencil-square me-1"></i> İçerik Yönetimi</a></li>
          <li><a href="company_content.php" class="nav-link text-dark"><i class="bi bi-building me-1"></i> Şirketimiz</a></li>
          <li><a href="carousel.php" class="nav-link text-dark"><i class="bi bi-images me-1"></i> Carousel</a></li>
          <li><a href="social_media.php" class="nav-link text-dark"><i class="bi bi-share me-1"></i> Sosyal Medya</a></li>
          <li><a href="partners.php" class="nav-link text-dark"><i class="bi bi-people me-1"></i> İş Ortakları</a></li>
          <li><a href="gallery.php" class="nav-link text-dark"><i class="bi bi-collection me-1"></i> Galeri</a></li>
          <li><a href="documents.php" class="nav-link text-dark"><i class="bi bi-file-earmark-text me-1"></i> Tescil Belgeleri</a></li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">İletişim & Kullanıcılar</span></li>
          <li><a href="contact.php" class="nav-link text-dark"><i class="bi bi-envelope me-1"></i> İletişim Mesajları</a></li>
          <li><a href="comments.php" class="nav-link text-dark"><i class="bi bi-chat-dots me-1"></i> Yorumlar</a></li>
          <li><a href="newsletter.php" class="nav-link text-dark"><i class="bi bi-envelope-paper me-1"></i> Bülten Mailleri</a></li>
          <li><a href="user.php" class="nav-link text-dark"><i class="bi bi-person me-1"></i> Kullanıcılar</a></li>
          <li><hr/></li>
          <li><a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-1"></i> Çıkış</a></li>
        </ul>
    </div>
</div>
