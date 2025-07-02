<!-- Mobil Navbar (sadece md altı görünür) -->
<nav class="navbar navbar-light bg-light d-md-none">
  <div class="container-fluid">
    <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
      ☰ Menü
    </button>
    <span class="navbar-brand mb-0 h1">CMC Organik Admin Paneli</span>
  </div>
</nav>

<div id="sidebar" class="d-none d-md-flex flex-column flex-shrink-0 p-3 bg-white border-end position-fixed" style="width: 250px; height: 100vh;">
  <h5 class="d-flex align-items-center mb-3 text-success fw-bold fs-4">CMC Organik Admin Paneli</h5>
  <hr/>
  <ul class="nav nav-pills flex-column mb-auto">
    <li><a href="products.php" class="nav-link">Ürünler</a></li>
    <li><a href="categories.php" class="nav-link">Kategoriler</a></li>
    <li><a href="comments.php" class="nav-link">Yorumlar</a></li>
    <li><a href="contact.php" class="nav-link">İletişim</a></li>
    <li><a href="gallery.php" class="nav-link">Galeri</a></li>
    <li><a href="documents.php" class="nav-link">Tescil Belgeleri</a></li>
    <li><a href="user.php" class="nav-link">Kullanıcılar</a></li>
    <li><a href="../logout.php" class="nav-link">Çıkış</a></li>
  </ul>
</div>

<div class="offcanvas offcanvas-start d-md-none p-3" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-success fw-bold" id="sidebarMobileLabel">CMC Organik Admin Paneli</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <hr/>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="products.php" class="nav-link">Ürünler</a></li>
        <li><a href="categories.php" class="nav-link">Kategoriler</a></li>
        <li><a href="comments.php" class="nav-link">Yorumlar</a></li>
        <li><a href="contact.php" class="nav-link">İletişim</a></li>
        <li><a href="gallery.php" class="nav-link">Galeri</a></li>
        <li><a href="documents.php" class="nav-link">Tescil Belgeleri</a></li>
        <li><a href="user.php" class="nav-link">Kullanıcılar</a></li>
        <li><a href="../logout.php" class="nav-link">Çıkış</a></li>
        </ul>
    </div>
</div>
