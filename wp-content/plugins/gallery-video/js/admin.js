jQuery(document).ready(function () {
	jQuery('.save-videogallery-options').click(function(){
		alert("General Settings are disabled in free version. If you need those functionalityes, you need to buy the commercial version.");
	return false;
	});
	jQuery('#arrows-type input[name="params[slider_navigation_type]"]').change(function(){
		jQuery(this).parents('ul').find('li.active').removeClass('active');
		jQuery(this).parents('li').addClass('active');
	});
	jQuery('input[data-videogallery="true"]').bind("videogallery:changed", function (event, data) {
		 jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
		 jQuery(this).val(parseInt(data.value));
	});
	jQuery('#videogallery-view-tabs li a').click(function(){
		jQuery('#videogallery-view-tabs > li').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('#videogallery-view-tabs-contents > li').removeClass('active');
		var liID=jQuery(this).attr('href').replace('#','');
		jQuery('#videogallery-view-tabs-contents > li[data-id="'+liID+'"').addClass('active');
		jQuery('#adminForm').attr('action',"admin.php?page=Options_videogallery_styles&task=save#"+liID);
	});
	jQuery('#huge_it_sl_effects').change(function(){
		jQuery('.videogallery-current-options').removeClass('active');
		jQuery('#videogallery-current-options-'+jQuery(this).val()).addClass('active');
	});
});