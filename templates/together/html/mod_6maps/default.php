<?php defined('_JEXEC') or die('Restricted access');
//$doc->addStyleSheet(JURI::root() . 'modules/mod_6maps/admin/css/style.css'); ?>
<script type="text/javascript">
var maps<?php echo $uniqid;?>;
var marker<?php echo $uniqid;?> = null;
var markerParam<?php echo $uniqid;?> = <?php echo $markerParam;?>
<?php if ($image!='') { ?>
	var image<?php echo $uniqid;?> = new google.maps.MarkerImage(
		markerParam<?php echo $uniqid;?>.image,
		new google.maps.Size(<?php echo $sizeImage[0];?>,<?php echo $sizeImage[1];?>),
		new google.maps.Point(0,0),
		new google.maps.Point(<?php echo $sizeImage[0]/2?>,<?php echo $sizeImage[1];?>));
<?php }?>
setTimeout(function() 
{
	location_address<?php echo $uniqid;?>();
},5);    
// Create an array of styles.
var styles<?php echo $uniqid;?> = 
[
	{
		stylers:
		[
			{ hue: "<?php echo $mapColor;?>" },
			{ saturation: -20 }
		]
	},
	{
		featureType: "road.local",
		stylers:
		[
			{ color: "<?php echo $mapColor;?>" },
			{ lightness: 100 },
			{ visibility: "simplified" }
		]
	},
	{
		featureType: "road",
		elementType: "labels",
	}
];

function location_address<?php echo $uniqid;?>() 
{
	var geocoder = new google.maps.Geocoder();
	myOptions = 
		{
		zoom:
		<?php if ($zoom) {
			echo $zoom;
		} else {
			echo 1;
		}?>,
		panControl:<?php echo $panControl?>,
		zoomControl:<?php echo $zoomControl?>,
		mapTypeControl:<?php echo $typeControl?>,
		scaleControl:<?php echo $scaleControl?>,
		streetViewControl:<?php echo $streetViewControl?>,
		overviewMapControl:<?php echo $overviewControl?>,
		rotateControl:<?php echo $rotateControl?>,
		mapTypeId: google.maps.MapTypeId.<?php echo $map_type?>
		}
	maps<?php echo $uniqid;?> = new google.maps.Map(document.getElementById("<?php echo "map_canvas-" . $uniqid;?>"), myOptions);
	maps<?php echo $uniqid;?>.setOptions({styles: styles<?php echo $uniqid;?>});
	var address = '<?php echo $address; ?>';
	geocoder.geocode( { 'address': address}, function(results, status)
					 {
						if (status == google.maps.GeocoderStatus.OK) {
      						maps<?php echo $uniqid;?>.setCenter(results[0].geometry.location);
							if (marker<?php echo $uniqid;?>) marker<?php echo $uniqid;?>.setMap(null);
							marker<?php echo $uniqid;?> = new google.maps.Marker({
								title: markerParam<?php echo $uniqid;?>.title,
								<?php if ($image!='') { ?>
									icon:image<?php echo $uniqid;?>,
								<?php }?>
								map: maps<?php echo $uniqid;?>,
								position: results[0].geometry.location,
								draggable: false,
								animation: google.maps.Animation.DROP
							});
							if (markerParam<?php echo $uniqid;?>.contentInfo) {
								var infowindow = new google.maps.InfoWindow({
									content: markerParam<?php echo $uniqid;?>.contentInfo
								});
								if (markerParam<?php echo $uniqid;?>.showContentOnload) {
									infowindow.open(maps<?php echo $uniqid;?>,marker<?php echo $uniqid;?>);
								}
								<?php if($infoWindowControl=='true'){?>
									infowindow.open(maps<?php echo $uniqid;?>,marker<?php echo $uniqid;?>);
									google.maps.event.addListener(marker<?php echo $uniqid;?>, 'click', function()
									{
										infowindow.open(maps<?php echo $uniqid;?>,marker<?php echo $uniqid;?>);
									});
								<?php } else { ?>
									google.maps.event.addListener(marker<?php echo $uniqid;?>, 'click', function()
									{
										infowindow.open(maps<?php echo $uniqid;?>,marker<?php echo $uniqid;?>);
									});
								<?php  } ?>
							}
						} else {
							alert("Please check the accuracy of Address");
						}
					});
}                                         

/*jQuery(document).ready(function() 
{
	var maps = <?php echo $width ?>;
	var mapsParent = jQuery('#<?php echo "map_canvas-" . $uniqid;?>').parent().width();
	if ( maps >= mapsParent ) {
		jQuery('#<?php echo "map_canvas-" . $uniqid;?>').width(mapsParent);
	} else {
		jQuery('#<?php echo "map_canvas-" . $uniqid;?>').width(maps);
	}
	jQuery(window).resize(function() 
  	{
		var maps = <?php echo $width ?>;
		var mapsParent = jQuery('#<?php echo "map_canvas-" . $uniqid;?>').parent().width();
		if ( maps >= mapsParent ) {
			jQuery('#<?php echo "map_canvas-" . $uniqid;?>').width(mapsParent);
		} else {
			jQuery('#<?php echo "map_canvas-" . $uniqid;?>').width(maps);
		}
	});
});*/
</script>               
                        
              
<?php
if ($address) {
?>
	<div id="<?php echo "map_canvas-" . $uniqid;?>" class="mod6map" style="width: 100%;"></div>
<?php } else { ?>
	<p>Please provide the address value.</p>
<?php }