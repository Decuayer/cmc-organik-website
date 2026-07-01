$(document).ready(function() {

    // Ana sayfa ürün carousel'i
    var owl = $("#owl-demo");
    if (owl.length) {
        owl.owlCarousel({
            items : 3, 
            itemsDesktop : [1000,3], 
            itemsDesktopSmall : [900,3], 
            itemsTablet: [600,1], 
            itemsMobile : false 
        });
        owl.trigger('owl.play',5000);

        $(".next").click(function(){ owl.trigger('owl.next'); });
        $(".prev").click(function(){ owl.trigger('owl.prev'); });
        $(".play").click(function(){ owl.trigger('owl.play',5000); });
        $(".stop").click(function(){ owl.trigger('owl.stop'); });
    }

    // Şirketimiz fotoğraf carousel'i
    var owlCinfo = $('#owl-cinfo');
    if (owlCinfo.length) {
        owlCinfo.owlCarousel({
            margin: 10,
            loop: true,
            autoWidth: true,
            items: 4,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true
        });
    }

    // İş ortakları detay sayfası logo carousel'i
    var owlPartners = $('#owl-partners');
    if (owlPartners.length) {
        owlPartners.owlCarousel({
            margin: 10,
            loop: true,
            autoWidth: true,
            items: 4,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true
        });
    }

    // İş ortakları özet sayfası logo carousel'i
    var owlBussiness = $('#owl-bussiness');
    if (owlBussiness.length) {
        owlBussiness.owlCarousel({
            margin: 10,
            loop: true,
            autoWidth: true,
            items: 4,
            autoplay: true,
            autoplayTimeout: 3500,
            autoplayHoverPause: true
        });
    }

    // Yorumlar carousel'i
    var owlComments = $('#comments-carousel');
    if (owlComments.length) {
        owlComments.owlCarousel({
            items : 3, 
            itemsDesktop : [1000,3], 
            itemsDesktopSmall : [900,3], 
            itemsTablet: [600,1], 
            itemsMobile : false 
        });
    }

});