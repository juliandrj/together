<?php
/**
* @package   6maps
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$uniqid = $module->id;
$address = $params->get ('address','');
$height = $params->get ('height',300);
$width = $params->get ('width',500);
$map_type = $params->get ('map_type','ROADMAP');
$zoom = $params->get ('zoom',6);
$zoomControl = $params->get ('zoomControl','true');
$typeControl = $params->get ('mapTypeControl','true');
$scaleControl = $params->get ('scaleControl','true');
$streetViewControl = $params->get ('streetViewControl','true');
$panControl = $params->get ('panControl','true');
$overviewControl = $params->get ('overviewMapControl','true');
$rotateControl = $params->get ('rotateControl','true');
$infoWindowControl = $params->get ('infoWindowControl','false');
$changeColor = $params->get ('changeMapColor');
if ($changeColor=='true') {
	$mapColor = $params->get ('mapColor');
} else {
	$mapColor = '';
}

$image = trim($params->get('image'));
if ($image!='') {
	$sizeImage = array();
	$sizeImage = getimagesize($params->get('image'));
}

$markerParam =  '{';
if ($params->get('title')!='') {
	$markerParam .=  'title:\''.addslashes($params->get('title')).'\',';
}
if ($image!='') {
	$markerParam .=  'image:\''.($image!=''? JURI::root().$image:'').'\',';
}
$txt = json_encode($params->get('contentInfo'));
if ($txt!='null') {
	$markerParam .=  'contentInfo:'. $txt.',';
}
$markerParam .=  '};';

$doc = JFactory::getDocument();
$doc->addScript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');//Add map api script
$doc->addStyledeclaration("#map_canvas-" . $uniqid . " {margin:0;padding:0;height:" . $height . "px}");//Add inline stlesheet
require (JModuleHelper::getLayoutPath('mod_6maps'));//Load layout