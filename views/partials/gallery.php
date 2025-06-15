<section class="container py-5">
    <h2 class="pb-2 border-bottom">Galeri</h2>
    <div class="container page-top">
        <div class="row">
            <?php 
            $dir = realpath(__DIR__ . '/../../public/img/gallery/');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    $filePath = $dir . $file;
                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    
                    if(in_array($fileExtension, $allowedExtensions)) {
                        $escapedPath = htmlspecialchars($filePath);
                        echo '
                            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                                <a href="/cmc-organik-website/public/img/gallery/' . $file . '" class="fancybox" rel="ligthbox">
                                    <img src="/cmc-organik-website/public/img/gallery/' . $file . '" class="zoom img-fluid" alt="' . $file . '">
                                </a>
                            </div>
                        ';
                    }
                }
            } else {
                echo "<p>Galeri klasörü bulunamadı.</p>";
            }
            ?>
        </div>
    </div>
</section>

