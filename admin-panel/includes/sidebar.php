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
      ☰ Menü
    </button>
    <span class="navbar-brand mb-0 h1">CMC Organik Admin</span>
    <?php if ($isMaintenanceActive): ?>
      <span class="badge bg-danger">🔴 Bakım</span>
    <?php endif; ?>
  </div>
</nav>

<div id="sidebar" class="d-none d-md-flex flex-column flex-shrink-0 p-3 bg-white border-end position-fixed" style="width: 260px; height: 100vh; overflow-y: auto;">
  <h5 class="d-flex align-items-center mb-1 text-success fw-bold fs-5">CMC Organik Admin</h5>
  <?php if ($isMaintenanceActive): ?>
    <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:0.78rem;">
      🔴 Bakım Modu Aktif
    </div>
  <?php endif; ?>
  <hr/>
  <ul class="nav nav-pills flex-column mb-auto" style="font-size:0.9rem;">

    <!-- Genel -->
    <li class="nav-item">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">Genel</span>
    </li>
    <li><a href="dashboard.php" class="nav-link <?= $currentAdminPage === 'dashboard.php' ? 'active' : 'text-dark' ?>">📊 Dashboard</a></li>
    <li>
      <a href="maintenance.php" class="nav-link <?= $currentAdminPage === 'maintenance.php' ? 'active' : ($isMaintenanceActive ? 'text-danger fw-semibold' : 'text-dark') ?>">
        🔧 Bakım Modu
        <?php if ($isMaintenanceActive): ?>
          <span class="badge bg-danger ms-1" style="font-size:0.65rem;">AKTİF</span>
        <?php endif; ?>
      </a>
    </li>

    <!-- Ürünler -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">Ürünler</span>
    </li>
    <li><a href="products.php" class="nav-link <?= $currentAdminPage === 'products.php' ? 'active' : 'text-dark' ?>">📦 Ürünler</a></li>
    <li><a href="best_products.php" class="nav-link <?= $currentAdminPage === 'best_products.php' ? 'active' : 'text-dark' ?>">⭐ Öne Çıkan Ürünler</a></li>
    <li><a href="categories.php" class="nav-link <?= $currentAdminPage === 'categories.php' ? 'active' : 'text-dark' ?>">🏷️ Kategoriler</a></li>

    <!-- İçerik Yönetimi -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">İçerik</span>
    </li>
    <li><a href="content_manager.php" class="nav-link <?= $currentAdminPage === 'content_manager.php' ? 'active' : 'text-dark' ?>">✏️ İçerik Yönetimi</a></li>
    <li><a href="company_content.php" class="nav-link <?= $currentAdminPage === 'company_content.php' ? 'active' : 'text-dark' ?>">🏢 Şirketimiz</a></li>
    <li><a href="partners.php" class="nav-link <?= $currentAdminPage === 'partners.php' ? 'active' : 'text-dark' ?>">🤝 İş Ortakları</a></li>
    <li><a href="gallery.php" class="nav-link <?= $currentAdminPage === 'gallery.php' ? 'active' : 'text-dark' ?>">🖼️ Galeri</a></li>
    <li><a href="documents.php" class="nav-link <?= $currentAdminPage === 'documents.php' ? 'active' : 'text-dark' ?>">📄 Tescil Belgeleri</a></li>

    <!-- İletişim -->
    <li class="nav-item mt-2">
      <span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600; letter-spacing:.5px;">İletişim & Kullanıcılar</span>
    </li>
    <li><a href="contact.php" class="nav-link <?= $currentAdminPage === 'contact.php' ? 'active' : 'text-dark' ?>">📬 İletişim Mesajları</a></li>
    <li><a href="comments.php" class="nav-link <?= $currentAdminPage === 'comments.php' ? 'active' : 'text-dark' ?>">💬 Yorumlar</a></li>
    <li><a href="newsletter.php" class="nav-link <?= $currentAdminPage === 'newsletter.php' ? 'active' : 'text-dark' ?>">📧 Bülten Mailleri</a></li>
    <li><a href="user.php" class="nav-link <?= $currentAdminPage === 'user.php' ? 'active' : 'text-dark' ?>">👤 Kullanıcılar</a></li>

    <!-- Çıkış -->
    <li class="nav-item mt-2">
      <hr/>
    </li>
    <li><a href="../logout.php" class="nav-link text-danger">🚪 Çıkış</a></li>
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
        🔴 Bakım Modu Aktif
      </div>
    <?php endif; ?>
    <hr/>
    <div class="offcanvas-body p-0">
        <ul class="nav nav-pills flex-column mb-auto" style="font-size:0.9rem;">
          <li><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">Genel</span></li>
          <li><a href="dashboard.php" class="nav-link text-dark">📊 Dashboard</a></li>
          <li>
            <a href="maintenance.php" class="nav-link <?= $isMaintenanceActive ? 'text-danger fw-semibold' : 'text-dark' ?>">
              🔧 Bakım Modu <?php if ($isMaintenanceActive): ?><span class="badge bg-danger" style="font-size:0.65rem;">AKTİF</span><?php endif; ?>
            </a>
          </li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">Ürünler</span></li>
          <li><a href="products.php" class="nav-link text-dark">📦 Ürünler</a></li>
          <li><a href="best_products.php" class="nav-link text-dark">⭐ Öne Çıkan Ürünler</a></li>
          <li><a href="categories.php" class="nav-link text-dark">🏷️ Kategoriler</a></li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">İçerik</span></li>
          <li><a href="content_manager.php" class="nav-link text-dark">✏️ İçerik Yönetimi</a></li>
          <li><a href="company_content.php" class="nav-link text-dark">🏢 Şirketimiz</a></li>
          <li><a href="partners.php" class="nav-link text-dark">🤝 İş Ortakları</a></li>
          <li><a href="gallery.php" class="nav-link text-dark">🖼️ Galeri</a></li>
          <li><a href="documents.php" class="nav-link text-dark">📄 Tescil Belgeleri</a></li>
          <li class="mt-2"><span class="text-muted px-2" style="font-size:0.72rem; text-transform:uppercase; font-weight:600;">İletişim & Kullanıcılar</span></li>
          <li><a href="contact.php" class="nav-link text-dark">📬 İletişim Mesajları</a></li>
          <li><a href="comments.php" class="nav-link text-dark">💬 Yorumlar</a></li>
          <li><a href="newsletter.php" class="nav-link text-dark">📧 Bülten Mailleri</a></li>
          <li><a href="user.php" class="nav-link text-dark">👤 Kullanıcılar</a></li>
          <li><hr/></li>
          <li><a href="../logout.php" class="nav-link text-danger">🚪 Çıkış</a></li>
        </ul>
    </div>
</div>
