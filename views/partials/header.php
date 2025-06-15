<?php
require_once __DIR__ . '/../../config/database.php';

$currentPage = basename($_SERVER['PHP_SELF']);

$typesQuery = $pdo->query("SELECT * FROM product_types ORDER BY idproduct_types ASC");
$productTypes = $typesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Topbar -->
<div id="topbar" class="bg-success text-white py-2 fixed-top d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 d-flex flex-wrap align-items-center mb-2 mb-md-0">
                <i class="fas fa-envelope me-2"></i>
                <a href="mailto:cmcorganik@hotmail.com" class="text-white text-decoration-none me-3">cmcorganik@hotmail.com</a>
                <i class="fas fa-phone me-2"></i>
                <a href="tel:+902324780078" class="text-white text-decoration-none">+90 (232) 478 00 78</a>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-start gap-3">
                <a href="https://www.instagram.com/cmcorganik/" target="_blank" class="text-white">
                    <svg class="bi" width="20" height="20"><use xlink:href="#instagram"/></svg>
                </a>
                <a href="https://www.facebook.com/cmcorganikizmir" target="_blank" class="text-white">
                    <svg class="bi" width="20" height="20"><use xlink:href="#facebook"/></svg>
                </a>
                <a href="https://www.twitter.com" target="_blank" class="text-white">
                    <svg class="bi" width="20" height="20"><use xlink:href="#twitter"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Navbar -->
<nav id="navbar" class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark py-3 mb-4" aria-label="Offcanvas navbar large">
    <div class="container container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img class="" src="public/img/logo-white.png" alt="logo" width="50" height="50">
            <span class="m-2">CMC Organik</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar2" aria-controls="offcanvasNavbar2" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar2" aria-labelledby="offcanvasNavbar2Label">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbar2Label">Menü</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" aria-current="page" href="index.php">
                            Anasayfa
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-toggle <?= in_array($currentPage, ['about.php', 'company.php', 'partners.php']) ? 'active' : '' ?>" href="editing.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Hakkında
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php">Hakkımızda</a></li>
                            <li><a class="dropdown-item" href="company.php">Şirketimiz</a></li>
                            <li><a class="dropdown-item" href="partners.php">İş Ortaklarımız</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($currentPage == 'bproducts.php') ? 'active' : '' ?>" href="editing.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Ürünler
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($productTypes as $index => $type): ?>
                                <?php if ((int)$type['diff'] === 1): ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a href="bproducts.php?category=<?= urlencode($type['name']) ?>" class="dropdown-item">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['editing.php', 'gallery.php']) ? 'active' : '' ?>" href="editing.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Belgeler
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="editing.php">Tesciller</a></li>
                            <li><a class="dropdown-item" href="gallery.php">Galeri</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'contact.php') ? 'active' : '' ?>" aria-current="page" href="contact.php">İletişim</a>
                    </li>
                </ul>
                <form class="d-flex mt-3 mt-lg-0" role="search">
                    <input class="form-control me-2 search" type="search" placeholder="Ara" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<div id="header-space"></div>
