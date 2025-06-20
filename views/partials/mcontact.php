<div class="container mb-4">
    <h2 class="h1-responsive font-weight-bold my-4">Bize Ulaşın
    </h2>
    <hr class="featurette-divider">
    <p class="w-responsive mx-auto mb-5">
        Herhangi bir sorunuz var mı? Lütfen doğrudan bizimle iletişime geçmekten çekinmeyin. Ekibimiz size yardımcı olmak için birkaç saat içinde size geri dönecektir.
    </p>
    <div class="row">
        <div class="col-md-9 mb-md-0 mb-5">
            <form id="contact-form-home" action="/config/formhandler.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <input type="text" id="name" name="name" class="form-control">
                            <label for="name" class="">
                                <svg class="bi" width="1em" height="1em">
                                    <use xlink:href="#user"/>
                                </svg>
                                İsim
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <input type="email" id="email" name="email" required class="form-control">
                            <label for="email" class="">
                                <svg class="bi" width="1em" height="1em">
                                    <use xlink:href="#envelope"/>
                                </svg>
                                E-posta
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <input type="text" id="phone" name="phone" pattern="[0-9\s\-\+\(\)]{8,20}" class="form-control">
                            <label for="subject" class="">
                                <svg class="bi" width="1em" height="1em">
                                    <use xlink:href="#phone"/>
                                </svg>
                                Telefon
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <input type="text" id="subject" name="subject" class="form-control">
                            <label for="subject" class="">
                                <svg class="subject" width="1em" height="1em">
                                    <use xlink:href="#book"/>
                                </svg>
                                Konu
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="md-form">
                            <textarea type="text" id="message" name="message" rows="6" class="form-control md-textarea"></textarea>
                            <label for="message">
                                <svg class="bi" width="1em" height="1em">
                                    <use xlink:href="#message"/>
                                </svg>
                                İletiniz
                            </label>
                        </div>
                    </div>
                </div>
                <div class="text-center text-md-left">
                    <button type="submit" class="btn btn-success">Gönder</button>
                </div>
            </form>
            <div class="status"></div>
            </div>
            <div class="col-md-3 text-center">
                <ul class="list-unstyled mb-0">
                    <li>
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                        <p>
                            Ulucak Cumhuriyet Mh. 9142 Sk. No: 5/1
                            35735 Kemalpaşa / İZMİR
                        </p>
                    </li>
                    <li>
                        <i class="fas fa-phone mt-4 fa-2x"></i>
                        <p>+90 232 478 00 78</p>
                    </li>
                    <li>
                        <i class="fas fa-envelope mt-4 fa-2x"></i>
                        <p>cmcorganik@hotmail.com</p>
                    </li>
                </ul>
            </div>
        </div>
</div>
