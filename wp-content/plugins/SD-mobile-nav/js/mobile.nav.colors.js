jQuery(document).ready(function( $ ) {
	$('#sdrn_bar_bgd_picker').wpColorPicker({defaultColor:'#0D0D0D'});
	$('#sdrn_bar_color_picker').wpColorPicker({defaultColor:'#F2F2F2'});
	$('#sdrn_menu_bgd_picker').wpColorPicker({defaultColor:'#2E2E2E'});
	$('#sdrn_menu_color_picker').wpColorPicker({defaultColor:'#CFCFCF'});
	$('#sdrn_menu_color_hover_picker').wpColorPicker({defaultColor:'#606060'});
	$('#sdrn_menu_border_top_picker').wpColorPicker({defaultColor:'#474747'});
	$('#sdrn_menu_border_bottom_picker').wpColorPicker({defaultColor:'#131212'});
	$('#sdrn_searchbar_border_color_picker').wpColorPicker({defaultColor:'#696969'});
	$('#sdrn_searchbar_background_color_picker').wpColorPicker({defaultColor:'#565656'});

	$('#sdrn_m_icons').parent().parent().find('th').first().hide();
	$('[id^=sdrn_ii_]').wpColorPicker({defaultColor:'#F1F1F1'});
	$('[id^=sdrn_log_ii_]').wpColorPicker({defaultColor:'#F1F1F1'});
});
