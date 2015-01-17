<?php
function modChrome_slide ($module, &$params, &$attribs) {
	if ($module->showtitle) {
		echo '<div class="item">';
	} else {
		echo '<div class="item active">';
	}
	echo strip_tags($module->content, '<img>');
	echo '<div class="carousel-caption">';
	echo $module->title;
	echo '</div>';
	echo '</div>';
}

function modChrome_mapa ($module, &$params, &$attribs) {
	echo '<article id="mapa" class="destacado">';
	if ($module->showtitle) {
		echo '<a href="#mapa"><h2><span class="glyphicon ' . $params->get('moduleclass_sfx') . '"></span> ' . $module->title . '</h2></a>';
	}
	echo '<div class="center-block text-center">' . $module->content . '</div>';
	echo '</article>';
}
