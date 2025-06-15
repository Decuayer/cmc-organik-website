<section class="container py-5">
    <h2 class="pb-2 border-bottom">İletişim</h2>
    <div class="py-3">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3123.655209871667!2d27.364389300000003!3d38.472520599999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b96356bdef71b5%3A0x46da62ef1fcb5dd1!2zQ01DIE9yZ2FuaWsgVGFyxLFtIMOccsO8bmxlcmkgTHRkLiDFnnRpLg!5e0!3m2!1str!2str!4v1749926998915!5m2!1str!2str" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="container contact-section py-3">
        <div class="row">
            <h2 class="pb-2 border-bottom">İletişim Formu</h3>
            <div class="col-md-7 mb-4">
                <form action="form-handler.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon Numarası</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Konu</label>
                        <input type="text" name="subject" id="subject" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Mesaj</label>
                        <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Gönder</button>
                </form>
            </div>
            <div class="col-md-5">
                <div class="bg-light p-4 rounded shadow-sm h-100">
                    <h4 class="mb-4 text-success">İletişim Bilgileri</h4>
                    
                    <div class="mb-3">
                        <i class="bi bi-geo-alt-fill me-2 text-success"></i>
                        <strong>Adres:</strong><br>
                        Ulucak Cumhuriyet, 9142. Sk No:2<br>
                        35735 Kemalpaşa / İzmir
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-telephone-fill me-2 text-success"></i>
                        <strong>Telefon:</strong><br>
                        <a href="tel:+902324780078" class="text-decoration-none text-dark">+90 (232) 478 00 78</a>
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-envelope-fill me-2 text-success"></i>
                        <strong>E-posta:</strong><br>
                        <a href="mailto:cmcorganik@hotmail.com" class="text-decoration-none text-dark">cmcorganik@hotmail.com</a>
                    </div>
                    
                    <div>
                        <i class="bi bi-clock-fill me-2 text-success"></i>
                        <strong>Çalışma Saatleri:</strong><br>
                        Hafta içi 09:00 - 18:00
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>