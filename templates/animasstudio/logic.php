<?php defined( '_JEXEC' ) or die;

// variables
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$active = $app->getMenu()->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/'.$this->template;
// generator tag
$this->setGenerator(null);

// template js
$doc->addScript($tpath.'/js/jquery.js');
$doc->addScript($tpath.'/js/isotope.pkgd.min.js');
$doc->addScript($tpath.'/js/jquery.countTo.js');
$doc->addScript($tpath.'/js/jquery.event.move.js');
$doc->addScript($tpath.'/js/jquery.inview.min.js');
$doc->addScript($tpath.'/js/jquery.magnific-popup.min.js');
$doc->addScript($tpath.'/js/jquery.nav.js');
$doc->addScript($tpath.'/js/jquery.parallax.js');
$doc->addScript($tpath.'/js/jquery.twentytwenty.js');
$doc->addScript($tpath.'/js/bootstrap.js');
$doc->addScript($tpath.'/js/jquery.typer.js');
$doc->addScript($tpath.'/js/main.js');

// template css
//$doc->addStyleSheet($tpath.'/css/templateanimas.css');
$doc->addStyleSheet($tpath.'/css/font-awesome.min.css');
$doc->addStyleSheet($tpath.'/css/magnific-popup.css');
$doc->addStyleSheet($tpath.'/css/main.css');
$doc->addStyleSheet($tpath.'/css/responsive.css');
$doc->addStyleSheet($tpath.'/css/tr-animation.css');
$doc->addStyleSheet($tpath.'/css/twentytwenty.css');
$doc->addStyleSheet($tpath.'/css/bootstrap.min.css');
