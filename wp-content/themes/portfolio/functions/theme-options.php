<?php
/* -------------------------------------------------------------------------
  Theme options
---------------------------------------------------------------------------- */

if ( ! function_exists( 'portfolio_options_init' ) ) :
	/**
	 * Create and initialize the theme options.
	 * Uses the Struts options framework.
	 * https://github.com/jestro/struts/
	 */
	function portfolio_options_init() {
		locate_template( array( 'includes/struts/classes/struts.php' ), true );

		Struts::load_config( array(
			'struts_root_uri' => get_template_directory_uri() . '/includes/struts', // required, set this to the URI of the root Struts directory
			'use_struts_skin' => true, // optional, overrides the Settings API html output
			'preview_javascript' => get_template_directory_uri() . '/includes/javascripts/previewer.js',
			'preview_javascript_dependencies' => array( 'jquery' )
		) );

		global $portfolio_options;

		$portfolio_options = new Struts_Options( 'portfolio', 'theme_portfolio_options' );

		// Setup the option sections
		$portfolio_options->add_section( 'logo_featured_slider_section', __( 'Logo &amp; featured slider', 'portfolio' ), 200 );
		$portfolio_options->add_section( 'font_color_section', __( 'Fonts &amp; colors', 'portfolio' ), 201 );
		$portfolio_options->add_section( 'display_options_section', __( 'Display options', 'portfolio' ), 202 );
		$portfolio_options->add_section( 'subscribe_copyright_section', __( 'Subscribe links &amp; copyright', 'portfolio' ), 203 );

		/* Logo and featured slider section
		 * ------------------------------------------------------------------ */

		$portfolio_options->add_option( 'logo_url', 'image', 'logo_featured_slider_section' )
			->default_value( '' )
			->label( __( 'Logo URL:', 'portfolio' ) )
			->description( __( 'Upload an image or enter an <abbr title="Universal resource locator">URL</abbr> for your image. If you choose not to upload a logo, your site\'s title will be used for the header.', 'portfolio' ) );

		$portfolio_options->add_option( 'autostart_slider', 'checkbox', 'logo_featured_slider_section' )
			->default_value( false )
			->label( __( 'Autostart featured slider', 'portfolio' ) )
			->description( __( 'Check to automatically start slideshow animation upon page load.', 'portfolio' ) );

		$portfolio_options->add_option( 'slider_animation', 'select', 'logo_featured_slider_section' )
			->valid_values( array(
				'slide' => __( 'Slide (Default)', 'portfolio' ),
				'fade' => __( 'Fade', 'portfolio' ) ) )
			->default_value( 'slide' )
			->label( __( 'Slider animation:', 'portfolio' ) )
			->description( __( 'Select an animation style for your slider.', 'portfolio' ) );

		/* Fonts and color section
		 * ------------------------------------------------------------------ */

		$portfolio_options->add_option( 'accent_font', 'select', 'font_color_section' )
			->valid_values( array(
				'Georgia' => __( 'Georgia (default)', 'portfolio' ),
				'Gudea' => __( 'Gudea', 'portfolio' ),
				'Gentium+Book+Basic' => __( 'Gentium Book Basic', 'portfolio' ),
				'Arial' => __( 'Arial', 'portfolio' ),
				'Helvetica' => __( 'Helvetica', 'portfolio' ),
				'Lekton' => __( 'Lekton', 'portfolio' ),
				'PT+Sans' => __( 'PT Sans', 'portfolio' ) ) )
			->default_value( 'Georgia' )
			->label( __( 'Accent font:', 'portfolio' ) )
			->description( __( 'The accent font is used for blockquotes.', 'portfolio' ) );

		$portfolio_options->add_option( 'body_font', 'select', 'font_color_section' )
			->valid_values( array(
				'Gudea' => __( 'Gudea (default)', 'portfolio' ),
				'Georgia' => __( 'Georgia', 'portfolio' ),
				'Gentium+Book+Basic' => __( 'Gentium Book Basic', 'portfolio' ),
				'Arial' => __( 'Arial', 'portfolio' ),
				'Helvetica' => __( 'Helvetica', 'portfolio' ),
				'Lekton' => __( 'Lekton', 'portfolio' ),
				'PT+Sans' => __( 'PT Sans', 'portfolio' ) ) )
			->default_value( 'Gudea' )
			->label( __( 'Body font:', 'portfolio' ) )
			->description( __( 'The body font is used for all content not covered by the accent font.<br><br><strong>Note:</strong> For optimum load times, use the <strong>Georgia</strong> or <strong>Arial</strong> font family. All other fonts will slightly increase page load time.', 'portfolio' ) );

		$portfolio_options->add_option( 'primary_color', 'color', 'font_color_section' )
			->default_value( '#eb171c' )
			->label( __( 'Primary color', 'portfolio' ) )
			->description( __( 'Choose the primary color for your theme. The primary color is used for link text, buttons, <code>&lt;h5&gt;</code> and <code>&lt;h6&gt;</code> tags as well as other accents.', 'portfolio' ) )
			->preview_function( 'portfolioThemePreviewPrimaryColor' );

		/* Display section
		 * ------------------------------------------------------------------ */

		$portfolio_options->add_option( 'hide_tags', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide tags', 'portfolio' ) )
			->description( __( 'Check to hide tags in posts.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_categories', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide categories', 'portfolio' ) )
			->description( __( 'Check to hide categories in posts.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_author', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide author', 'portfolio' ) )
			->description( __( 'Check to hide the author in posts.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_post_nav', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide post navigation', 'portfolio' ) )
			->description( __( 'Check to hide post navigation in single post view.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_date', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide post date', 'portfolio' ) )
			->description( __( 'Check to hide dates in posts.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_date_port', 'checkbox', 'display_options_section' )
			->default_value( false )
			->label( __( 'Hide portfolio date', 'portfolio' ) )
			->description( __( 'Check to hide dates in portfolio pages.', 'portfolio' ) );

		$portfolio_options->add_option( 'sticky_text' , 'text', 'display_options_section' )
			->default_value( 'Featured' )
			->label( __( 'Sticky post text:', 'portfolio' ) )
			->description( __( "Enter the text you would like to appear in the sticky post title. To remove the title completely, leave this option blank.", 'portfolio' ) )
			->preview_function( 'portfolioThemePreviewStickyText' );

		$portfolio_options->add_option( 'portfolio_item_button_label' , 'text', 'display_options_section' )
			->default_value( 'View project' )
			->label( __( 'Portfolio item button label:', 'portfolio' ) )
			->description( __( "Enter the text you would like to appear below the portfolio item description (on the Homepage page template). To remove the label completely, leave this option blank.", 'portfolio' ) );

		$portfolio_options->add_option( 'enable_lazyload', 'checkbox', 'display_options_section' )
			->default_value( true )
			->label( __( 'Fade images in', 'portfolio' ) )
			->description( __( 'Check to enable a fade effect when images load on screen. This effect will not be enabled for visitors using Internet Explorer 8 and below.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_portfolio_items', 'checkbox', 'display_options_section' )
			->default_value( true )
			->label( __( 'Remove portfolio items from blog/archives', 'portfolio' ) )
			->description( __( 'Check to remove all portfolio items from blog, archive, category and tag pages. Use this option if you don\'t want your portfolio items showing up as regular blog posts.', 'portfolio' ) );


		/* Footer & Subscribe links section
		 * ------------------------------------------------------------------ */

		$portfolio_options->add_option( 'twitter_url', 'text', 'subscribe_copyright_section' )
			->label( __( 'Twitter <abbr title="Universal resource locator">URL</abbr>:', 'portfolio' ) )
			->description( __( 'Enter your Twitter link.', 'portfolio' ) );

		$portfolio_options->add_option( 'facebook_url', 'text', 'subscribe_copyright_section' )
			->label( __( 'Facebook <abbr title="Universal resource locator">URL</abbr>:', 'portfolio' ) )
			->description( __( 'Enter your Facebook link.', 'portfolio' ) );

		$portfolio_options->add_option( 'google_url', 'text', 'subscribe_copyright_section' )
			->label( __( 'Google+ <abbr title="Universal resource locator">URL</abbr>:', 'portfolio' ) )
			->description( __( 'Enter your Google+ link.', 'portfolio' ) );

		$portfolio_options->add_option( 'flickr_url', 'text', 'subscribe_copyright_section' )
			->label( __( 'Flickr <abbr title="Universal resource locator">URL</abbr>:', 'portfolio' ) )
			->description( __( 'Enter your Flickr link.', 'portfolio' ) );

		$portfolio_options->add_option( 'hide_icons', 'checkbox', 'subscribe_copyright_section' )
			->default_value( false )
			->label( __( 'Disable all icons', 'portfolio' ) )
			->description( __( 'Check to hide all subscribe icons (including <abbr title="Really Simple Syndication">RSS</abbr>). This option overrides all other icon settings.', 'portfolio' ) );

		$portfolio_options->add_option( 'copyright_text' , 'text', 'subscribe_copyright_section' )
			->label( __( 'Copyright text:', 'portfolio' ) )
			->description( __( "Enter your copyright notice to be displayed in the theme footer.", 'portfolio' ) );
	}

endif; // portfolio_options_init

if ( ! function_exists( 'portfolio_option' ) ) :

	// Gets the value for a requested option.

	function portfolio_option( $option_name ) {
		global $portfolio_options;

		return $portfolio_options->get_value( $option_name );
	}

endif; // portfolio_option
