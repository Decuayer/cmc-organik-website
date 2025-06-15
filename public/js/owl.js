$(document).ready(function() {

    var owl = $("#owl-demo");

    owl.owlCarousel({
        items : 3, 
        itemsDesktop : [1000,3], 
        itemsDesktopSmall : [900,3], 
        itemsTablet: [600,1], 
        itemsMobile : false 
    });
    owl.trigger('owl.play',5000);

    // Custom Navigation Events
    $(".next").click(function(){
        owl.trigger('owl.next');
    })
    $(".prev").click(function(){
        owl.trigger('owl.prev');
    })
    $(".play").click(function(){
        owl.trigger('owl.play',5000);
    })
    $(".stop").click(function(){
        owl.trigger('owl.stop');
    })
    
    var owl2 = $("#owl-info");

    owl2.owlCarousel({
        margin:10,
        loop:true,
        autoWidth:true,
        items:4
    })

    var owl3 = $('#comments-carousel');


    owl3.owlCarousel({
        items : 3, 
        itemsDesktop : [1000,3], 
        itemsDesktopSmall : [900,3], 
        itemsTablet: [600,1], 
        itemsMobile : false 
    });

});