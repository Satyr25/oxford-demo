function scroll(){
    revisaVisible();
    var top = $(window).scrollTop();
    if(top > 20){
        if($('header .container').hasClass('transparente')){
            $('header .container').removeClass('transparente')
            // $('#menu').addClass('oculto');
            // $('#boton-menu').addClass('visible');
        }
    }else{
        if(!$('header .container').hasClass('transparente')){
            $('header .container').addClass('transparente')
            // $('#menu').removeClass('oculto');
            // $('#boton-menu').removeClass('visible');
        }
    }
}

function revisaVisible(){
    if($('#london').visible() && !$('#mission-vision').visible(true)){
        if(!$('#menu-home').hasClass('seleccionado')){
            $('#menu a').removeClass('seleccionado');
            $('#menu-home').addClass('seleccionado');
        }
    }else if($('#mission-vision').visible(true)){
        $('#mission-vision').addClass('visible');
        if(!$('#menu-about').hasClass('seleccionado')){
            $('#menu a').removeClass('seleccionado');
            $('#menu-about').addClass('seleccionado');
        }
    }else if($('#what-we-do').visible(true)){
        if(!$('#menu-doit').hasClass('seleccionado')){
            $('#menu a').removeClass('seleccionado');
            $('#menu-doit').addClass('seleccionado');
        }
    }else if($('#associates').visible(true)){
        if(!$('#menu-associates').hasClass('seleccionado')){
            $('#menu a').removeClass('seleccionado');
            $('#menu-associates').addClass('seleccionado');
        }
    }
}

function resize(){
    if(window.innerWidth > 600){
        $('.user-type').each(function(){
            var altura = 0;
            $(this).find('.level-info').each(function(){
                if($(this).height() > altura){
                    altura = $(this).height();
                }
            });
            $(this).find('.level-info').height(altura);
        });
    }else{
        $('.user-type .level-info').removeAttr('style');
    }
}

jQuery(document).ready(function($){
    scroll();
    jQuery(window).scroll(function(){
        scroll();
    });

    resize();
    $(window).resize(function(){
        resize();
    });

    $('.desplegar-menu').on('click', function(){
        $('body').toggleClass('desplegado');
    });

    $('.scroll-to').bind('click', function(e) {
        try {
            e.preventDefault();
            var target = $(this).attr('href');
            if(target.charAt(0) == "/"){
                target = target.substring(1);

            }
            if($('.desplegado').length){
                $('.desplegar-menu').click();
            }
            $('body,html').animate({
                scrollTop: $(target).offset().top - (Math.ceil($('header').height()))
                }, 800);
        } catch (error) {
            window.location.href = $(this).attr('href');
        }
    });

    $('.perfil-switch').on('click', function(){
        $('#profile').toggleClass('visible');
    });

    $('#signup-here a').on('click', function(){
        $('#campos-registro').slideToggle();
    });

    $('.select-login-btn').on('click', function(){
        $('.select-login-btn').removeClass('selected');
        $(this).addClass('selected');
        if($(this).data('tipo') == 'STU'){
            $('#institute-form').addClass('oculto');
            $('#student-form').removeClass('oculto');
        } else{
            $('#student-form').addClass('oculto');
            $('#institute-form').removeClass('oculto');
        }
    });

    $('.popup-with-zoom-anim').magnificPopup({
          type: 'inline',
          fixedContentPos: true,
          fixedBgPos: true,

          overflowY: 'auto',

          closeBtnInside: true,
          preloader: false,

          midClick: true,
          removalDelay: 300,
          mainClass: 'my-mfp-zoom-in'
      });

      $('#recover').on('click', function(){
          $('#password-form').slideToggle();
      });

      $('.help-button').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,

          fixedContentPos: true,
          closeOnContentClick: false,
          closeOnBgClick: false,
          showCloseBtn: true,
          enableEscapeKey: true,
      });

    $('.show-pass').on('click', function () {
        $(this).toggleClass('glyphicon-eye-close').toggleClass('glyphicon-eye-open');
        $(this).siblings('input.form-control').attr('type', ($(this).data('showing') == 0 ? 'text' : 'password'));
        $(this).data('showing', ($(this).data('showing') == 0 ? '1' : '0'));
    });
});
