<?php
/**
 * Mobile.Nav Settings page in WP admin
 */




/**
 *
 * Default settings
 *
 */
$sdrn_setup = array(
    'enabled' => 1,
    'menu' => '',
    'custom_for_logged_in' => 'no',
    'logged_in_menu' => '',
    'menu_symbol_pos' => 'left',
    'bar_title' => 'MENU',
    'nesting_icon' => '',
    'nesting_icon_open' => '',
    'expand_sub_with_parent' => 'no',
    'display_on' => 'screen_width',
    'from_width' => 961,
    'devices' => array(),
    'position' => 'top',
    'how_wide' => '80',
    'swipe_actions' => 'yes',
    'hide' => array(),
    'zooming' => 'no',
    'animation' => 'sdrn_jquery',
    'searchbar' => 'no',
    'searchbar_label' => 'Search',
    'bar_bgd' => '#0D0D0D',
    'bar_color' => '#F2F2F2',
    'menu_bgd' => '#2E2E2E',
    'menu_color' => '#CFCFCF',
    'menu_color_hover' => '#606060',
    'menu_border_top' => '#474747',
    'menu_border_bottom' => '#131212',
    'menu_border_bottom_show' => 'yes',
    'icons_for_items' => array(),
    'logmenu_icons_for_items' => array(),
    'searchbar_border_color' => '#696969',
    'searchbar_background_color' => '#565656',
    'font_awesome_css' => ''
);








if(!get_option('sdrn_options')) {
    /**
     *
     * Save the default settings if not present
     *
     */
    add_option('sdrn_options', $sdrn_setup);
} else {
    /**
     * Updating options for previous releases
     */
    $sdrn_setup = get_option('sdrn_options');
    if(!isset($sdrn_setup['swipe_actions'])) $sdrn_setup['swipe_actions'] = 'yes';
    if(!isset($sdrn_setup['expand_sub_with_parent'])) $sdrn_setup['expand_sub_with_parent'] = 'no';
    if(!isset($sdrn_setup['animation'])) $sdrn_setup['animation'] = 'sdrn_jquery';
    if(!isset($sdrn_setup['searchbar'])) $sdrn_setup['searchbar'] = 'no';
    if(!isset($sdrn_setup['searchbar_label'])) $sdrn_setup['searchbar_label'] = 'Search';
    if(!isset($sdrn_setup['custom_for_logged_in'])) $sdrn_setup['custom_for_logged_in'] = 'no';
    if(!isset($sdrn_setup['logged_in_menu'])) $sdrn_setup['logged_in_menu'] = 'no';
    if(!isset($sdrn_setup['icons_for_items'])) $sdrn_setup['icons_for_items'] = array();
    if(!isset($sdrn_setup['logmenu_icons_for_items'])) $sdrn_setup['logmenu_icons_for_items'] = array();
    if(!isset($sdrn_setup['searchbar_border_color'])) $sdrn_setup['searchbar_border_color'] = '#696969';
    if(!isset($sdrn_setup['searchbar_background_color'])) $sdrn_setup['searchbar_background_color'] = '#565656';
    if(!isset($sdrn_setup['display_on'])) $sdrn_setup['display_on'] = 'screen_width';
    if(!isset($sdrn_setup['devices'])) $sdrn_setup['devices'] = array();
    if(!isset($sdrn_setup['font_awesome_css'])) $sdrn_setup['font_awesome_css'] = '';

    update_option('sdrn_options', $sdrn_setup);
}








/**
 *
 * Add settings page menu item
 *
 */
if ( is_admin() ){
    /**
     * action name
     * function that will create the menu page link / options page
     */
    add_action( 'admin_menu', 'sdrn_admin_menu' );
}



/**
 *
 * Add plugin settings page
 *
 */
function sdrn_admin_menu(){
    /**
     * menu title
     * page title
     * who can acces the settings  - user that can ...
     * the settings page identifier for the url
     * function that will generate the form with th esettings
     */
    add_options_page(__('Mobile.Nav','sdrn'),__('Mobile.Nav','sdrn'),'manage_options','sdrn_settings','sdrn_settings');
}



function sdrn_add_admin_scripts() {
    if ( 'settings_page_sdrn_settings' == get_current_screen()->id ) {
        if(function_exists( 'wp_enqueue_media' )){
            wp_enqueue_media();
        }else{
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }
    }
}
add_action('admin_enqueue_scripts', 'sdrn_add_admin_scripts');








/**
 *
 * Create the tabs for the settings page
 * @param  string $current default  tab
 * @return HTML          The tab switcher
 *
 */
function sdrn_settings_tabs( $current = 'general' ) {
    $tabs = array( 'general' => __('General','sdrn'), 'appearance' => __('Appearance','sdrn'));
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=sdrn_settings&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}







/**
 *
 * The settings wrappers
 * one for 'general' and 'emails' tabs
 * one for subscribers list
 *
 */
function sdrn_settings() {
    ?>
    <div class="wrap">
        <br>
        <br>
        <img src="<?php echo plugins_url( 'mobile-nav-wp-setting-logo.png' , __FILE__ ) ?>"/>
        <br>
        <br>
        <?php ( isset($_GET['tab']) )? sdrn_settings_tabs($_GET['tab']) : sdrn_settings_tabs('general'); ?>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('sdrn_options');
            do_settings_sections('sdrn_plugin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}






/**
 *
 * Initialize the settings
 *
 */
if ( is_admin() ) {
    /**
     * action name
     * function that will do all the initialization
     */
    add_action('admin_init', 'sdrn_admin_init');
}






/**
 *
 * Settings sections and fields setup
 *
 */
function sdrn_admin_init(){
    register_setting( 'sdrn_options', 'sdrn_options', 'sdrn_options_validate' );
    //
    if(!isset($_GET['tab']) || $_GET['tab'] == 'general') {
        add_settings_section('sdrn_general_settings', '<br>'.__('General settings','sdrn'), 'sdrn_general_settings_section', 'sdrn_plugin');
        //
        add_settings_field('sdrn_enabled', __('Enable mobile navigation','sdrn'), 'sdrn_general_settings_enabled', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_menu', __('Choose the wordpress menu','sdrn'), 'sdrn_general_settings_menu', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('custom_for_logged_in', __('Custom menu for logged in users?','sdrn'), 'sdrn_general_settings_custom_for_logged_in', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('logged_in_menu', __('Choose the wordpress menu for logged in users','sdrn'), 'sdrn_general_settings_logged_in_menu', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_menu_symbol_pos', __('Menu symbol position (on the top menu bar)','sdrn'), 'sdrn_general_settings_menu_symbol_pos', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_bar_title', __('Display text for the top menu bar','sdrn'), 'sdrn_general_settings_bar_title', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_bar_logo', __('Choose optional logo image for the top menu bar','sdrn'), 'sdrn_general_settings_bar_logo', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_nesting_icon', __('Optional custom submenus icon.<br>Uses icons from <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">font awesome</a>.<br/>Pick Your icon <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">here</a> and input the icon sumbol like <strong>fa-plus</strong>.<br/>Leave empty for a default icon','sdrn'), 'sdrn_general_settings_nesting_icon', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_nesting_icon_opened', __('Optional custom font awesome icon for opened submenu','sdrn'), 'sdrn_general_settings_nesting_icon_open', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_expand_sub_with_parent', __('Expand submenu by clicking parent item','sdrn'), 'sdrn_general_settings_expand_sub_with_parent', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_display_on', __('Display menu based on browser width or device type','sdrn'), 'sdrn_general_settings_display_on', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_from_width', __('Display menu from width (below in pixels)','sdrn'), 'sdrn_general_settings_from_width', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_devices', __('Display menu on following devices','sdrn'), 'sdrn_general_settings_devices', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_position', __('Menu position','sdrn'), 'sdrn_general_settings_position', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_how_wide', __('Width of the open menu (only for LEFT position - % of total page width)','sdrn'), 'sdrn_general_settings_how_wide', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('swipe_actions', __('Enable the "swipe to open/close" for touch screens?','sdrn'), 'sdrn_general_settings_swipe_actions', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_hide', __('Hide other navigation elements. CSS selectors (IDs or classes, coma separated)','sdrn').'<br>'.__('CSS sellectors (IDs and classes coma separated)','sdrn'), 'sdrn_general_settings_hide', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_zooming', __('Allow zooming on mobile devices?','sdrn'), 'sdrn_general_settings_zooming', 'sdrn_plugin', 'sdrn_general_settings');
        //
        add_settings_field('sdrn_animation', __('Animate menu with','sdrn'), 'sdrn_general_settings_animation', 'sdrn_plugin', 'sdrn_general_settings');   
        //
        add_settings_field('sdrn_searchbar', __('Add search bar to the menu?','sdrn'), 'sdrn_general_settings_searchbar', 'sdrn_plugin', 'sdrn_general_settings');   
        //
        add_settings_field('sdrn_searchbar_label', __('Search bar label','sdrn'), 'sdrn_general_settings_searchbar_label', 'sdrn_plugin', 'sdrn_general_settings');   
    }
    //
    if(isset($_GET['tab']) && $_GET['tab'] == 'appearance') {
        add_settings_section('sdrn_appearance_settings', '<br>'.__('Menu appearance','sdrn'), 'sdrn_appearance_settings_section', 'sdrn_plugin');
        //
        add_settings_field('sdrn_bar_bgd', __('Menu top bar background color','sdrn'), 'sdrn_appearance_settings_bar_bgd', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_bar_color', __('Menu top bar text color','sdrn'), 'sdrn_appearance_settings_bar_color', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_bgd', __('Menu background color','sdrn'), 'sdrn_appearance_settings_menu_bgd', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_color', __('Menu text color','sdrn'), 'sdrn_appearance_settings_menu_color', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_color_hover', __('Menu text color on hover','sdrn'), 'sdrn_appearance_settings_menu_color_hover', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_border_top', __('Menu borders color (top & left)','sdrn'), 'sdrn_appearance_settings_menu_border_top', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_border_bottom', __('Menu borders color (bottom)','sdrn'), 'sdrn_appearance_settings_menu_border_bottom', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_border_bottom_show', __('Enable/disable bottom border on menu list items','sdrn'), 'sdrn_appearance_settings_menu_border_bottom_show', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_searchbar_border_color', __('Search bar border color','sdrn'), 'sdrn_appearance_settings_searchbar_border_color', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_field('sdrn_menu_searchbar_background_color', __('Search bar background color','sdrn'), 'sdrn_appearance_settings_searchbar_background_color', 'sdrn_plugin', 'sdrn_appearance_settings');
        //
        add_settings_section('sdrn_appearance_settings_icons', '<br>'.__('Custom icons for menu items','sdrn'), 'sdrn_appearance_settings_section_icons', 'sdrn_plugin');
        //
        add_settings_field('sdrn_menu_icons_for_links', __('Apply icons to the menu','sdrn'), 'sdrn_appearance_settings_menu_icons', 'sdrn_plugin', 'sdrn_appearance_settings_icons');
    }
}


function sdrn_general_settings_section() {

}


function sdrn_general_settings_enabled() {
    $options = get_option('sdrn_options');
    ?>
    <label for="sdrn_enabled">
        <input name="sdrn_options[enabled]" type="checkbox" id="sdrn_enabled" value="1" <?php if($options['enabled']) echo 'checked="checked"' ?>>
        <?php ' '._e('Enabled','sdrn'); ?>
    </label>
    <?php
}


function sdrn_general_settings_menu() {
    $options = get_option('sdrn_options');
    $menus = get_terms('nav_menu',array('hide_empty'=>false));
    ?>
    <select name="sdrn_options[menu]" >
        <option <?php if($options['menu'] == 0) echo 'selected="selected"'; ?>  value="0">Choose the menu</option>
        <?php foreach( $menus as $m ): ?>
            <option <?php if($m->term_id == $options['menu']) echo 'selected="selected"'; ?>  value="<?php echo $m->term_id ?>"><?php echo $m->name ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}


function sdrn_general_settings_custom_for_logged_in() {
    $options = get_option('sdrn_options');
    ?>
    <select id="custom_for_logged_in" name="sdrn_options[custom_for_logged_in]" >
        <option <?php if($options['custom_for_logged_in'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes</option>
        <option <?php if($options['custom_for_logged_in'] == 'no') echo 'selected="selected"'; ?>  value="no">No</option>
    </select>
    <?php
}


function sdrn_general_settings_logged_in_menu() {
    $options = get_option('sdrn_options');
    $menus = get_terms('nav_menu',array('hide_empty'=>false));
    ?>
    <select id="logged_in_menu" name="sdrn_options[logged_in_menu]" >
        <option <?php if($options['menu'] == 0) echo 'selected="selected"'; ?>  value="0">Choose the menu</option>
        <?php foreach( $menus as $m ): ?>
            <option <?php if($m->term_id == $options['logged_in_menu']) echo 'selected="selected"'; ?>  value="<?php echo $m->term_id ?>"><?php echo $m->name ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}


function sdrn_general_settings_menu_symbol_pos() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_menu_symbol_pos" name="sdrn_options[menu_symbol_pos]" >
        <option <?php if($options['menu_symbol_pos'] == 'left') echo 'selected="selected"'; ?>  value="left">left</option>
        <option <?php if($options['menu_symbol_pos'] == 'right') echo 'selected="selected"'; ?>  value="right">right</option>
    </select>
    <?php
}

function sdrn_general_settings_bar_title() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_bar_title" name="sdrn_options[bar_title]"  size="20" type="text" value="<?php echo $options['bar_title'] ?>" />
    <?php
}

function sdrn_general_settings_bar_logo() {
    $options = get_option('sdrn_options');
    ?>
    <input type="hidden" name="sdrn_options[bar_logo]" class="sdrn_bar_logo_url" value="<?php echo $options['bar_logo'] ?>">
    <span style="position:relative">
        <img style="<?php if(!$options['bar_logo']) echo 'display:none; ' ?> width:auto; height:20px; margin-bottom:-6px; margin-right:6px;" class="sdrn_bar_logo_prev" src="<?php echo $options['bar_logo'] ?>" alt="">
    </span>
    <input id="upload_bar_logo_button" type="button" class="button" value="Choose image" />
    <span class="description"><?php if(isset($options['bar_logo'])) echo ' <a class="sdrn_disc_bar_logo" href="#" style="margin-left:10px;"> Discard the image (disable logo)</a>'; ?></span>
    <?php
}


function sdrn_general_settings_nesting_icon() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_nesting_icon" name="sdrn_options[nesting_icon]"  size="20" type="text" value="<?php echo $options['nesting_icon'] ?>" />
    <?php
}

function sdrn_general_settings_nesting_icon_open() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_nesting_icon_open" name="sdrn_options[nesting_icon_open]"  size="20" type="text" value="<?php echo $options['nesting_icon_open'] ?>" />
    <?php
}

function sdrn_general_settings_expand_sub_with_parent() {
    $options = get_option('sdrn_options');
    ?>
    <select id="expand_sub_with_parent" name="sdrn_options[expand_sub_with_parent]" >
        <option <?php if($options['expand_sub_with_parent'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes</option>
        <option <?php if($options['expand_sub_with_parent'] == 'no') echo 'selected="selected"'; ?>  value="no">No</option>
    </select>
    <?php
}

function sdrn_general_settings_display_on() {
    $options = get_option('sdrn_options');
    ?>
    <select id="display_on" name="sdrn_options[display_on]" >
        <option <?php if($options['display_on'] == 'screen_width') echo 'selected="selected"'; ?>  value="screen_width">Browser width</option>
        <option <?php if($options['display_on'] == 'device_type') echo 'selected="selected"'; ?>  value="device_type">Device type (desktop, tablet, smartphone)</option>
    </select>
    <?php
}

function sdrn_general_settings_from_width() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_from_width" name="sdrn_options[from_width]" min="280" max="2500" size="20" type="number" value="<?php echo $options['from_width'] ?>" />
    <?php
}

function sdrn_general_settings_devices() {
    $options = get_option('sdrn_options');
    $devices = $options['devices'];
    ?>
    <div class="sdrn_devices">
        <label for="sdrn_smartphones" style="margin-bottom:6px; display:inline-block;">
            <input name="sdrn_options[devices][smartphone]" type="checkbox" id="sdrn_smartphones" value="1" <?php if(isset($devices['smartphone'])) echo 'checked="checked"' ?>>
            <?php ' '._e('Smartphones','sdrn'); ?>
        </label> 
        <br>
        <label for="sdrn_tablets" style="margin-bottom:6px; display:inline-block;">
            <input name="sdrn_options[devices][tablet]" type="checkbox" id="sdrn_tablets" value="1" <?php if(isset($devices['tablet'])) echo 'checked="checked"' ?>>
            <?php ' '._e('Tablets','sdrn'); ?>
        </label> 
        <br>
        <label for="sdrn_desktops" style="margin-bottom:6px; display:inline-block;">
            <input name="sdrn_options[devices][desktop]" type="checkbox" id="sdrn_desktops" value="1" <?php if(isset($devices['desktop'])) echo 'checked="checked"' ?>>
            <?php ' '._e('Desktops','sdrn'); ?>
        </label> 
    </div>
    <?php
}



function sdrn_general_settings_position() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_menu_pos" name="sdrn_options[position]" >
        <option <?php if($options['position'] == 'top') echo 'selected="selected"'; ?>  value="top">top</option>
        <option <?php if($options['position'] == 'left') echo 'selected="selected"'; ?>  value="left">left</option>
        <option <?php if($options['position'] == 'right') echo 'selected="selected"'; ?>  value="right">right</option>
    </select>
    <?php
}


function sdrn_general_settings_how_wide() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_how_wide" name="sdrn_options[how_wide]" min="30" max="100" size="20" type="number" value="<?php echo $options['how_wide'] ?>" />
    <?php
}


function sdrn_general_settings_swipe_actions() {
    $options = get_option('sdrn_options');
    ?>
    <select id="swipe_actions" name="sdrn_options[swipe_actions]" >
        <option <?php if($options['swipe_actions'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes</option>
        <option <?php if($options['swipe_actions'] == 'no') echo 'selected="selected"'; ?>  value="no">No</option>
    </select>
    <?php
}


function sdrn_general_settings_hide() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_hide" name="sdrn_options[hide]"  size="60" type="text" value="<?php echo implode(', ',$options['hide']) ?>" />
    <br><i>Example:<br/> #main_menu, .custom_menu</i>
    <?php
}


function sdrn_general_settings_zooming() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_zooming" name="sdrn_options[zooming]" >
        <option <?php if($options['zooming'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes</option>
        <option <?php if($options['zooming'] == 'no') echo 'selected="selected"'; ?>  value="no">No</option>
    </select>
    <?php
}


function sdrn_general_settings_animation() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_animation" name="sdrn_options[animation]" >
        <option <?php if($options['animation'] == 'sdrn_jquery') echo 'selected="selected"'; ?>  value="sdrn_jquery">jQuery</option>
        <option <?php if($options['animation'] == 'sdrn_css3') echo 'selected="selected"'; ?>  value="sdrn_css3">css3</option>
    </select>
    <?php
}


function sdrn_general_settings_searchbar() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_searchbar" name="sdrn_options[searchbar]" >
        <option <?php if($options['searchbar'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes</option>
        <option <?php if($options['searchbar'] == 'no') echo 'selected="selected"'; ?>  value="no">no</option>
    </select>
    <?php
}


function sdrn_general_settings_searchbar_label() {
    $options = get_option('sdrn_options');
    ?>
    <input id="sdrn_searchbar_label" name="sdrn_options[searchbar_label]" type="text" value="<?php echo $options['searchbar_label'] ?>" />
    <?php
}











function sdrn_appearance_settings_section() {

}


function sdrn_appearance_settings_bar_bgd() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[bar_bgd]" id="sdrn_bar_bgd_picker"  value="<?php echo $options['bar_bgd']; ?>" />
    <?php
}


function sdrn_appearance_settings_bar_color() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[bar_color]" id="sdrn_bar_color_picker"  value="<?php echo $options['bar_color']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_bgd() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[menu_bgd]" id="sdrn_menu_bgd_picker"  value="<?php echo $options['menu_bgd']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_color() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[menu_color]" id="sdrn_menu_color_picker"  value="<?php echo $options['menu_color']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_color_hover() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[menu_color_hover]" id="sdrn_menu_color_hover_picker"  value="<?php echo $options['menu_color_hover']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_border_top() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[menu_border_top]" id="sdrn_menu_border_top_picker"  value="<?php echo $options['menu_border_top']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_border_bottom() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[menu_border_bottom]" id="sdrn_menu_border_bottom_picker"  value="<?php echo $options['menu_border_bottom']; ?>" />
    <?php
}


function sdrn_appearance_settings_menu_border_bottom_show() {
    $options = get_option('sdrn_options');
    ?>
    <select id="sdmn_menu_border_bottom_show" name="sdrn_options[menu_border_bottom_show]" >
        <option <?php if($options['menu_border_bottom_show'] == 'yes') echo 'selected="selected"'; ?>  value="yes">Yes - show bevel border</option>
        <option <?php if($options['menu_border_bottom_show'] == 'no') echo 'selected="selected"'; ?>  value="no">No - hide bevel border</option>
    </select>
    <?php
}



function sdrn_appearance_settings_searchbar_border_color() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[searchbar_border_color]" id="sdrn_searchbar_border_color_picker"  value="<?php echo $options['searchbar_border_color']; ?>" />
    <?php
}


function sdrn_appearance_settings_searchbar_background_color() {
    $options = get_option('sdrn_options');
    ?>
    <input maxlength="7" size="5" type="text" name="sdrn_options[searchbar_background_color]" id="sdrn_searchbar_background_color_picker"  value="<?php echo $options['searchbar_background_color']; ?>" />
    <?php
}






function sdrn_appearance_settings_section_icons() {
    ?>
    <p>
        Mobile.Nav allows for applying custom icons to menu items (links) in the Mobile.Nav menu. <br>
        You can choose from over 360 icons provided by  <a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">font awesome</a> (v4.0.3) or upload your own icon image (23px/23px).<br>
        To apply a custom icon from fontawesome first select it from <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank" >icon library</a> and input the icon sumbol (ex. "<strong>fa-plus</strong>") to appropriet input bellow (leave empty for no icon).<br>
        If icon image is set the fontawesome icon code will be ignored.
    </p>
    <?php
}


function sdrn_appearance_settings_menu_icons() {
    $options = get_option('sdrn_options');
    $logged_in_menu_icons = ($options['custom_for_logged_in'] == 'yes' && $options['logged_in_menu'] > 0)? true : false;
    ?>
    <table id="sdrn_m_icons">
        <thead>
            <tr>
                <th class="imith">Menu item</th>
                <th class="icth">Icon code</th>
                <th class="iccl">Icon color</th>
                <th>Icon image</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $menus = get_terms('nav_menu',array('hide_empty'=>false));
        $menu = false;
        if($menus) : foreach($menus as $m) :
            if($m->term_id == $options['menu']) $menu = $m;
        endforeach; endif;
        if(is_object($menu)) :
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            $i=0;foreach ($menu_items as $item) {
                $icon_settings = get_item_icon_settings($options['icons_for_items'], $item->ID);
                ?>
                <tr class="sdrn_ii_li">
                    <td>
                        <?php sdrn_item_indentation($item, $menu_items) ?>
                        <input type="hidden" name="sdrn_ii[<?php echo $i ?>][item_id]" value="<?php echo $item->ID ?>">
                    </td>
                    <td>
                        <input type="text" name="sdrn_ii[<?php echo $i ?>][item_icon]" value="<?php echo $icon_settings['item_icon'] ?>">
                    </td>
                    <td>
                        <input maxlength="7" size="5" type="text" name="sdrn_ii[<?php echo $i ?>][color]" id="sdrn_ii_<?php echo $i ?>"  value="<?php echo $icon_settings['color'] ?>" />
                    </td>
                    <td>
                        <input type="hidden" name="sdrn_ii[<?php echo $i ?>][icon_image]" class="cii_image_input" value="<?php echo $icon_settings['icon_image']; ?>">
                        <span style="position:relative">
                            <?php
                            $maybe_hide = 'display: none; ';
                            $src = '';
                            if(isset($icon_settings['icon_image'])) {
                                if(!$icon_settings['icon_image'] == '') {
                                    $maybe_hide = '';
                                    $src = $icon_settings['icon_image'];
                                } 
                            }                            
                            ?>
                            <img style="<?php echo $maybe_hide ?> width:23px; height:23px; margin-bottom:-6px; margin-right:10px;" class="cii_image_prev" src="<?php echo $src ?>" alt="">
                        </span>
                        <input type="button" class="button cii_select_file" value="Choose image" />
                        <span class="description">
                            <?php if(isset($icon_settings['icon_image'])) {
                                if($icon_settings['icon_image'] != '') {
                                    echo ' <a class="remove_cii" href="#" style="margin-left:10px;"> remove</a>';
                                }
                            } ?>
                        </span>
                    </td>
                </tr>
                <?php
            $i++; }
        endif;
        ?>
        <?php
        if($logged_in_menu_icons) {
            echo '<tr class="logged_in_menu_icons_header"><td colspan="4">Icon setting for second menu (menu for logged in users)</td></tr>';

            if($menus) : foreach($menus as $m) :
            if($m->term_id == $options['logged_in_menu']) $menu = $m;
            endforeach; endif;
            if(is_object($menu)) :
                $menu_items = wp_get_nav_menu_items($menu->term_id);
                $i=0;foreach ($menu_items as $item) { 
                    $icon_settings = get_item_icon_settings($options['logmenu_icons_for_items'], $item->ID);
                    ?>
                    <tr class="sdrn_ii_li">
                        <td>
                            <?php sdrn_item_indentation($item, $menu_items) ?>
                            <input type="hidden" name="sdrn_log_ii[<?php echo $i ?>][item_id]" value="<?php echo $item->ID ?>">
                        </td>
                        <td>
                            <input type="text" name="sdrn_log_ii[<?php echo $i ?>][item_icon]" value="<?php echo $icon_settings['item_icon'] ?>">
                        </td>
                        <td>
                            <input maxlength="7" size="5" type="text" name="sdrn_log_ii[<?php echo $i ?>][color]" id="sdrn_log_ii_<?php echo $i ?>"  value="<?php echo $icon_settings['color'] ?>" />
                        </td>
                        <td>
                            <input type="hidden" name="sdrn_log_ii[<?php echo $i ?>][icon_image]" class="cii_image_input" value="<?php echo $icon_settings['icon_image'] ?>">
                            <span style="position:relative">
                                <?php
                                $maybe_hide = 'display: none; ';
                                $src = '';
                                if(isset($icon_settings['icon_image'])) {
                                    if(!$icon_settings['icon_image'] == '') {
                                        $maybe_hide = '';
                                        $src = $icon_settings['icon_image'];
                                    } 
                                }                            
                                ?>
                                <img style="<?php echo $maybe_hide ?> width:23px; height:23px; margin-bottom:-6px; margin-right:10px;" class="cii_image_prev" src="<?php echo $src ?>" alt="">
                            </span>
                            <input type="button" class="button cii_select_file" value="Choose image" />
                            <span class="description">
                                <?php if(isset($icon_settings['icon_image'])) {
                                    if($icon_settings['icon_image'] != '') {
                                        echo ' <a class="remove_cii" href="#" style="margin-left:10px;"> remove</a>';
                                    }
                                } ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                $i++; }
            endif;



        }   
        ?>
        </tbody>
    </table>
    <style type="text/css">
        #sdrn_m_icons {
            border-spacing: 0px;
            border-left:1px solid #E3E3E3;
            border-top:1px solid #E3E3E3;
        }
        #sdrn_m_icons td, #sdrn_m_icons th {
            padding:8px;
            border-right:1px solid #E3E3E3;
            border-bottom:1px solid #E3E3E3;
            background:#fefefe;
            border-spacing: 0px!important;
        }
        #sdrn_m_icons th, #sdrn_m_icons .logged_in_menu_icons_header td { 
            background: #333333;
            color: #BBBBBB;
            border:none;
        }
        #sdrn_m_icons th.icth, #sdrn_m_icons th.imith, #sdrn_m_icons th.iccl {
            border-right:1px solid #BBBBBB;
        }
        #sdrn_m_icons tr.sdrn_ii_tr input[type="text"] {
            margin: 0px;
        }
        #sdrn_m_icons li.sdrn_ii_li .wp-picker-container .wp-color-result {
            margin:0px;
            top:2px;
        }

    </style>
    <?php
}





/**
 * Helper function for marking the menu nested items with "-"
 * @param  object $item menu item object
 * @return string       menu item title with indentation marks.
 */
function sdrn_item_indentation($item, $menu_items) {
    $pre = '';
    $title = $item->title;
    if($item->menu_item_parent > 0) {
        while($item->menu_item_parent > 0) {
            $pre .= '— ';
            foreach ($menu_items as $menu_item) {
                if($menu_item->ID == $item->menu_item_parent) $item = $menu_item;
            }
        }
    }
    echo $pre . $title;
}

/**
 * Helper function for geting the icon settings for right menu item (comapres item ID's - does not use array keys)
 * @param  array $icons_array settings for all icons saved for this menu
 * @param  int $item_id     menu item id
 * @return array             icon settings for current menu item
 */
function get_item_icon_settings($icons_array, $item_id) {
    $item_icons_settings = array(
        'item_id'=>$item_id,
        'item_icon'=>'',
        'color'=>'#F1F1F1',
        'icon_image'=>''
        );
    foreach ($icons_array as $icon_array_item) {
        if($icon_array_item['item_id'] == $item_id) return $icon_array_item;
    }
    return $item_icons_settings;
}











/**
 *
 * VALIDATE & PREPARE FOR SAVING
 *
 * Validates and PREPARES FOR SAVING the values from ALL the inputs
 * @param array $input The array that holds all the inputs from the settings page
 *
 * (the saving is handled by Wordpress)
 *
 */
function sdrn_options_validate($input) {
    global $sdrn_setup; //default settings array

    $options = get_option('sdrn_options');

    //enabled  / dispabled
    if(isset($input['menu'])) {
        $options['enabled'] = $input['enabled'];
    }

    //section "General", option "menu"
    if(isset($input['menu'])) {
        $options['menu'] = $input['menu'];
        if($options['menu'] == false || $options['menu'] == null || $options['menu'] == 0 || $options['menu'] == '') $options['menu'] = '';
    }

    //section "General", option "logged_in_menu"
    if(isset($input['logged_in_menu'])) {
        $options['logged_in_menu'] = $input['logged_in_menu'];
        if($options['logged_in_menu'] == false || $options['logged_in_menu'] == null || $options['logged_in_menu'] == 0 || $options['logged_in_menu'] == '') $options['logged_in_menu'] = '';
    }

    //section "General", option "custom_for_logged_in"
    if(isset($input['custom_for_logged_in'])) {
        $options['custom_for_logged_in'] = $input['custom_for_logged_in'];
    }

    if(isset($input['menu_symbol_pos'])) {
       $options['menu_symbol_pos'] = $input['menu_symbol_pos'];
    }

    //section "General", option "bar_title"
    if(isset($input['bar_title'])) {
        $options['bar_title'] = trim($input['bar_title']);
        if($options['bar_title'] == false || $options['bar_title'] == '') $options['bar_title'] = '';
    }

    //section "General", option "bar_logo"
    if(isset($input['bar_logo'])) {
        $options['bar_logo'] = trim($input['bar_logo']);
        if($options['bar_logo'] == false || $options['bar_logo'] == '') $options['bar_logo'] = '';
    }

    if(isset($input['nesting_icon'])) {
        $options['nesting_icon'] = trim($input['nesting_icon']);
        if($options['nesting_icon'] == false || $options['nesting_icon'] == '') $options['nesting_icon'] = '';
    }

    if(isset($input['nesting_icon_open'])) {
        $options['nesting_icon_open'] = trim($input['nesting_icon_open']);
        if($options['nesting_icon_open'] == false || $options['nesting_icon_open'] == '') $options['nesting_icon_open'] = '';
    }

    //section "General", option "expand_sub_with_parent"
    if(isset($input['expand_sub_with_parent'])) {
        $options['expand_sub_with_parent'] = $input['expand_sub_with_parent'];
    }

    //section "General", option "display_on"
    if(isset($input['display_on'])) {
        $options['display_on'] = $input['display_on'];
    }

    //section "General", option "from_width"
    if(isset($input['from_width'])) {
        $options['from_width'] = $input['from_width'];
    }

    //section "General", option "device"
    if(isset($input['menu'])) $options['devices'] = array(); //checkin if MENU is set makes sure that we are saving the general settings tab
    if(isset($input['devices'])) {
        $options['devices'] = $input['devices'];
    }

    //section "General", option "position"
    if(isset($input['position'])) {
        $options['position'] = $input['position'];
    }

    //section "General", option "how_wide"
    if(isset($input['how_wide'])) {
        $options['how_wide'] = $input['how_wide'];
    }

    //section "General", option "swipe_actions"
    if(isset($input['swipe_actions'])) {
        $options['swipe_actions'] = $input['swipe_actions'];
    }

    //section "General", option "hide"
    if(isset($input['hide'])) {
        $sel = explode(',', trim($input['hide']));
        foreach($sel as $s) {
            $selectors[] = trim($s);
        }
        $options['hide'] = $selectors;
    } else {
    }

    //section "General", option "zooming"
    if(isset($input['zooming'])) {
        $options['zooming'] = $input['zooming'];
    }

    //section "General", option "animation"
    if(isset($input['animation'])) {
        $options['animation'] = $input['animation'];
    }

    //section "General", option "searchbar"
    if(isset($input['searchbar'])) {
        $options['searchbar'] = $input['searchbar'];
    }

    //section "General", option "searchbar_label"
    if(isset($input['searchbar_label'])) {
        $options['searchbar_label'] = trim($input['searchbar_label']);
        if($options['searchbar_label'] == false || $options['searchbar_label'] == '') $options['searchbar_label'] = '';
    }



    //section "appearance", option "bar_bgd"
    if(isset($input['bar_bgd'])) {
        $options['bar_bgd'] = $input['bar_bgd'];
    }

    //section "appearance", option "bar_color"
    if(isset($input['bar_color'])) {
        $options['bar_color'] = $input['bar_color'];
    }

    //section "appearance", option "menu_bgd"
    if(isset($input['menu_bgd'])) {
        $options['menu_bgd'] = $input['menu_bgd'];
    }

    //section "appearance", option "menu_color"
    if(isset($input['menu_color'])) {
        $options['menu_color'] = $input['menu_color'];
    }

    //section "appearance", option "menu_color_hover"
    if(isset($input['menu_color_hover'])) {
        $options['menu_color_hover'] = $input['menu_color_hover'];
    }

    //section "appearance", option "menu_border_top"
    if(isset($input['menu_border_top'])) {
        $options['menu_border_top'] = $input['menu_border_top'];
    }


    //section "appearance", option "menu_border_bottom"
    if(isset($input['menu_border_bottom'])) {
        $options['menu_border_bottom'] = $input['menu_border_bottom'];
    }

    if(isset($input['menu_border_bottom_show'])) {
        $options['menu_border_bottom_show'] = $input['menu_border_bottom_show'];
    }


    if(isset($_POST['sdrn_ii'])) {
        $options['icons_for_items'] = $_POST['sdrn_ii'];
    }

    if(isset($_POST['sdrn_log_ii'])) {
        $options['logmenu_icons_for_items'] = $_POST['sdrn_log_ii'];
    }

    if(isset($input['searchbar_border_color'])) {
        $options['searchbar_border_color'] = $input['searchbar_border_color'];
    }

    if(isset($input['searchbar_background_color'])) {
        $options['searchbar_background_color'] = $input['searchbar_background_color'];
    }

    //save only the options that were changed
    $options = array_merge(get_option('sdrn_options'), $options);


    //prepare font awesome styles
    

    //echo '<pre>'; print_r($options); echo '</pre>';

    return $options;
}





function font_awesome_css($options) {
    // the $fa_icons will store all the icon codes used by actual settings of Mobile.Nav
    $fa_icons = array();

    //Lets loop trough Mobile.nav settings and gather all used icon codes to $fa_icons array
    if($options['nesting_icon'] != '') $fa_icons[] = $options['nesting_icon'];
    if($options['nesting_icon_open'] != '') $fa_icons[] = $options['nesting_icon_open'];
    if(!empty($options['icons_for_items'])) {
        foreach ($options['icons_for_items'] as $icon) {
            if($icon['item_icon'] != '') $fa_icons[] = $icon['item_icon'];
        }
    }
    if(!empty($options['logmenu_icons_for_items'])) {
        foreach ($options['logmenu_icons_for_items'] as $icon) {
            if($icon['item_icon'] != '') $fa_icons[] = $icon['item_icon'];
        }
    }
    $fa_icons = array_unique($fa_icons);
    //echo "<pre>"; print_r($fa_icons); echo "</pre>"; 

    //css styles for all the icons copied from fontawesome css file (MINIFIED)
    //based on fontawesome .css file (v.4.0.3)
    $css = '.fa-glass:before{content:"\f000"}.fa-music:before{content:"\f001"}.fa-search:before{content:"\f002"}.fa-envelope-o:before{content:"\f003"}.fa-heart:before{content:"\f004"}.fa-star:before{content:"\f005"}.fa-star-o:before{content:"\f006"}.fa-user:before{content:"\f007"}.fa-film:before{content:"\f008"}.fa-th-large:before{content:"\f009"}.fa-th:before{content:"\f00a"}.fa-th-list:before{content:"\f00b"}.fa-check:before{content:"\f00c"}.fa-times:before{content:"\f00d"}.fa-search-plus:before{content:"\f00e"}.fa-search-minus:before{content:"\f010"}.fa-power-off:before{content:"\f011"}.fa-signal:before{content:"\f012"}.fa-cog:before,.fa-gear:before{content:"\f013"}.fa-trash-o:before{content:"\f014"}.fa-home:before{content:"\f015"}.fa-file-o:before{content:"\f016"}.fa-clock-o:before{content:"\f017"}.fa-road:before{content:"\f018"}.fa-download:before{content:"\f019"}.fa-arrow-circle-o-down:before{content:"\f01a"}.fa-arrow-circle-o-up:before{content:"\f01b"}.fa-inbox:before{content:"\f01c"}.fa-play-circle-o:before{content:"\f01d"}.fa-repeat:before,.fa-rotate-right:before{content:"\f01e"}.fa-refresh:before{content:"\f021"}.fa-list-alt:before{content:"\f022"}.fa-lock:before{content:"\f023"}.fa-flag:before{content:"\f024"}.fa-headphones:before{content:"\f025"}.fa-volume-off:before{content:"\f026"}.fa-volume-down:before{content:"\f027"}.fa-volume-up:before{content:"\f028"}.fa-qrcode:before{content:"\f029"}.fa-barcode:before{content:"\f02a"}.fa-tag:before{content:"\f02b"}.fa-tags:before{content:"\f02c"}.fa-book:before{content:"\f02d"}.fa-bookmark:before{content:"\f02e"}.fa-print:before{content:"\f02f"}.fa-camera:before{content:"\f030"}.fa-font:before{content:"\f031"}.fa-bold:before{content:"\f032"}.fa-italic:before{content:"\f033"}.fa-text-height:before{content:"\f034"}.fa-text-width:before{content:"\f035"}.fa-align-left:before{content:"\f036"}.fa-align-center:before{content:"\f037"}.fa-align-right:before{content:"\f038"}.fa-align-justify:before{content:"\f039"}.fa-list:before{content:"\f03a"}.fa-dedent:before,.fa-outdent:before{content:"\f03b"}.fa-indent:before{content:"\f03c"}.fa-video-camera:before{content:"\f03d"}.fa-picture-o:before{content:"\f03e"}.fa-pencil:before{content:"\f040"}.fa-map-marker:before{content:"\f041"}.fa-adjust:before{content:"\f042"}.fa-tint:before{content:"\f043"}.fa-edit:before,.fa-pencil-square-o:before{content:"\f044"}.fa-share-square-o:before{content:"\f045"}.fa-check-square-o:before{content:"\f046"}.fa-arrows:before{content:"\f047"}.fa-step-backward:before{content:"\f048"}.fa-fast-backward:before{content:"\f049"}.fa-backward:before{content:"\f04a"}.fa-play:before{content:"\f04b"}.fa-pause:before{content:"\f04c"}.fa-stop:before{content:"\f04d"}.fa-forward:before{content:"\f04e"}.fa-fast-forward:before{content:"\f050"}.fa-step-forward:before{content:"\f051"}.fa-eject:before{content:"\f052"}.fa-chevron-left:before{content:"\f053"}.fa-chevron-right:before{content:"\f054"}.fa-plus-circle:before{content:"\f055"}.fa-minus-circle:before{content:"\f056"}.fa-times-circle:before{content:"\f057"}.fa-check-circle:before{content:"\f058"}.fa-question-circle:before{content:"\f059"}.fa-info-circle:before{content:"\f05a"}.fa-crosshairs:before{content:"\f05b"}.fa-times-circle-o:before{content:"\f05c"}.fa-check-circle-o:before{content:"\f05d"}.fa-ban:before{content:"\f05e"}.fa-arrow-left:before{content:"\f060"}.fa-arrow-right:before{content:"\f061"}.fa-arrow-up:before{content:"\f062"}.fa-arrow-down:before{content:"\f063"}.fa-mail-forward:before,.fa-share:before{content:"\f064"}.fa-expand:before{content:"\f065"}.fa-compress:before{content:"\f066"}.fa-plus:before{content:"\f067"}.fa-minus:before{content:"\f068"}.fa-asterisk:before{content:"\f069"}.fa-exclamation-circle:before{content:"\f06a"}.fa-gift:before{content:"\f06b"}.fa-leaf:before{content:"\f06c"}.fa-fire:before{content:"\f06d"}.fa-eye:before{content:"\f06e"}.fa-eye-slash:before{content:"\f070"}.fa-exclamation-triangle:before,.fa-warning:before{content:"\f071"}.fa-plane:before{content:"\f072"}.fa-calendar:before{content:"\f073"}.fa-random:before{content:"\f074"}.fa-comment:before{content:"\f075"}.fa-magnet:before{content:"\f076"}.fa-chevron-up:before{content:"\f077"}.fa-chevron-down:before{content:"\f078"}.fa-retweet:before{content:"\f079"}.fa-shopping-cart:before{content:"\f07a"}.fa-folder:before{content:"\f07b"}.fa-folder-open:before{content:"\f07c"}.fa-arrows-v:before{content:"\f07d"}.fa-arrows-h:before{content:"\f07e"}.fa-bar-chart-o:before{content:"\f080"}.fa-twitter-square:before{content:"\f081"}.fa-facebook-square:before{content:"\f082"}.fa-camera-retro:before{content:"\f083"}.fa-key:before{content:"\f084"}.fa-cogs:before,.fa-gears:before{content:"\f085"}.fa-comments:before{content:"\f086"}.fa-thumbs-o-up:before{content:"\f087"}.fa-thumbs-o-down:before{content:"\f088"}.fa-star-half:before{content:"\f089"}.fa-heart-o:before{content:"\f08a"}.fa-sign-out:before{content:"\f08b"}.fa-linkedin-square:before{content:"\f08c"}.fa-thumb-tack:before{content:"\f08d"}.fa-external-link:before{content:"\f08e"}.fa-sign-in:before{content:"\f090"}.fa-trophy:before{content:"\f091"}.fa-github-square:before{content:"\f092"}.fa-upload:before{content:"\f093"}.fa-lemon-o:before{content:"\f094"}.fa-phone:before{content:"\f095"}.fa-square-o:before{content:"\f096"}.fa-bookmark-o:before{content:"\f097"}.fa-phone-square:before{content:"\f098"}.fa-twitter:before{content:"\f099"}.fa-facebook:before{content:"\f09a"}.fa-github:before{content:"\f09b"}.fa-unlock:before{content:"\f09c"}.fa-credit-card:before{content:"\f09d"}.fa-rss:before{content:"\f09e"}.fa-hdd-o:before{content:"\f0a0"}.fa-bullhorn:before{content:"\f0a1"}.fa-bell:before{content:"\f0f3"}.fa-certificate:before{content:"\f0a3"}.fa-hand-o-right:before{content:"\f0a4"}.fa-hand-o-left:before{content:"\f0a5"}.fa-hand-o-up:before{content:"\f0a6"}.fa-hand-o-down:before{content:"\f0a7"}.fa-arrow-circle-left:before{content:"\f0a8"}.fa-arrow-circle-right:before{content:"\f0a9"}.fa-arrow-circle-up:before{content:"\f0aa"}.fa-arrow-circle-down:before{content:"\f0ab"}.fa-globe:before{content:"\f0ac"}.fa-wrench:before{content:"\f0ad"}.fa-tasks:before{content:"\f0ae"}.fa-filter:before{content:"\f0b0"}.fa-briefcase:before{content:"\f0b1"}.fa-arrows-alt:before{content:"\f0b2"}.fa-group:before,.fa-users:before{content:"\f0c0"}.fa-chain:before,.fa-link:before{content:"\f0c1"}.fa-cloud:before{content:"\f0c2"}.fa-flask:before{content:"\f0c3"}.fa-cut:before,.fa-scissors:before{content:"\f0c4"}.fa-copy:before,.fa-files-o:before{content:"\f0c5"}.fa-paperclip:before{content:"\f0c6"}.fa-floppy-o:before,.fa-save:before{content:"\f0c7"}.fa-square:before{content:"\f0c8"}.fa-bars:before{content:"\f0c9"}.fa-list-ul:before{content:"\f0ca"}.fa-list-ol:before{content:"\f0cb"}.fa-strikethrough:before{content:"\f0cc"}.fa-underline:before{content:"\f0cd"}.fa-table:before{content:"\f0ce"}.fa-magic:before{content:"\f0d0"}.fa-truck:before{content:"\f0d1"}.fa-pinterest:before{content:"\f0d2"}.fa-pinterest-square:before{content:"\f0d3"}.fa-google-plus-square:before{content:"\f0d4"}.fa-google-plus:before{content:"\f0d5"}.fa-money:before{content:"\f0d6"}.fa-caret-down:before{content:"\f0d7"}.fa-caret-up:before{content:"\f0d8"}.fa-caret-left:before{content:"\f0d9"}.fa-caret-right:before{content:"\f0da"}.fa-columns:before{content:"\f0db"}.fa-sort:before,.fa-unsorted:before{content:"\f0dc"}.fa-sort-asc:before,.fa-sort-down:before{content:"\f0dd"}.fa-sort-desc:before,.fa-sort-up:before{content:"\f0de"}.fa-envelope:before{content:"\f0e0"}.fa-linkedin:before{content:"\f0e1"}.fa-rotate-left:before,.fa-undo:before{content:"\f0e2"}.fa-gavel:before,.fa-legal:before{content:"\f0e3"}.fa-dashboard:before,.fa-tachometer:before{content:"\f0e4"}.fa-comment-o:before{content:"\f0e5"}.fa-comments-o:before{content:"\f0e6"}.fa-bolt:before,.fa-flash:before{content:"\f0e7"}.fa-sitemap:before{content:"\f0e8"}.fa-umbrella:before{content:"\f0e9"}.fa-clipboard:before,.fa-paste:before{content:"\f0ea"}.fa-lightbulb-o:before{content:"\f0eb"}.fa-exchange:before{content:"\f0ec"}.fa-cloud-download:before{content:"\f0ed"}.fa-cloud-upload:before{content:"\f0ee"}.fa-user-md:before{content:"\f0f0"}.fa-stethoscope:before{content:"\f0f1"}.fa-suitcase:before{content:"\f0f2"}.fa-bell-o:before{content:"\f0a2"}.fa-coffee:before{content:"\f0f4"}.fa-cutlery:before{content:"\f0f5"}.fa-file-text-o:before{content:"\f0f6"}.fa-building-o:before{content:"\f0f7"}.fa-hospital-o:before{content:"\f0f8"}.fa-ambulance:before{content:"\f0f9"}.fa-medkit:before{content:"\f0fa"}.fa-fighter-jet:before{content:"\f0fb"}.fa-beer:before{content:"\f0fc"}.fa-h-square:before{content:"\f0fd"}.fa-plus-square:before{content:"\f0fe"}.fa-angle-double-left:before{content:"\f100"}.fa-angle-double-right:before{content:"\f101"}.fa-angle-double-up:before{content:"\f102"}.fa-angle-double-down:before{content:"\f103"}.fa-angle-left:before{content:"\f104"}.fa-angle-right:before{content:"\f105"}.fa-angle-up:before{content:"\f106"}.fa-angle-down:before{content:"\f107"}.fa-desktop:before{content:"\f108"}.fa-laptop:before{content:"\f109"}.fa-tablet:before{content:"\f10a"}.fa-mobile-phone:before,.fa-mobile:before{content:"\f10b"}.fa-circle-o:before{content:"\f10c"}.fa-quote-left:before{content:"\f10d"}.fa-quote-right:before{content:"\f10e"}.fa-spinner:before{content:"\f110"}.fa-circle:before{content:"\f111"}.fa-mail-reply:before,.fa-reply:before{content:"\f112"}.fa-github-alt:before{content:"\f113"}.fa-folder-o:before{content:"\f114"}.fa-folder-open-o:before{content:"\f115"}.fa-smile-o:before{content:"\f118"}.fa-frown-o:before{content:"\f119"}.fa-meh-o:before{content:"\f11a"}.fa-gamepad:before{content:"\f11b"}.fa-keyboard-o:before{content:"\f11c"}.fa-flag-o:before{content:"\f11d"}.fa-flag-checkered:before{content:"\f11e"}.fa-terminal:before{content:"\f120"}.fa-code:before{content:"\f121"}.fa-mail-reply-all:before,.fa-reply-all:before{content:"\f122"}.fa-star-half-empty:before,.fa-star-half-full:before,.fa-star-half-o:before{content:"\f123"}.fa-location-arrow:before{content:"\f124"}.fa-crop:before{content:"\f125"}.fa-code-fork:before{content:"\f126"}.fa-chain-broken:before,.fa-unlink:before{content:"\f127"}.fa-question:before{content:"\f128"}.fa-info:before{content:"\f129"}.fa-exclamation:before{content:"\f12a"}.fa-superscript:before{content:"\f12b"}.fa-subscript:before{content:"\f12c"}.fa-eraser:before{content:"\f12d"}.fa-puzzle-piece:before{content:"\f12e"}.fa-microphone:before{content:"\f130"}.fa-microphone-slash:before{content:"\f131"}.fa-shield:before{content:"\f132"}.fa-calendar-o:before{content:"\f133"}.fa-fire-extinguisher:before{content:"\f134"}.fa-rocket:before{content:"\f135"}.fa-maxcdn:before{content:"\f136"}.fa-chevron-circle-left:before{content:"\f137"}.fa-chevron-circle-right:before{content:"\f138"}.fa-chevron-circle-up:before{content:"\f139"}.fa-chevron-circle-down:before{content:"\f13a"}.fa-html5:before{content:"\f13b"}.fa-css3:before{content:"\f13c"}.fa-anchor:before{content:"\f13d"}.fa-unlock-alt:before{content:"\f13e"}.fa-bullseye:before{content:"\f140"}.fa-ellipsis-h:before{content:"\f141"}.fa-ellipsis-v:before{content:"\f142"}.fa-rss-square:before{content:"\f143"}.fa-play-circle:before{content:"\f144"}.fa-ticket:before{content:"\f145"}.fa-minus-square:before{content:"\f146"}.fa-minus-square-o:before{content:"\f147"}.fa-level-up:before{content:"\f148"}.fa-level-down:before{content:"\f149"}.fa-check-square:before{content:"\f14a"}.fa-pencil-square:before{content:"\f14b"}.fa-external-link-square:before{content:"\f14c"}.fa-share-square:before{content:"\f14d"}.fa-compass:before{content:"\f14e"}.fa-caret-square-o-down:before,.fa-toggle-down:before{content:"\f150"}.fa-caret-square-o-up:before,.fa-toggle-up:before{content:"\f151"}.fa-caret-square-o-right:before,.fa-toggle-right:before{content:"\f152"}.fa-eur:before,.fa-euro:before{content:"\f153"}.fa-gbp:before{content:"\f154"}.fa-dollar:before,.fa-usd:before{content:"\f155"}.fa-inr:before,.fa-rupee:before{content:"\f156"}.fa-cny:before,.fa-jpy:before,.fa-rmb:before,.fa-yen:before{content:"\f157"}.fa-rouble:before,.fa-rub:before,.fa-ruble:before{content:"\f158"}.fa-krw:before,.fa-won:before{content:"\f159"}.fa-bitcoin:before,.fa-btc:before{content:"\f15a"}.fa-file:before{content:"\f15b"}.fa-file-text:before{content:"\f15c"}.fa-sort-alpha-asc:before{content:"\f15d"}.fa-sort-alpha-desc:before{content:"\f15e"}.fa-sort-amount-asc:before{content:"\f160"}.fa-sort-amount-desc:before{content:"\f161"}.fa-sort-numeric-asc:before{content:"\f162"}.fa-sort-numeric-desc:before{content:"\f163"}.fa-thumbs-up:before{content:"\f164"}.fa-thumbs-down:before{content:"\f165"}.fa-youtube-square:before{content:"\f166"}.fa-youtube:before{content:"\f167"}.fa-xing:before{content:"\f168"}.fa-xing-square:before{content:"\f169"}.fa-youtube-play:before{content:"\f16a"}.fa-dropbox:before{content:"\f16b"}.fa-stack-overflow:before{content:"\f16c"}.fa-instagram:before{content:"\f16d"}.fa-flickr:before{content:"\f16e"}.fa-adn:before{content:"\f170"}.fa-bitbucket:before{content:"\f171"}.fa-bitbucket-square:before{content:"\f172"}.fa-tumblr:before{content:"\f173"}.fa-tumblr-square:before{content:"\f174"}.fa-long-arrow-down:before{content:"\f175"}.fa-long-arrow-up:before{content:"\f176"}.fa-long-arrow-left:before{content:"\f177"}.fa-long-arrow-right:before{content:"\f178"}.fa-apple:before{content:"\f179"}.fa-windows:before{content:"\f17a"}.fa-android:before{content:"\f17b"}.fa-linux:before{content:"\f17c"}.fa-dribbble:before{content:"\f17d"}.fa-skype:before{content:"\f17e"}.fa-foursquare:before{content:"\f180"}.fa-trello:before{content:"\f181"}.fa-female:before{content:"\f182"}.fa-male:before{content:"\f183"}.fa-gittip:before{content:"\f184"}.fa-sun-o:before{content:"\f185"}.fa-moon-o:before{content:"\f186"}.fa-archive:before{content:"\f187"}.fa-bug:before{content:"\f188"}.fa-vk:before{content:"\f189"}.fa-weibo:before{content:"\f18a"}.fa-renren:before{content:"\f18b"}.fa-pagelines:before{content:"\f18c"}.fa-stack-exchange:before{content:"\f18d"}.fa-arrow-circle-o-right:before{content:"\f18e"}.fa-arrow-circle-o-left:before{content:"\f190"}.fa-caret-square-o-left:before,.fa-toggle-left:before{content:"\f191"}.fa-dot-circle-o:before{content:"\f192"}.fa-wheelchair:before{content:"\f193"}.fa-vimeo-square:before{content:"\f194"}.fa-try:before,.fa-turkish-lira:before{content:"\f195"}.fa-plus-square-o:before{content:"\f196"}';

    //explode the styles by "}""
    $styles = explode('}',$css);
    array_pop($styles);
    $data = array();
 

    //track the class selector for icon + its symbol and store them in array
    foreach ($styles as $style) {
        $sel = preg_match_all('/(fa-)[\w-]+/', $style, $res);
        $sel = $res[0];
        $code = preg_match('/(?<=").+(?=")/', $style, $res2);
        $code = $res2[0];

        foreach ($sel as $s) {
            $data[$s]=$code;
        }
    }

    //loop trough array and prepare the mobile nav icon styling
    $string = '';
    //echo "<pre>"; print_r($data); echo "</pre>"; 
    
    //loop trough all font awesome icon codes and their classes and use the m (include in style string) only if they are found in $fa_icons array - that stores currently used icons
    foreach ($data as $sel => $code) {
        if(in_array($sel, $fa_icons)) {
            //echo $sel.'<br>';
            $string .= '#sdrn_menu .sdrn_icon_par.'.$sel.':before { content: "'.$code.'"; } ';
            $string .= '#sdrn_menu .sdrn_par_opened.'.$sel.':before { content: "'.$code.'"!important; } ';
            $string .= '#sdrn_menu .sdrn_item_custom_icon.'.$sel.':before { content: "'.$code.'"!important; } ';
        }
    }

    return $string;
} 

//save custom font awesome styling after main plugin settings are saved
if (isset($_GET['settings-updated']) && isset($_GET['page'])) {
    if($_GET['page'] == 'sdrn_settings') {
        $options = get_option('sdrn_options');
        $options['font_awesome_css'] = font_awesome_css($options);
        update_option('sdrn_options', $options);
    }
}





