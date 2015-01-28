<?php defined( '_JEXEC' ) or die( 'Restricted access' );
// Remove Scripts
$doc = JFactory::getDocument();
unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-more.js']);
unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core.js']);
unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
unset($doc->_scripts[JURI::root(true) . '/media/system/js/modal.js']);
unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
$app = JFactory::getApplication();
$menu = $app->getMenu();
$isFrontPage = $menu->getActive() == $menu->getDefault();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/together/css/bootstrap.min.css" type="text/css" />
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/together/js/jquery-1.11.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/together/js/bootstrap.min.js"></script>
		<style type="text/css">
			body {
				position: relative;
				color: #1c1c1b;
			}
			.margen-derecho {
				margin-right: 0.5em;
			}
			#nav-wrapper.affix {
				top: 0;
				left: 0;
				width: 100%;
				z-index: 9999;
			}
			#nav > .navbar-inner {
				border-left: 0;
				border-right: 0;
				border-radius: 0;
				-webkit-border-radius: 0;
				-moz-border-radius: 0;
				-o-border-radius: 0;
			}
			.espacio-inferior {
				margin-bottom: 100px;
			}
			.espacio-superior {
				margin-top: 60px;
			}
			.shadow {
				padding: 1.5em;
				background: #000;
				position: absolute;
				bottom: 0;
				right: 0;
				left: 0;
				z-index: 10;
			}
			.shadow p, .shadow h1, .shadow h2, .shadow h3, .shadow h4 {
				opacity: 1 !important;
			}
			#head {
				background: url(<?php echo $this->baseurl; ?>/templates/together/img/imgtogether1400x900.png) 50% 0 fixed; 
				height: auto;  
				margin: 0 auto; 
				width: 100%; 
				position: relative; 
				box-shadow: 0 0 50px rgba(0,0,0,0.8);
				padding: 200px 0;
			}

			#nav {
				background-color: transparent !important;
				background-image: url(<?php echo $this->baseurl; ?>/templates/together/img/trans_menu.png);
				border-color: transparent !important;
			}
			
			#logo {
				width: 175px;
				height: 40px;
				margin: 0.3em 0.5em;
				background: url(<?php echo $this->baseurl; ?>/templates/together/img/logo.png) no-repeat center center;
				float: left;
			}

			.head {
				width: 535px;
				height: 175px;
				padding: 50px 25px 50px 10px;
				background: url(<?php echo $this->baseurl; ?>/templates/together/img/transptop635x175.png) no-repeat;
				color: #fff;
				font-weight: bolder;
				font-size: 1.75em;
			}
			.menu-destacados span.glyphicon, article.destacado a span.glyphicon {
				color: #F15A24;
			}
			.menu-destacados span.glyphicon {
				font-size: 3em;
			}
			article.destacado {
				padding-top: 60px;
			}
			article.destacado a h2 {
				color: #1c1c1b;
				font-weight: bolder;
				font-size: 3.5em;
			}
			
			.flecha_izquierda {
				width: 100%;
				height: 200px;
				padding: 1em 1em 1em 8em;
				background: url(<?php echo $this->baseurl; ?>/templates/together/img/flechatex790x200.png) no-repeat;
				background-position: right center;
				background-color: #F15A24;
				color: #fff;
				text-align: right;
			}
			
			.flecha_izquierda p {
				width: 700px;
				float: right;
			}
			
			.flecha_derecha {
				width: 100%;
				height: 200px;
				padding: 1em 8em 1em 1em;
				background: url(<?php echo $this->baseurl; ?>/templates/together/img/flechatex0790x200.png) no-repeat;
				background-position: left center;
				background-color: #F15A24;
				color: #fff;
			}
			
			.flecha_derecha p {
				width: 700px;
			}
			
			.fondo_naranja {
				background-color: #F15A24;
				padding: 1em;
				color: #fff;
			}
			
			.fondo_negro {
				padding: 1em;
				background-color: #000;
				color: #fff;
			}
			
			.fondo_negro a h2 {
				color: #fff !important;
			}
		</style>
		<script type="text/javascript">
		$(document).ready(function(){
			$('article').mouseenter(function(){
				$('.intro-text', $(this)).show('slow');
			})
			.mouseleave(function(){
				$('.intro-text', $(this)).hide('slow');
			});
   // cache the window object
   $window = $(window);
   $('section[data-type="background"]').each(function(){
     // declare the variable to affect the defined data-type
     var $scroll = $(this);
      $(window).scroll(function() {
        // HTML5 proves useful for helping with creating JS functions!
        // also, negative value because we're scrolling upwards                             
        var yPos = -($window.scrollTop() / $scroll.data('speed')); 
        // background position
        var coords = '50% '+ yPos + 'px'; 
        // move the background
        $scroll.css({ backgroundPosition: coords });    
      }); // end window scroll
   });  // end section function

		});
		</script>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body data-spy="scroll" data-target="#<?php if ($app->input->getString('option') != 'com_together') { ?>destacados<?php } else {?>bar-together<?php } ?>">
		<div id="inicio" class="container-fluid<?php if ($isFrontPage) { ?> espacio-inferior<?php } else { ?> espacio-superior<?php } ?>">
			<div class="row">
				<div class="col-md-12">
					<jdoc:include type="modules" name="menu" />
				</div>
			</div>
			<?php if ($isFrontPage) : ?>
			<section id="head" data-speed="4" data-type="background">
				<div class="row">
					<div class="col-md-offset-6 col-md-6">
						<div class="head">
							<jdoc:include type="modules" name="head" />
						</div>
					</div>
				</div>
			</section>
			<?php endif; ?>
			<?php if ($this->countModules( 'breadcrumbs' ) && !$isFrontPage) : ?>
			<div class="row">
				<div class="col-md-12">
					<jdoc:include type="modules" name="breadcrumbs" />
				</div>
			</div>
			<?php endif; ?>
			<?php if ($this->countModules( 'jumbotron' ) && $isFrontPage) : ?>
			<div id="mec3_carousel" class="carousel slide" data-ride="carousel">
				<ol class="carousel-indicators">
					<?php for ($i = 0; $i < $this->countModules( 'jumbotron' ); $i ++) : ?>
					<li data-target="#mec3_carousel" data-slide-to="<?php echo $i; ?>"<?php if ($i == 0) { ?> class="active" <?php } ?>></li>
					<?php endfor; ?>
				</ol>
				<div class="carousel-inner" role="listbox">
					<jdoc:include type="modules" name="jumbotron" style="slide" />
				</div>
				<a class="left carousel-control" href="#mec3_carousel" role="button" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				</a>
				<a class="right carousel-control" href="#mec3_carousel" role="button" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				</a>
			</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-12">
					<jdoc:include type="component" />
				</div>
			</div>
			<jdoc:include type="modules" name="footer" style="mapa" />
		</div>
		<jdoc:include type="modules" name="login" />
	</body>
</html>
