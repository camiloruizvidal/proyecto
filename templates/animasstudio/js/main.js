jQuery(function ($) {
    'use strict';
    (function () {
        function parallaxInit() {
            $("#ticket").parallax("50%", 0.3);
            $("#choose-color").parallax("50%", 0.3);
            $("#blue #choose-color").parallax("50%", 0.3);
        }
        parallaxInit();
    });
    (function () {
        $('#fun-facts, #achievement').bind('inview', function (event, visible, visiblePartX, visiblePartY) {
            if (visible) {
                $(this).find('.timer').each(function () {
                    var $this = $(this);
                    $({Counter: 0}).animate({Counter: $this.text()}, {
                        duration: 2000,
                        easing: 'swing',
                        step: function () {
                            $this.text(Math.ceil(this.Counter));
                        }
                    });
                });
                $(this).unbind('inview');
            }
        });
    }());
    (function () {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(this).attr('href');
            $(target).css('top', '-' + $(window).width() + 'px');
            var top = $(target).offset().top;
            $(target).css({top: top}).animate({"top": "0px"}, "-20");
        });
    }());
    (function () {
        $('.image-link').magnificPopup({
            gallery: {
                enabled: true
            },
            type: 'image'
        });
        $('.feature-image .image-link').magnificPopup({
            gallery: {
                enabled: false
            },
            type: 'image'
        });
        $('.image-popup').magnificPopup({
            type: 'image'
        });
        $('.video-link').magnificPopup({type: 'iframe'});
    }());
    (function () {
        var win = $(window),
                foo = $('#typer');
        foo.typer(["Introduciendo Nuevas Ideas Al Mundo", "Obten Nuevas Ideas y Conceptos", "Construye Tu Sueño"]);
        foo = $('#promotion h1');
        foo.typer(["¿Deseas trabajar con nostros?", "Haz Que Tus Sueños Pasen"]);
    }());

    (function () {
        $(window).load(function () {
            $(".layer-slide").twentytwenty();
        });
    }());
    (function () {
        $('.faqs .collapse').on('shown.bs.collapse', function () {
            $(this).parent().find(".fa-plus-circle").removeClass("fa-plus-circle").addClass("fa-minus-circle");
        }).on('hidden.bs.collapse', function () {
            $(this).parent().find(".fa-minus-circle").removeClass("fa-minus-circle").addClass("fa-plus-circle");
        });
        $('.faqs .panel-heading').on('click', function () {
            if (!$(this).hasClass('active'))
            {
                $('.panel-heading').removeClass('active');
                $(this).addClass('active');
                setIconOpened(this);
            }
            else
            {
                if ($(this).find('b').hasClass('opened'))
                {
                    setIconOpened(null);
                }
                else
                {
                    setIconOpened(this);
                }
            }
        });
    }());
    (function () {
        $(window).load(function () {
            var $portfolio_selectors = $('.project-filter >ul>li>a');
            var $portfolio = $('#projects');
            $portfolio.isotope({
                itemSelector: '.project-content',
                layoutMode: 'fitRows'
            });
            $portfolio_selectors.on('click', function () {
                $portfolio_selectors.removeClass('active');
                $(this).addClass('active');
                var selector = $(this).attr('data-filter');
                $portfolio.isotope({filter: selector});
                return false;
            });
        });
    }());
    (function () {
        $(window).load(function () {
            var $portfolio_selectors = $('.architect-filter >ul>li>a');
            var $portfolio = $('#all-architect');
            $portfolio.isotope({
                itemSelector: '.architect',
                layoutMode: 'fitRows'
            });
            $portfolio_selectors.on('click', function () {
                $portfolio_selectors.removeClass('active');
                $(this).addClass('active');
                var selector = $(this).attr('data-filter');
                $portfolio.isotope({filter: selector});
                return false;
            });
        });
    }());
});
