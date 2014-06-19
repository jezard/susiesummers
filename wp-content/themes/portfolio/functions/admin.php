<?php

if ( ! function_exists( 'portfolio_enqueue_admin_scripts' ) ) :
	/**
	 * Enqueue any admin scripts to be served on the backend
	 */
	function portfolio_enqueue_admin_scripts() {
		wp_enqueue_script(
			'portfolio_admin_javascript',
			get_template_directory_uri() . '/javascripts/admin.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ),
			null
		);

		wp_enqueue_style(
			'portfolio_tinymce_plugin_css',
			get_template_directory_uri() . '/includes/tinymce/plugin/style.css',
			array(),
			null
		);

		wp_enqueue_style(
			'portfolio_admin_css',
			get_template_directory_uri() . '/includes/stylesheets/admin.css',
			array(),
			null
		);

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

endif; // portfolio_enqueue_admin_scripts

if ( ! function_exists( 'portfolio_admin_print_footer_excerpt_scripts' ) ) :
	/**
	 * Prints some JS used in the admin panel.
	 */
	function portfolio_admin_print_footer_excerpt_scripts() {
		$external_link_html = "<a class=\"view-item-link\" href=\"http://www.example.com/\">" . __( 'View project', 'portfolio' ) . "</a>";
		$blockquote_html = "<blockquote>\n\t<p>" . __( 'Quote text here.', 'portfolio' ) . "</p>\n\t<cite>" . __( 'Citation here', 'portfolio' ) . "</cite>\n</blockquote>";

		$helpful_html = '<p>' . __( 'To use these elements, just copy and paste the HTML provided into your excerpt and replace the URLs and text with your own values.' , 'portfolio' ) . '</p>';
		$helpful_html .= '<h4>' . __( 'External link styling:', 'portfolio' ) . '</h4>';
		$helpful_html .= '<pre class="portfolio-pre" lang="html">' . esc_html( $external_link_html ) . '</pre>';
		$helpful_html .= '<h4>' . __( 'Quotation with citation:', 'portfolio' ) . '</h4>';
		$helpful_html .= '<pre class="portfolio-pre" lang="html">' . esc_html( $blockquote_html ) . '</pre>';
?>
	<script type="text/javascript">
		var excerptHelpfulHTMLContent = <?php echo json_encode( $helpful_html ); ?>;
		jQuery('#postexcerpt .inside').append('<p><a id="portfolio-toggle-helpful-html" href="#">Click to show excerpt HTML help</a></p>');
		jQuery('#postexcerpt .inside').append('<div id="portfolio-excerpt-helpful-html"></div>');
		jQuery('#portfolio-excerpt-helpful-html').html(excerptHelpfulHTMLContent);
		jQuery('#portfolio-excerpt-helpful-html').hide();

		jQuery('#portfolio-toggle-helpful-html').click(function(){
			var htmlHelp = jQuery('#portfolio-excerpt-helpful-html');
			htmlHelp.toggle();
			if (htmlHelp.is(':visible')) {
				jQuery(this).text('<?php _e( 'Click to hide excerpt HTML help', 'portfolio' ); ?>');
			} else {
				jQuery(this).text('<?php _e( 'Click to show excerpt HTML help', 'portfolio' ); ?>');
			}
			return false;
		});
	</script>
<?php
	}

endif; // portfolio_admin_print_footer_excerpt_scripts

if ( ! function_exists( 'portfolio_admin_print_footer_pointer_scripts' ) ) :
	/**
	 * Print admin pointer JavaScript
	 */
	function portfolio_admin_print_footer_pointer_scripts() {
		$pointers = array();
		$note_text = '<strong>' . _x( 'Note:', 'noun: A notice', 'portfolio' ) . '</strong> ';

		$pointers['a#portfolio-item-info']['content'] = '<h3>' . __( 'Portfolio items', 'portfolio' ) . '</h3>';
		$pointers['a#portfolio-item-info']['content'] .= '<p>' . __( 'Portfolio items are given a special display and can be added to pages using the "Porfolio" page template.', 'portfolio' ) . '</p>';
		$pointers['a#portfolio-item-info']['content'] .= '<p>' . __( 'Use portfolio items for work that you want to be highlighted on your site.', 'portfolio' ) . '</p>';
		$pointers['a#portfolio-item-info']['content'] .= '<p>' . $note_text . sprintf( __( 'Only <strong>%s</strong> posts can be marked as portfolio items.', 'portfolio' ), _x( 'standard', 'A post format', 'portfolio' ) ) . '</p>';

		$pointers['a#portfolio-item-info']['position']['offset'] = "-25 0";
		$pointers['a#portfolio-item-info']['position']['align'] = "left";
		$pointers['a#portfolio-item-info']['position']['edge'] = "right";

		$pointers['a#portfolio-item-description-info']['content'] = '<h3>' . __( 'Portfolio item description', 'portfolio' ) . '</h3>';
		$pointers['a#portfolio-item-description-info']['content'] .= '<p>' . __( 'This should be a short description of your portfolio item. This description will be used below the featured image on the Homepage and Portfolio page templates.', 'portfolio' ) . '</p>';
		$pointers['a#portfolio-item-description-info']['content'] .= '<p>' . __( 'If you don\'t add a description here, the excerpt will be used instead.', 'portfolio' ) . '</p>';

		$pointers['a#portfolio-item-description-info']['position']['offset'] = "-25 0";
		$pointers['a#portfolio-item-description-info']['position']['align'] = "left";
		$pointers['a#portfolio-item-description-info']['position']['edge'] = "right";
	?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			<?php $counter = 0; ?>
			<?php foreach ( $pointers as $selector => $data ) : ?>
				$(<?php echo json_encode( $selector ); ?>).on('click', function(){
					$('.ttf-pointer').hide();
					var thisPointerClass = 'ttf-pointer-' + '<?php echo $counter++; ?>';
					$(this).pointer({
						pointerClass: 'wp-pointer ttf-pointer ' + thisPointerClass,
						content: <?php echo json_encode( $data['content'] ); ?>,
						position: <?php echo json_encode( $data['position'] ); ?>
					}).pointer('open');
					$(this).off('click');
					$(this).on('click', function() {
						$('.ttf-pointer').not(this).hide();
						$('.' + thisPointerClass).show();
						return false;
					});
					return false;
				});
			<?php endforeach; ?>
		});
		//]]>
		</script>
	<?php
	}

endif; // portfolio_admin_print_footer_pointer_scripts
