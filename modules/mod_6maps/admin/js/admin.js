/* Copyright @ Balbooa.com, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(window).load(function(){
	var widthWind = jQuery(window).height()-120;
	jQuery('#module-form').height(widthWind);

	jQuery('.form-horizontal').append("<div class='sidebar'></div>");
	
	jQuery(window).resize(function() {
		var widthWind = jQuery(window).height()-120;
	jQuery('#module-form').height(widthWind);
	});
});
jQuery(document).ready(function(){
	var cloneContent = jQuery('.form-inline.form-inline-header').clone();
	jQuery('.form-inline.form-inline-header').remove();
	jQuery('#general').prepend(cloneContent);
	jQuery( "#jform_params_module_background" ).change(function() {
		var backgroundIMG = jQuery('#jform_params_module_background').val();
		if ( backgroundIMG != "" ) {
			jQuery('.form-horizontal').css('background-image', 'url(../' + backgroundIMG + ')');
		}
	}).trigger( "change" );
});