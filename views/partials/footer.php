<?php
require_once __DIR__ . '/../../config/database.php';

$currentPage = basename($_SERVER['PHP_SELF']);

$typesQuery = $pdo->query("SELECT * FROM product_types ORDER BY idproduct_types ASC");
$productTypes = $typesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <footer class="pt-5">
    <hr class="featurette-divider">
        <div class="row mt-5">
            <div class="col-6 col-md-2 mb-3">
                <h5>Ürünlerimiz</h5>
                <ul class="nav flex-column">
                    <?php foreach ($productTypes as $index => $type): ?>
                        <li class="nav-item mb-2">
                            <a href="bproducts.php?category=<?= urlencode($type['name']) ?>" class="nav-link p-0 text-body-secondary">
                                <?= htmlspecialchars($type['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <h5>Hakkında</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="about.php" class="nav-link p-0 text-body-secondary">Hakkımızda</a></li>
                    <li class="nav-item mb-2"><a href="company.php" class="nav-link p-0 text-body-secondary">Şirketimiz</a></li>
                    <li class="nav-item mb-2"><a href="partners.php" class="nav-link p-0 text-body-secondary">İş Ortaklarımız</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2 mb-3">
                <h5>Belgeler</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="editing.php" class="nav-link p-0 text-body-secondary">Tesciller</a></li>
                    <li class="nav-item mb-2"><a href="gallery.php" class="nav-link p-0 text-body-secondary">Galeri</a></li>

                </ul>
            </div>
            <div class="col-md-5 offset-md-1 mb-3">
                <form>
                    <h5>Bültenimize abone olun</h5>
                    <p>Bizden gelen yeni ve heyecan verici şeylerin aylık özeti.</p>
                    <div class="d-flex flex-column flex-sm-row w-100 gap-2">
                        <label for="newsletter1" class="visually-hidden">E-posta adresi</label>
                        <input id="newsletter1" type="text" class="form-control" placeholder="E-posta adresi">
                        <button class="btn btn-success" type="button">Abone</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="d-flex flex-column flex-sm-row justify-content-between py-4 my-4 border-top">
            <p>&copy; 2024 CMC Organik Tarım Ltd. Şti. - Bütün hakları saklıdır.</p>
            <ul class="list-unstyled d-flex">
                <li class="ms-3"><a class="link-body-emphasis" href="https://www.twitter.com"><svg class="bi" width="24" height="24"><use xlink:href="#twitter"/></svg></a></li>
                <li class="ms-3"><a class="link-body-emphasis" href="https://www.facebook.com/cmcorganikizmir"><svg class="bi" width="24" height="24"><use xlink:href="#instagram"/></svg></a></li>
                <li class="ms-3"><a class="link-body-emphasis" href="https://www.instagram.com/cmcorganik/"><svg class="bi" width="24" height="24"><use xlink:href="#facebook"/></svg></a></li>
            </ul>
        </div>
    </footer>
</div>