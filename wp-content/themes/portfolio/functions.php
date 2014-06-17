<?php

/**
 * Theme version
 */
define( 'PORTFOLIO_VERSION', '1.3.19' );

// Require the other functions files
locate_template( array( 'functions/admin.php' ), true );
locate_template( array( 'functions/utility.php' ), true );
locate_template( array( 'functions/theme-options.php' ), true );
locate_template( array( 'functions/post-options.php' ), true );
locate_template( array( 'functions/page-options.php' ), true );
locate_template( array( 'functions/tinymce-plugin.php' ), true );
locate_template( array( 'functions/ttf-common/ttf-common.php' ), true );

// Setup content width
if ( ! isset( $content_width ) ) {
	$content_width = 860;
}

function portfolio_set_content_width() {
	global $content_width;

	$is_portfolio_item_with_sidebar = portfolio_is_portfolio_item() && ! portfolio_show_portfolio_item_sidebar();
	$is_full_width = $is_portfolio_item_with_sidebar || is_page_template( 'default' ) || is_page_template( 'homepage.php' ) || is_page_template( 'portfolio.php' );

	if ( is_active_sidebar( 'primary_sidebar' ) && ! $is_full_width ) {
		$content_width = 550;
	}
}
add_action( 'template_redirect', 'portfolio_set_content_width' );

/* Theme setup
 * ------------------------------------------------------------------ */

add_action( 'after_setup_theme', 'portfolio_setup' );

if ( ! function_exists( 'portfolio_setup' ) ) :

	function portfolio_setup() {
		// Attempt to load from child theme first, then parent
		if ( ! load_theme_textdomain( 'portfolio', get_stylesheet_directory() . '/languages' ) ) {
			load_theme_textdomain( 'portfolio', get_template_directory() . '/languages' );
		}

		portfolio_options_init();

		// Theme support

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'portfolio_featured', 860, 450, true );
		add_image_size( 'portfolio_page_thumb', 242, 135, true );
		add_theme_support( 'post-formats', array( 'aside', 'image' ) );
		add_editor_style( 'includes/stylesheets/editor-style.css' );

		// Version 3.4 introduced a new way to register support for custom backgrounds
		global $wp_version;
		if ( version_compare( $wp_version, '3.4-alpha', '>=' ) ) {
			add_theme_support( 'custom-background' );
		} else {
			add_custom_background();
		}

		// Register elements

		register_nav_menu( 'primary', __( 'Primary Menu', 'portfolio' ) );

		// Filters from the common functions file

		add_filter( 'next_posts_link_attributes', 'ttf_common_link_rel_next' );
		add_filter( 'post_gallery', 'ttf_common_gallery_display', 10, 2 );
		add_filter( 'posts_where', 'ttf_common_remove_password_posts' );
		add_filter( 'previous_posts_link_attributes', 'ttf_common_link_rel_prev' );
		add_filter( 'wp_title', 'ttf_common_page_title' );

		// Register filters

		add_filter( 'excerpt_more', 'portfolio_excerpt_more' );
		add_filter( 'comment_text', 'portfolio_wrap_comment_text', 1000 );
		add_filter( 'body_class', 'portfolio_body_class' );
		add_filter( 'excerpt_length', 'portfolio_excerpt_length', 999 );
		add_filter( 'posts_where', 'portfolio_filter_portfolio_items' );
		add_filter( 'posts_join', 'portfolio_filter_portfolio_items' );
		add_filter( 'the_content', 'portfolio_filter_container_from_content', 1 ); // High priorty to remove this content
		add_filter( 'the_editor_content', 'portfolio_filter_container_from_editor' );

		// Add actions, standard stuff

		add_action( 'widgets_init', 'portfolio_register_sidebar' );
		add_action( 'wp_enqueue_scripts', 'portfolio_enqueue_scripts' );
		add_action( 'admin_enqueue_scripts', 'portfolio_enqueue_admin_scripts' );
		add_action( 'save_post', 'portfolio_save_post_meta', 10 );
		add_action( 'add_meta_boxes', 'portfolio_add_post_option_box' );
		add_action( 'save_post', 'portfolio_save_page_meta', 10 );
		add_action( 'add_meta_boxes', 'portfolio_add_page_option_box' );
		add_action( 'wp_head', 'portfolio_print_header_items' );
		add_action( 'init', 'portfolio_tinymce_plugin' );
		add_action( 'admin_print_footer_scripts', 'portfolio_admin_print_footer_excerpt_scripts' );
		add_action( 'admin_print_footer_scripts', 'portfolio_admin_print_footer_pointer_scripts' );
		add_action( 'save_post', 'portfolio_prepend_portfolio_container', 10, 2 );
		// Hooks that check if the post thumbnail is being changed
		add_action( 'added_post_meta', 'portfolio_set_post_thumbnail', 10, 4 );
		add_action( 'updated_post_meta', 'portfolio_set_post_thumbnail', 10, 4 );
		add_action( 'deleted_post_meta', 'portfolio_set_post_thumbnail', 10, 4 );
		add_action( 'customize_register', 'portfolio_hook_customizer_javascript' );

		portfolio_setup_color_selectors();

	}

endif; // portfolio_setup

if ( ! function_exists( 'portfolio_get_portfolio_items_query' ) ) :
	/**
	 * Queries for portfolio items, appending $args to the query.
	 */
	function portfolio_get_portfolio_items_query( $query_args=array() ) {
		$common_args = array(
			'meta_query' => array(
				array(
					'key' => PT_MetaKeys::IS_PORTFOLIO_ITEM,
					'value' => true,
					'type' => 'BOOLEAN'
				)
			),
			'posts_per_page' => -1,
			'ignore_sticky_posts' => 1
		);

		return new WP_Query( array_merge( $common_args, $query_args ) );
	}

endif; // portfolio_get_portfolio_items_query

if ( ! function_exists( 'portfolio_filter_portfolio_items' ) ) :
	/**
	 * Filters out portfolio items from the query
	 */
	function portfolio_filter_portfolio_items( $clause = '' ) {
		$apply_filter =
			! is_admin() &&
			! is_singular() && // Don't filter on a single page/post view.
			portfolio_option( 'hide_portfolio_items' ); // The option to remove portfolio items is enabled.

		if ( $apply_filter ) {
			global $wpdb;

			// To keep it simple, both the join and where clauses are modified here.
			if ( 'posts_join' == current_filter() ) {
				// We join the postmeta table so we can check the value in the WHERE clause.
				$clause .= " LEFT JOIN $wpdb->postmeta AS pt_meta ON ($wpdb->posts.ID = pt_meta.post_id AND pt_meta.meta_key = '" . PT_MetaKeys::IS_PORTFOLIO_ITEM . "') ";
			} else {
				// Check whether the value is empty or NULL. If it is neither, then we want to filter it.
				$clause .= " AND ( (pt_meta.meta_key = '" . PT_MetaKeys::IS_PORTFOLIO_ITEM . "' AND CAST(pt_meta.meta_value AS CHAR) = '') OR pt_meta.meta_id IS NULL ) ";
			}
		}

		return $clause;
	}

endif; // portfolio_filter_portfolio_items

if ( ! function_exists( 'portfolio_get_format_template' ) ) :
	/**
	 * Convenience function to get the format name, filtered by those this theme has templates for
	 */
	function portfolio_get_format_template() {
		$formats_with_templates = array( 'aside', 'image' );
		$format = get_post_format();

		// If we have a special template for this format, let's use it. If not, use standard
		$format = ( in_array( $format, $formats_with_templates ) ) ? $format : 'standard';

		return '_format-' . $format;
	}

endif; // portfolio_get_format_template

if ( ! function_exists( 'portfolio_get_font_styles' ) ) :
	/**
	 * Returns the font selectors + the appropriate stacks based on the font options selected.
	 */
	function portfolio_get_font_styles() {
		$styles = '';

		$accent_font = str_replace( '+', ' ', esc_html( portfolio_option( 'accent_font' ) ) );
		$body_font = str_replace( '+', ' ',  esc_html( portfolio_option( 'body_font' ) ) );

		if ( $accent_font ) {
			$accent_stack = portfolio_get_font_stack( $accent_font );

			$accent_font_selectors = 'blockquote, blockquote p';

			$styles .= "$accent_font_selectors { font-family: '$accent_font', $accent_stack; }\n";
		}

		if ( $body_font ) {
			$body_stack = portfolio_get_font_stack( $body_font );

			$body_font_selectors =
				'body, .page-template-homepage-php #featured span.slide-more,
				.colophon section button, .colophon section blockquote cite';

			$styles .= "$body_font_selectors { font-family: '$body_font', $body_stack; }\n";
		}

		return $styles;
	}

endif; // portfolio_get_font_styles

if ( ! function_exists( 'portfolio_get_font_stack' ) ) :
	/**
	 * Convenience function to get the fallback font stack for $font.
	 */
	function portfolio_get_font_stack( $font ) {
		// Specify which fonts fall under which stack
		$mono_fonts = array( 'Lekton' );
		$sans_serif_fonts = array( 'Helvetica', 'Arial', 'Gudea', 'PT Sans' );

		if ( in_array( $font, $sans_serif_fonts ) ) {
			// If $font is sans-serif, let's give it that stack
			$stack = "Helvetica, 'Helvetica Neue', Arial, Verdana, Tahoma, sans-serif";
		} elseif ( in_array( $font, $mono_fonts ) ) {
			// Or if it's mono, give it mono
			$stack = "Monaco, 'Courier New', Courier, monospace";
		} else {
			// And finally, fall back to the serif stack
			$stack = "Georgia, Cambria, 'Times New Roman', Times, serif";
		}

		return $stack;
	}

endif; // portfolio_get_font_stack

if ( ! function_exists( 'portfolio_print_header_items' ) ) :
	/**
	 * Prints JS and CSS necessary in the header
	 */
	function portfolio_print_header_items() {
		$color_styles = portfolio_get_color_styles();
		$font_styles = portfolio_get_font_styles();

		if ( ! empty( $color_styles ) ) {
			echo "<style id='portfolio-color-styles' type='text/css'>$color_styles</style>";
		}

		if ( ! empty( $font_styles ) ) {
			echo "<style id='portfolio-font-styles' type='text/css'>$font_styles</style>";
		}

		// If the image fade effect is on, we need to hide images temporarily
		if ( portfolio_use_lazyloading() ) :
?>
<script type="text/javascript">
	// We don't want to hide images unless JS is enabled, so we dynamically add the CSS necessary.
	// This was done to prevent flickering caused by hiding elements individually.
	var head = document.getElementsByTagName('head')[0],
	    style = document.createElement('style'),
	    rules = document.createTextNode('.page-feature img, .content img { visibility: hidden; } .flexslider img, .tiled-gallery img { visibility: visible; }');

	style.type = 'text/css';
	style.id = "portfolio-hidden-images";
	if(style.styleSheet)
	    style.styleSheet.cssText = rules.nodeValue;
	else style.appendChild(rules);
	head.appendChild(style);

	jQuery(document).ready(function(){
		// Let's remove the style. Any images that should be hidden are now set inline (by theme.js).
		// We need to remove the style in case images are added to the DOM after document.ready, and
		// they don't have our lazyloading handler attached.
		jQuery('#portfolio-hidden-images').remove();
	});
</script>
<?php endif; // END image fade effect JS ?>
<!--[if lte IE 8]>
	<style type="text/css">
		<?php echo file_get_contents( get_template_directory() . '/includes/stylesheets/mediaqueries.css' ); ?>
	</style>
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/javascripts/mediaqueries.js"></script>
<![endif]-->
<?php
	}

endif; // portfolio_print_header_items

if ( ! function_exists( 'portfolio_featured_slider_javascript' ) ) :
	/**
	 * JavaScript that powers the featured slider on the front page.
	 * Includes some height prediciton code that prevents the slider from dramatically changing the page height.
	 */
	function portfolio_featured_slider_javascript() { ?>
		<script type="text/javascript">
			jQuery('#featured .flexslider').flexslider({
				controlNav: false,
				pauseOnHover: true,
				touch: ttfUseTouchControls,
				<?php if ( ! portfolio_option( 'autostart_slider' ) ) { echo 'slideshow: false,'; } ?>
				<?php if ( portfolio_option( 'slider_animation' ) ) { echo "animation: '" . esc_js( portfolio_option( 'slider_animation' ) ) . "'"; } ?>
			});
		</script>
	<?php
	}

endif; // portfolio_featured_slider_javascript

if ( ! function_exists ( 'portfolio_comment' ) ) :

	function portfolio_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; ?>
		<li  id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<article>
				<header class="comment-author vcard">
					<?php echo get_avatar( $comment, $size = '48' ); ?>
					<cite class="fn"><?php comment_author_link(); ?></cite>
					<time datetime="<?php comment_date(); ?>"><a href="<?php echo esc_url( get_comment_link() ); ?>"><?php comment_date(); ?></a></time>
				</header>

				<?php if ( $comment->comment_approved == '0' ) { ?>
					<p><?php _e( 'Your comment is awaiting moderation.', 'portfolio' ) ?></p>
				<?php } ?>

				<section class="comment post-content">
					<?php comment_text(); ?>
				</section>

				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

			</article>
		</li>
	<?php
	}

endif; //portfolio_comment

if ( ! function_exists( 'portfolio_register_sidebar' ) ) :
	/**
	 * Register the sidebars
	 */
	function portfolio_register_sidebar() {
		register_sidebar( array(
			'name'=> __( 'Sidebar', 'portfolio' ),
			'id' => 'primary_sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		) );
	}

endif; // portfolio_register_sidebar

if ( ! function_exists( 'portfolio_wrap_comment_text' ) ) :
	/**
	 * Wrap & apply class to comment text
	 */
	function portfolio_wrap_comment_text( $content ) {
	    return "<div class=\"comment-text\">" . $content . "</div>";
	}

endif; //portfolio_wrap_comment_text

if ( ! function_exists( 'portfolio_post_thumbnail_caption' ) ) :
	/**
	 * Featured image caption
	 */
	function portfolio_post_thumbnail_caption() {
		global $post;

		$thumbnail_id    = get_post_thumbnail_id( $post->ID );
		$thumbnail_image = get_posts( array( 'p' => $thumbnail_id, 'post_type' => 'attachment' ) );

		if ( $thumbnail_image && isset( $thumbnail_image[0] ) ) {
			$excerpt = $thumbnail_image[0]->post_excerpt;
			$excerpt = apply_filters( 'the_excerpt', $excerpt );
			echo '<figcaption>' . $excerpt . '</figcaption>';
		}
	}

endif; // portfolio_post_thumbnail_caption

if ( ! function_exists( 'portfolio_excerpt_length' ) ) :
	/**
	 * Custom excerpt length
	 */
	function portfolio_excerpt_length( $length ) {
		if ( is_search() ) {
			return 50;
		} else {
			return 20;
		}
	}

endif; // portfolio_excerpt_length

if ( ! function_exists( 'portfolio_excerpt_more' ) ) :
	/**
	 * Custom excerpt more
	 */
	function portfolio_excerpt_more() {
		return '&hellip;';
	}

endif; // portfolio_excerpt_more

if ( ! function_exists( 'portfolio_enqueue_scripts' ) ) :
	/**
	 * Enqueue any scripts to be served on the frontend
	 */
	function portfolio_enqueue_scripts() {
		// Stylesheets first

		// Primary style.css file
		wp_enqueue_style(
			'portfolio_style',
			get_bloginfo( 'stylesheet_url' ),
			array(),
			null
		);

		portfolio_enqueue_font_styles();

		// JavaScripts next
		wp_enqueue_script( 'jquery' );

		// Primary theme JavaScript
		wp_enqueue_script(
			'portfolio_javascript',
			get_template_directory_uri() . '/javascripts/theme.js',
			array( 'jquery' ),
			null
		);

		// Lastly, enqueue the comment reply script if required

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

endif; // portfolio_enqueue_scripts

if ( ! function_exists( 'portfolio_enqueue_font_styles' ) ) :
	/**
	 * Used for loading fonts from Google WebFonts
	 */
	function portfolio_enqueue_font_styles() {
		$web_safe_fonts = array( 'Georgia', 'Arial' );
		// Enqueue fonts
		$body_font = portfolio_option( 'body_font' );
		$accent_font = portfolio_option( 'accent_font' );

		// Body fonts require extra weights
		if ( $body_font && ! in_array( $body_font, $web_safe_fonts ) ) {
			$fonts_to_request[] = $body_font . ':400,700,400italic';
		}

		// Don't re-request the same font!
		if ( $accent_font && ! in_array( $accent_font, $web_safe_fonts ) && ( $body_font != $accent_font ) ) {
			$fonts_to_request[] = $accent_font . ':400,400italic';
		}

		// If we need to request custom fonts, get them from Google
		if ( ! empty( $fonts_to_request ) ) {
			$protocol = is_ssl() ? 'https' : 'http';

			wp_enqueue_style(
				'portfolio_font_style',
				"$protocol://fonts.googleapis.com/css?family=" . implode( '|', $fonts_to_request ),
				array( 'portfolio_style' ),
				null
			);
		}
	}

endif; // portfolio_enqueue_font_styles

if ( ! function_exists( 'portfolio_setup_color_selectors' ) ) :
	/**
	 * Sets up a global to hold some CSS selector values that need to be used in multiple places
	 */
	function portfolio_setup_color_selectors() {
		global $portfolio_color_selectors;

		$portfolio_color_selectors = array(
			'font' =>
				'a, h5, h6, blockquote cite, img, span.required, header .post-meta a:hover,
				nav[role="navigation"] li.current-menu-item a, nav[role="navigation"] li.current-menu-parent a,
				nav[role="navigation"] li.current_page_item a, nav[role="navigation"] li.current_page_parent a,
				.tmp-portfolio .view-port-item::before, .page-template-homepage-php .view-port-item:before',
			'background' =>
				'table caption, .flexslider .flex-control-nav li a.active, button, span.slide-more,
				input[type="reset"], input[type="submit"], input[type="button"], .sticky-text,
				a.view-item-link',
			'border_bottom' =>
				'span.title-underline'
		);
	}

endif; // portfolio_setup_color_selectors

if ( ! function_exists( 'portfolio_get_color_styles' ) ) :
	/**
	 * Lots of HTML printing to allow customization of colors.
	 */
	function portfolio_get_color_styles() {
		// Grab the primary color selected on the theme options page
		$primary_color = esc_html( portfolio_option( 'primary_color' ) );

		global $portfolio_color_selectors;

		// All selectors to use CSS 'color' attribute
		$primary_font_color_selectors = $portfolio_color_selectors['font'];

		// All selectors to use CSS 'background-color' attribute
		$primary_bg_color_selectors = $portfolio_color_selectors['background'];

		$primary_border_bottom_color_selectors = $portfolio_color_selectors['border_bottom'];

		// Apply the primary color to the primary selectors
		$styles = "$primary_font_color_selectors { color: $primary_color; }\n";
		$styles .= "$primary_bg_color_selectors { background-color: $primary_color; }\n";
		$styles .= "$primary_border_bottom_color_selectors { border-bottom: 2px solid $primary_color; }\n";

		return $styles;
	}

endif; // portfolio_get_color_styles

if ( ! function_exists( 'portfolio_page_menu_item_class' ) ) :
/**
 * Adjust page menu item CSS classes
 *
 * If the current view is a portfolio item, and portfolio items are set to be hidden
 * from the blog, make sure the page menu doesn't highlight the Posts Page item.
 *
 * @since 1.3.16
 *
 * @param  array         $css_class       Array of classes applied to the item.
 * @param  string|int    $page            ID of the item.
 * @return array                          Modified classes
 */
function portfolio_page_menu_item_class( $css_class, $page ) {
	if ( ! portfolio_option( 'hide_portfolio_items' ) || ! portfolio_is_portfolio_item() ) {
		return $css_class;
	}

	$filtered_css_class = $css_class;

	if ( $page->ID === (int) get_option( 'page_for_posts' ) ) {
		$search = 'current_page_parent';
		$remove = array_keys( $filtered_css_class, $search );
		foreach ( $remove as $key ) {
			unset( $filtered_css_class[$key] );
		}
	}

	return $filtered_css_class;
}
endif;

add_filter( 'page_css_class', 'portfolio_page_menu_item_class', 10, 2 );

if ( ! function_exists( 'portfolio_nav_menu_objects' ) ) :
/**
 * Adjust nav menu item CSS classes
 *
 * If the current view is a portfolio item, and portfolio items are set to be hidden
 * from the blog, make sure the nav menu doesn't highlight the Posts Page item.
 *
 * @since 1.3.16
 *
 * @param  array    $items    The menu items.
 * @return array              Modified menu items.
 */
function portfolio_nav_menu_objects( $items ) {
	if ( ! portfolio_option( 'hide_portfolio_items' ) || ! portfolio_is_portfolio_item() ) {
    	return $items;
    }

    $filtered_items = $items;

    foreach ( $filtered_items as $item ) {
    	if ( $item->object_id === get_option( 'page_for_posts' ) ) {
    		$search = 'current_page_parent';
			$remove = array_keys( $item->classes, $search );
			foreach ( $remove as $key ) {
				unset( $item->classes[$key] );
			}
    	}
    }

    return $filtered_items;
}
endif;

add_filter( 'wp_nav_menu_objects', 'portfolio_nav_menu_objects' );

if ( ! function_exists( 'portfolio_get_featured_posts_query' ) ) :
	/**
	 * Returns a query of the posts that belong in the featured slider
	 */
	function portfolio_get_featured_posts_query() {
		return new WP_Query( array(
			'meta_query' => array(
				array(
					'key' => PT_MetaKeys::FEATURED_SLIDER,
					'value' => true,
					'type' => 'BOOLEAN'
				)
			),
			'posts_per_page' => -1,
			'ignore_sticky_posts' => 1
		) );
	}

endif; // portfolio_get_featured_posts_query

if ( ! function_exists( 'portfolio_hook_customizer_javascript' ) ) :
	/**
	 * Sets up the wp_footer hook to print the customizer setup JS.
	 */
	function portfolio_hook_customizer_javascript( $wp_customize ) {
		if ( $wp_customize->is_preview() && ! is_admin() ) {
			add_action( 'wp_footer', 'portfolio_print_customizer_javascript' );
		}
	}

endif; // portfolio_hook_customizer_javascript

if ( ! function_exists( 'portfolio_print_customizer_javascript' ) ) :
	/**
	 * Prints some JS variables that make customization previews much easier.
	 */
	function portfolio_print_customizer_javascript() {
		global $portfolio_color_selectors;
		?>
			<script type="text/javascript">
				var portfolioFontColorSelectors = <?php echo json_encode( $portfolio_color_selectors['font'] ); ?>;
				var portfolioBackgroundColorSelectors = <?php echo json_encode( $portfolio_color_selectors['background'] ); ?>;
				var portfolioBorderBottomColorSelectors = <?php echo json_encode( $portfolio_color_selectors['border_bottom'] ); ?>;
			</script>
		<?php
	}

endif; // portfolio_print_customizer_javascript