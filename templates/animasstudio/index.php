<?php
defined('_JEXEC') or die;
include_once JPATH_THEMES . '/' . $this->template . '/logic.php';
?>
<!doctype html>
<html lang="<?php echo $this->language; ?>">

    <head>
    <jdoc:include type="head"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/images/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $tpath; ?>/images/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $tpath; ?>/images/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $tpath; ?>/images/apple-touch-icon-144x144-precomposed.png">

    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta name ="apple-mobile-web-app-capable" content="IE=edge, chrome=1" /><!--nos dice que es compatible con el navegador safari-->
    <meta name="description" content="CRV|SOLUCIONES SOFTWARE es una empresa desarrolladora de software, aplicativos moviles y desarrollo de contenidos digitales." />
    <meta name="author" content="CRV| SOLUCIONES SOFTWARE" />
    <meta name="description" content="Home page" />
    <meta name="keywords" content="marketing digital, desarrollo software, contenidos digitales, aplicativos moviles, optimizacion del negocio,calidad,responsabilidad y valor" />		
    <meta property="og:title" content="Experiencias elaborado contenidos digitales"> <!-- creo que debe ir Experiencias elaborado contenidos digitales el titulo de la pagina -->
    <meta property="og:description" content="CRV|SOLUCIONES SOFTWARE es una empresa desarrolladora de software, aplicativos moviles y desarrollo de contenidos digitales.">
    <meta property="og:image" content="http://www.solucionescrv.com/img/ui/Logo-cuadrado.png"> <!--debemos colocar la imagen de CRV el logo, no se si lo hice bien.-->
    <meta property="og:type" content="website">
    <meta property="og:url" content="index.html">



</head>
<body class="<?php echo (($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')) . ' ' . $active->alias . ' ' . $pageclass; ?>">
    <!-- Fixed navbar -->
    <span itemscope itemtype="http://schema.org/LocalBusiness">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./"><img src="<?php echo $tpath; ?>/images/logo2.png"></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse navbar-right">
                    <jdoc:include type="modules" name="navbaram" />

                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container-fluid" id="content">
            <jdoc:include type="component" />
        </div>

        <footer id="footer">			
            <div class="container">
                <div class="padding">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="footer-widget">
                                <a class="logo" href="#"><img class="img-responsive" src="images/logo2.png" alt=""></a>
                                <p>Somos una empresa de comunicación visual, confiable, eficiente e innovadora, con  talento humano id</p>
                                <address>
                                    <ul>
                                        <li><span class="labels">Dirección:</span><a itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" target="_blank" href="https://goo.gl/maps/HP9oEGisxwP2">
                                                <span itemprop="streetAddress"><i class="fa fa-map-marker"></i> Carrera 10ª N° 1n 22, Barrio Modelo</span></a></li>
                                        <li><span class="labels">Teléfono:</span><a href="tel:314672835">
                                                <span itemprop="telephone">(+57) 314672835</span></a></li>
                                        <li><span class="labels">Email:</span><a href="mailto:animascoombia@gmail.com">
                                                <span itemprop="email">animascoombia@gmail.com</span></a></li>
                                    </ul>
                                </address>
                                <div class="socials">
                                    <ul class="list-inline social-icons">
                                        <li><a href="https://facebook.com/animasstudio"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="http://twiter.com/@animasstudio "><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="https://youtube.com/c/AniMaspublicidad"><i class="fa fa-youtube"></i></a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="footer-widget">
                                <h2>Links de Interes</h2>
                                <div id="NavbarInteres">
                                    <jdoc:include type="modules" name="navbaram" />

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="footer-widget instagram">
                                <h2>Proyectos</h2>
                                <ul class="list-inline">
                                    <li>
                                        <a href="images/project/2.jpg" class="image-link"><img class="img-responsive" src="images/project/2.jpg" alt="sample1"></a>
                                    </li>
                                    <li>
                                        <a href="images/project/8.jpg" class="image-link"><img class="img-responsive" src="images/project/8.jpg" alt="sample2"></a>
                                    </li>
                                    <li>
                                        <a href="images/project/7.jpg" class="image-link"><img class="img-responsive" src="images/project/7.jpg" alt="sampl3"></a>
                                    </li>
                                    <li>
                                        <a href="images/project/6.jpg" class="image-link"><img class="img-responsive" src="images/project/6.jpg" alt="sample4"></a>
                                    </li>
                                    <li>
                                        <a href="images/project/s2.jpg" class="image-link"><img class="img-responsive" src="images/project/s2.jpg" alt="sampl5"></a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="footer-bottom">
                <div class="container text-center">
                    <p>© Copyright 2016 
                        <span itemprop="name"><a href="#">Ani+</a>.</span> All Rights Reserved.
                        <br/>Design by <a target="_blank" href="http://solucionescrv.com/">CRV | SOLUCIONES SOFTWARE</a>
                    </p>
                </div>			
            </div>
        </footer>
    </span>
    <script type="application/ld+json">
        {
        "@context" : "http://schema.org",
        "@type" : "LocalBusiness",
        "name" : "Ani+.",
        "image" : "http://animasstudio.solucionescrv.com/templates/animasstudio/images/logo2.png",
        "telephone" : "(+57) 314672835",
        "email" : "animascoombia@gmail.com",
        "address" : {
        "@type" : "PostalAddress",
        "streetAddress" : "Carrera 10ª N° 1n 22, Barrio Modelo"
        }
        }
    </script>
</body>
</html>
