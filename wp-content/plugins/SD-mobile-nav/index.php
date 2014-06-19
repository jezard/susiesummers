<?php
/*
Plugin Name: Mobile.Nav
Plugin URI: http://codecanyon.net/user/sommersethdesign
Description: Easy to setup and maintain mobile navigation for Wordpress
Version: 1.5.2.2
Author: Konrad Węgrzyniak
Author URI: http://codecanyon.net/user/sommersethdesign
Copyright: Sommerseth Design
*/



require('SDRN_plugin_update.php');
$current_version = '1.5.2.2';

add_action('init', 'sdrn_setup_autoupdate');  
function sdrn_setup_autoupdate() {  
    global $current_version;
    $update_script = 'http://updates.sommersethdesign.no/_node.php?p=e3fDE8e33ed2342e'; 
    new SDRN_plugin_update ($current_version, $update_script, plugin_basename(__FILE__));  
}




/**
 *
 * Enable translating
 *
 */
load_plugin_textdomain('sdrn', false, basename( dirname( __FILE__ ) ) . '/lang' );




/**
 *
 * Add admin settings
 *
 */
if(is_admin()) require dirname(__FILE__).'/admin/settings.php';




/*
 * Checks if Mobile.Nav can be added to the page
 * (checks if menu is enabled and has some WP menu set for logged in and/or not logged in users)
 */
function load_menu() {
	$options = get_option('sdrn_options');
	if($options['enabled']) {
		if(is_user_logged_in() && $options['custom_for_logged_in'] == 'yes') {
			//check if menu for logged in users provided
			if($options['logged_in_menu'] == 0) {
				return false;
			} else {
				return true;
			}
		} else {
			//check if menu for not logged in users provided
			if($options['menu'] == 0) {
				return false;
			} else {
				return true;
			}
		}
	} else {
		return false;
	}
}



/**
 * Loads all Mobile.Nav components
 */
function sdrn_init() {
	if(load_menu()) {
		add_action('wp_enqueue_scripts', 'sdrn_frontend_scripts_and_styles' );
		add_filter('body_class','sdrn_body_classes');
		add_action('wp_footer', 'sdrn_menu', 100);
		add_filter('nav_menu_link_attributes', 'sdrn_item_attributes', 10, 3 );
		add_action('wp_head', 'sdrn_header_styles');
	}
}
add_action('init', 'sdrn_init');





/**
 *
 * Add scripts and styles for the frontend
 *
 */
function sdrn_frontend_scripts_and_styles() {
	$options = get_option('sdrn_options');

	if(!wp_script_is('jquery')) wp_enqueue_script('jquery',false,array(),false, true);
		
	wp_enqueue_script('jquery.transit', plugins_url( '/js/jquery.transit.min.js', __FILE__ ),array(),false, true);
	if($options["swipe_actions"] == 'yes') {
		wp_enqueue_script('touchwipe', plugins_url( '/js/jquery.touchwipe.min.js', __FILE__ ),array(),false, true);
	}
	if($options["animation"] == 'sdrn_jquery') {
		wp_enqueue_script('sidr', plugins_url( '/js/jquery.sidr-ck.js', __FILE__ ), array( 'jquery' ),array(),false, true);
	}
	wp_enqueue_script('mobile.nav.frontend.js', plugins_url( '/js/min/mobile.nav.frontend-ck.js', __FILE__ ),array(),false, true);

	wp_register_style( 'mobile.nav.frontend.css', plugins_url('css/mobile.nav.frontend.css', __FILE__));
	wp_enqueue_style( 'mobile.nav.frontend.css' );
}
//if(load_menu()) add_action( 'wp_enqueue_scripts', 'sdrn_frontend_scripts_and_styles' );


/**
 *
 * Add scripts and styles for the backend
 *
 */
function sdrn_backend_scripts_and_styles($hook) {
	if($hook != 'settings_page_sdrn_settings') return;

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'mobile.nav.colors.js', plugins_url('js/mobile.nav.colors.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	wp_enqueue_script( 'mobile.nav.backend.js', plugins_url('js/mobile.nav.backend.js', __FILE__ ), array( 'jquery' ), false, true);
}
add_action( 'admin_enqueue_scripts', 'sdrn_backend_scripts_and_styles' );







/**
 * Adds the extra classes to the <body> 
 * @param  array $classes the default classes array
 * @return array          classes array with extra items
 */
function sdrn_body_classes($classes) {
	$options = get_option('sdrn_options');
	$classes[] = $options['animation'];
	$classes[] = 'sdrn_'.$options['position'];
	return $classes;
}
//if(load_menu()) add_filter('body_class','sdrn_body_classes');







/**
 * Main function
 * Adds the Mobile.Nav top bar & the menu to the website (wp_footer hook - menu is being added at the wery bottom of teh page)
 * @return HTML Mobile.Nav bar & menu	
 */
function sdrn_menu() {
	$options = get_option('sdrn_options');
	?>
	<div id="sdrn_bar" class="sdrn_bar" data-from_width="<?php echo $options['from_width'] ?>">
		<div class="sdrn_ic">
			<span class="sdrn_ic_1"></span>
			<span class="sdrn_ic_2"></span>
			<span class="sdrn_ic_3"></span>
		</div>
		<!--<span class="sdrn_icon sdrn_icon_menu" data-icon="m"></span>-->
		<span class="menu_title">
			<?php echo $options['bar_title'] ?>
			<?php if($options['bar_logo']) echo '<img class="bar_logo" src="'.$options['bar_logo'].'"/>' ?>
		</span>
	</div>

	<div id="sdrn_menu" class="sdrn_levels <?php echo $options['position'] ?> <?php echo $options['animation'] ?> <?php if($options["nesting_icon"] != '') echo 'sdrn_custom_icons'  ?>" data-custom_icon="<?php echo $options["nesting_icon"]  ?>" data-custom_icon_open="<?php echo $options["nesting_icon_open"]  ?>" data-zooming="<?php echo $options["zooming"] ?>" data-swipe_actions="<?php echo $options["swipe_actions"] ?>" data-how_wide="<?php echo $options["how_wide"] ?>" data-expand_sub_with_parent="<?php echo $options["expand_sub_with_parent"] ?>" >
		<ul id="sdrn_menu_ul">
			<?php
			//searchbar ?
			if($options['searchbar'] == 'yes') {
				?>
				<li class="menu-item menu-item-searchbar">
					<?php $input_val = $options['searchbar_label']; ?>
					<form role="search" method="get" id="sdrn_searchform" action="<?php echo home_url( '/' ); ?>">
						<input type="text" onfocus="if(this.value == '<?php echo $input_val; ?>') { this.value = ''; }" onblur="if(this.value == '') { this.value = '<?php echo $input_val; ?>'; }" value="<?php echo $input_val; ?>" name="s" id="sdrn_s" />
						<input type="submit" id="sdrn_searchsubmit"  value="" />
						<span class="sdrn_icon sdrn_searchsubmit_ic"></span>
					</form>
				</li>
				<?php
			}
			?>
			<?php
			//custom menu for logged in users
			$menu_setting = '';
			if($options['custom_for_logged_in'] == 'yes' && is_user_logged_in()) {
				$menu_setting = $options['logged_in_menu'];
			} else {
				$menu_setting = $options['menu'];
			}

			$menus = get_terms('nav_menu',array('hide_empty'=>false));
			if($menus) : foreach($menus as $m) :
				if($m->term_id == $menu_setting) $menu = $m;
			endforeach; endif;
			if(is_object($menu)) :
				wp_nav_menu( array('menu'=>$menu->name,'container'=>false,'items_wrap'=>'%3$s','link_before'=>'<div></div>','walker'=>new Walker_Nav_Menu));
			endif;
			?>
		</ul>
	</div>
	<?php
}
//if(load_menu()) add_action('wp_footer', 'sdrn_menu', 100);



/**
 * Filetr the menu items and apply custom attributes (for icons) those that have custom icons assigned in settings
 */
function sdrn_item_attributes($atts, $item, $args) {
	$options = get_option('sdrn_options');
	//check if witch icons should be used ( normal menu or for logged in menu)
	$icons = ($options['custom_for_logged_in'] == 'yes' && $options['logged_in_menu'] > 0 && is_user_logged_in())? $options['logmenu_icons_for_items'] : $options['icons_for_items'];
	if(count($icons >0)) {
		foreach ($icons as $item_set) {
			if(isset($item_set['item_id']) && isset($item_set['item_icon']) && isset($item_set['color']) && isset($item_set['icon_image'])) {
				if ($item_set['item_id'] == $item->ID) {
					//fontawesome icon
					if($item_set['item_icon'] != '' && $item_set['icon_image'] == '') {
						$atts['data-cii'] = true;
						$atts['data-fa_icon'] = $item_set['item_icon'];
						$atts['data-icon_color'] = $item_set['color'];
					}
					//image icon
					if($item_set['icon_image'] != '') {
						$atts['data-cii'] = true;
						$atts['data-icon_src'] = $item_set['icon_image'];
					}
				}
			}
		}
	}
    return $atts;
}
//if(load_menu()) add_filter('nav_menu_link_attributes', 'sdrn_item_attributes', 10, 3 );







/**
 * Adds the custom styling for the menu to website header
 * @return HTML - the <style> tag with extra styling based on user settings
 */
function sdrn_header_styles() {
	$options = get_option('sdrn_options');
	if($options['enabled']) :
		?>
		<style id="sdrn_css" type="text/css" >
			/* apply appearance settings */
			#sdrn_bar {
				background: <?php echo $options["bar_bgd"] ?>;
			}
			#sdrn_bar .menu_title, #sdrn_bar .sdrn_icon_menu {
				color: <?php echo $options["bar_color"] ?>;
			}
			#sdrn_bar .sdrn_ic span {
				background: <?php echo $options["bar_color"] ?>;
			}
			#sdrn_menu {
				background: <?php echo $options["menu_bgd"] ?>!important;
			}
			#sdrn_menu.sdrn_levels ul li {
				border-bottom:1px dotted <?php echo $options["menu_border_bottom"] ?>;
				border-top:1px solid<?php echo $options["menu_border_top"] ?>;
			}
			#sdrn_menu ul li a {
				color: <?php echo $options["menu_color"] ?>;
			}
			#sdrn_menu ul li a:hover {
				color: <?php echo $options["menu_color_hover"] ?>;
			}
			#sdrn_menu.sdrn_levels a.sdrn_parent_item {
				border-left:1px dotted <?php echo $options["menu_border_top"] ?>;
			}
			#sdrn_menu .sdrn_icon_par {
				color: <?php echo $options["menu_color"] ?>;
			}
			#sdrn_menu .sdrn_icon_par:hover {
				color: <?php echo $options["menu_color_hover"] ?>;
			}
			#sdrn_menu.sdrn_levels ul li ul {
				border-top:3px dotted <?php echo $options["menu_border_bottom"] ?>;
			}

			#sdrn_s {
				color: <?php echo $options["menu_color"] ?>;
				border:1px dotted <?php echo $options["searchbar_border_color"] ?>;
				background: <?php echo $options["searchbar_background_color"] ?>
			}

			#sdrn_searchsubmit {
				color: <?php echo $options["menu_color"] ?>;
			}

			#sdrn_menu  .sdrn_searchsubmit_ic:before {
				color: <?php echo $options["menu_color"] ?>;
			}

			<?php
			//when option "hide bottom borders is on...
			if($options["menu_border_bottom_show"] === 'no') { ?>
				#sdrn_menu, #sdrn_menu ul, #sdrn_menu li {
					border-bottom:none!important;
				}
				#sdrn_menu.sdrn_levels > ul {
					border-bottom:3px solid <?php echo $options["menu_border_top"] ?>!important;
				}
				.sdrn_no_border_bottom {
					border-bottom:none!important;
				}
				#sdrn_menu.sdrn_levels ul li ul {
					border-top:none!important;
				}
			<?php } ?>
	
			<?php if($options["animation"] == 'sdrn_jquery') : ?>
				#sdrn_menu.left {
					width:<?php echo $options["how_wide"] ?>%;
					left: -<?php echo $options["how_wide"] ?>%;
				    right: auto;
				}
			<?php endif ?>

			<?php if($options["animation"] == 'sdrn_jquery') : ?>
				#sdrn_menu.right {
					width:<?php echo $options["how_wide"] ?>%;
				    right: -<?php echo $options["how_wide"] ?>%;
				    left: auto;
				}
			<?php endif ?>

			<?php if($options["nesting_icon"] != '') : ?>
				#sdrn_menu .sdrn_icon:before {
					font-family: 'fontawesome'!important;
				}
				#sdrn_menu .sdrn_searchsubmit_ic:before {
  					font-family: 'sdrn'!important;
  				}
			<?php endif; ?>

			<?php if($options["menu_symbol_pos"] == 'right') : ?>
				#sdrn_bar .sdrn_ic {
					float: <?php echo $options["menu_symbol_pos"] ?>!important;
					margin-right:0px!important;
				}
				#sdrn_bar .bar_logo {
					pading-left: 0px;
				}
			<?php endif; ?>

			<?php 
			// Displaing menu based on Browser width
			?>
			<?php if($options["display_on"] == 'screen_width') : ?>
				/* show the bar and hide othere navigation elements */
				@media only screen and (max-width: <?php echo $options["from_width"] ?>px) {
					<?php if($options["animation"] == 'sdrn_jquery' || $options["position"] == 'top') : ?> html { padding-top: 42px!important; } <?php endif ?>
					<?php if($options["animation"] == 'sdrn_css3') : ?> #sdrn_wrapper { padding-top: 42px!important; } <?php endif ?>
					#sdrn_bar { display: block!important; }
					<?php
					if(count($options['hide']) > 0) {
						echo implode(', ', $options['hide']);
						echo ' { display:none!important; }';
					}
					?>
				}
				/* hide the bar & the menu */
				@media only screen and (min-width: <?php echo (int)$options["from_width"]+1 ?>px) {
				}
			<?php endif; ?>

			<?php 
			// Displaing menu ON SMARTPHONES
			if(isset($options["devices"]["smartphone"]) && $options["display_on"] == 'device_type') {
				$device = 'smartphone';
				if($options["animation"] == 'sdrn_jquery' || $options["position"] == 'top') {
					echo 'html.sdrn_'.$device.' { padding-top: 42px!important; }';
				}
				if($options["animation"] == 'sdrn_css3') {
					echo 'html.sdrn_'.$device.' #sdrn_wrapper { padding-top: 42px!important; }';
				}
				echo 'html.sdrn_'.$device.' #sdrn_bar { display: block!important; }';
				if(count($options['hide']) > 0) {
					foreach ($options['hide'] as $sel) {
						if($sel != '') echo 'html.sdrn_'.$device.' '.$sel.' { display:none!important; }';
					}
				}
			}
			?>

			<?php 
			// Displaing menu ON TABLETS
			if(isset($options["devices"]["tablet"]) && $options["display_on"] == 'device_type') {
				$device = 'tablet';
				if($options["animation"] == 'sdrn_jquery' || $options["position"] == 'top') {
					echo 'html.sdrn_'.$device.' { padding-top: 42px!important; }';
				}
				if($options["animation"] == 'sdrn_css3') {
					echo 'html.sdrn_'.$device.' #sdrn_wrapper { padding-top: 42px!important; }';
				}
				echo 'html.sdrn_'.$device.' #sdrn_bar { display: block!important; }';
				if(count($options['hide']) > 0) {
					foreach ($options['hide'] as $sel) {
						if($sel != '') echo 'html.sdrn_'.$device.' '.$sel.' { display:none!important; }';
					}
				}
			}
			?>

			<?php 
			// Displaing menu ON DESKTOPS
			if(isset($options["devices"]["desktop"]) && $options["display_on"] == 'device_type') {
				$device = 'desktop';
				if($options["animation"] == 'sdrn_jquery' || $options["position"] == 'top') {
					echo 'html.sdrn_'.$device.' { padding-top: 42px!important; }';
				}
				if($options["animation"] == 'sdrn_css3') {
					echo 'html.sdrn_'.$device.' #sdrn_wrapper { padding-top: 42px!important; }';
				}
				echo 'html.sdrn_'.$device.' #sdrn_bar { display: block!important; }';
				if(count($options['hide']) > 0) {
					foreach ($options['hide'] as $sel) {
						if($sel != '') echo 'html.sdrn_'.$device.' '.$sel.' { display:none!important; }';
					}
				}
			}
			?>


			<?php if($options["animation"] == 'sdrn_css3') : ?>
				#sdrn_wrapper {
				    position: relative!important;
				    z-index: 2;
				    -webkit-backface-visibility: hidden;
				    -moz-backface-visibility:    hidden;
				    -ms-backface-visibility:     hidden;
				    transition: -webkit-transform .3s ease;
				    transition: -moz-transform .3s ease;
				    transition: -o-transform .3s ease;
				    transition: -ms-transform .3s ease;
				    transition: transform .3s ease;
				    
				    -webkit-transform: translate3d(0px, 0, 0);
    				-moz-transform: translate3d(0px, 0, 0);
   					-o-transform: translate3d(0px, 0, 0);
    				-ms-transform: translate3d(0px, 0, 0);
    				transform: translate3d(0px, 0, 0);
				}
				#sdrn_menu {
					display: none;
				}
				#sdrn_menu.left.sdrn_css3 {
				    width: <?php echo $options["how_wide"] ?>%;
				    z-index: 1;
				}
				#sdrn_menu.right.sdrn_css3 {
				    width: <?php echo $options["how_wide"] ?>%;
				    z-index: 1;
				    right:0px;
				}
				body.sdrn_css3.menu_is_opened #sdrn_wrapper, .fixed_animation_moved  {
					overflow-x: hidden!important;
				    <?php if($options["position"] == 'left') : ?>
				    	-webkit-transform: translate3d(<?php echo $options["how_wide"] ?>%, 0, 0);
    					-moz-transform: translate3d(<?php echo $options["how_wide"] ?>%, 0, 0);
   						-o-transform: translate3d(<?php echo $options["how_wide"] ?>%, 0, 0);
    					-ms-transform: translate3d(<?php echo $options["how_wide"] ?>%, 0, 0);
    					transform: translate3d(<?php echo $options["how_wide"] ?>%, 0, 0);
				    <?php endif ?>
				    <?php if($options["position"] == 'right') : ?>
				    	-webkit-transform: translate3d(-<?php echo $options["how_wide"] ?>%, 0, 0);
    					-moz-transform: translate3d(-<?php echo $options["how_wide"] ?>%, 0, 0);
   						-o-transform: translate3d(-<?php echo $options["how_wide"] ?>%, 0, 0);
    					-ms-transform: translate3d(-<?php echo $options["how_wide"] ?>%, 0, 0);
    					transform: translate3d(-<?php echo $options["how_wide"] ?>%, 0, 0);
				    <?php endif ?>
				}
				body.sdrn_css3 {
					overflow-x:hidden!important;
				}
				<?php endif ?>


				<?php if(isset($options['font_awesome_css'])) echo $options['font_awesome_css'] ?>

		</style>
		<?php
	endif;
}
//if(load_menu()) add_action('wp_head', 'sdrn_header_styles');



