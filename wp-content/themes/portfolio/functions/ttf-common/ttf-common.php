<?php
/**
 * A library of common functions used in many of our themes.
 * NOTE: Not all of these may be used in this theme.
 */

if ( ! function_exists( 'ttf_common_get_meta_checkbox_html' ) ) :
	/**
	 * Generates the HTML for our standard meta checkboxes
	 */
	function ttf_common_get_meta_checkbox_html( $input_name, $is_checked, $label_text, $add_wrapper=true, $help_link_id=false ) {
		$checked = $is_checked ? ' checked="checked"' : '';
	?>
		<?php if ( $add_wrapper ) : ?>
			<div class="ttf-common-input-wrapper">
		<?php endif; ?>
				<input type="checkbox" id="<?php echo esc_attr( $input_name ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="1"<?php echo $checked; ?> />
				<label for="<?php echo esc_attr( $input_name ); ?>">
					<?php echo $label_text; ?>
				</label>
				<?php if ( $help_link_id ) : ?>
					<a id="<?php echo esc_attr( $help_link_id ); ?>" style="text-decoration:none;" href="#">(?)</a>
				<?php endif; ?>
		<?php if ( $add_wrapper ) : ?>
			</div>
		<?php endif; ?>
	<?php
	}

endif; // ttf_common_get_meta_checkbox_html

if ( ! function_exists( 'ttf_common_save_meta' ) ) :
	/**
	 * Small convenience function for saving boolean meta values
	 */
	function ttf_common_save_meta( $post_id, $meta_key, $meta_value ) {
		add_post_meta( $post_id, $meta_key, $meta_value, true ) or update_post_meta( $post_id, $meta_key, $meta_value );
	}

endif; // ttf_common_save_meta

if ( ! function_exists( 'ttf_common_page_title' ) ) :
	/**
	 * Adjust the page title to something sensible
	 */
	function ttf_common_page_title( $title ) {
		// We don't want to affect RSS feeds
		if ( is_feed() ) { return; }

		if ( is_front_page() ) {
			return get_bloginfo( 'name' );
		} elseif ( is_404() ) {
			return __( 'Page Not Found | ', 'ttf_common' ) . get_bloginfo( 'name' );
		} elseif ( is_search() ) {
			return __( 'Search results | ', 'ttf_common' ) . get_bloginfo( 'name' );
		} else {
			return trim( $title ) . ' | ' . get_bloginfo( 'name' );
		}
	}

endif; // ttf_common_page_title

if ( ! function_exists( 'ttf_common_archives_title' ) ) :
	/**
	 * Adjust the archives page title to something sensible
	 */
	function ttf_common_archives_title() {
		if ( is_category() ) { /* If this is a category archive */
			printf( __( 'Posts from the &#8216%s&#8217 category', 'ttf_common' ), single_cat_title( '', false ) );
		} elseif ( is_tag() ) { /* If this is a tag archive */
			printf( __( 'Posts tagged &#8216%s&#8217', 'ttf_common' ), single_tag_title( '', false ) );
		} elseif ( is_day() ) { /* If this is a daily archive */
			printf( __( 'Archive for &#8216%s&#8217', 'ttf_common' ), get_the_time( 'F jS, Y' ) );
		} elseif ( is_month() ) { /* If this is a monthly archive */
			printf( __( 'Archive for &#8216%s&#8217', 'ttf_common' ), get_the_time( 'F, Y' ) );
		} elseif ( is_year() ) { /* If this is a yearly archive */
			printf( __( 'Archive for &#8216%s&#8217', 'ttf_common' ), get_the_time( 'Y' ) );
		} elseif ( is_author() ) { /* If this is an author archive */
			printf( __( 'Posts by %s', 'ttf_common' ), get_the_author() );
		} elseif ( is_paged() ) { /* If this is a paged archive */
			_e( 'Blog Archives', 'ttf_common' );
		}
	}

endif; // ttf_common_archives_title

if ( ! function_exists( 'ttf_common_gallery_display' ) ) :
	/**
	 * Replaces the [gallery] output with flexslider if the slider attribute is "true"
	 */
	function ttf_common_gallery_display( $output, $attr ) {
		global $post;

		if ( isset( $attr['slider'] ) ) {
			$post_id = isset( $attr['id'] ) ? $attr['id'] : $post->ID;

			$query_args = array(
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'numberposts' => 999
			);

			if ( isset( $attr['include'] ) ) {
				$query_args['post__in'] = explode( ',', $attr['include'] );
				// If specific attachments are set, we can remove the post_parent requirement
				$query_args['post_parent'] = NULL;
			} elseif ( isset( $attr['exclude'] ) ) {
				$query_args['post__not_in'] = explode( ',', $attr['exclude'] );
			}

			if ( isset( $attr['orderby'] ) ) {
				$query_args['orderby'] = $attr['orderby'];
			}

			$images = get_children( $query_args );

			if ( $images ) {
				$autostart = isset( $attr['autostart'] ) ? ' data-autostart="true"' : '';

				if ( defined( 'TTF_USE_RESPONSIVESLIDES' ) && TTF_USE_RESPONSIVESLIDES ) {
					$slider_classes = array( 'responsive-slides' );
				} else {
					$slider_classes = array( 'gallery-flexslider', 'flexslider' );
				}

				$output = '<div class="' . implode( ' ', $slider_classes ) . '"' . $autostart . '>';
				$output .= '<ul class="slides">';

				foreach ( $images as $image ) {
					$image_html = wp_get_attachment_image( $image->ID, 'large' );

					if ( isset( $attr['link'] ) && in_array( $attr['link'], array( 'file', 'attachment' ) ) ) {
						switch ( $attr['link'] ) {
							case 'attachment':
								$link = get_attachment_link( $image->ID );
								break;
							default:
								$image_array = wp_get_attachment_image_src( $image->ID, 'full' );
								$link = $image_array[0];
								break;
						}

						$output .= '<li><a href="' . esc_url( $link ) . '">' . $image_html . '</a></li>';
					} else {
						$output .= '<li>' . $image_html . '</li>';
					}
				}

				$output .= '</ul>';
				if ( defined( 'TTF_USE_RESPONSIVESLIDES' ) && TTF_USE_RESPONSIVESLIDES ) {
					$output .= '<ul class="rslides-direction-nav"></ul>';
				}
				$output .= '</div><!-- /.slider -->';
			}
		}

		return $output;
	}

endif; // ttf_common_gallery_display

if ( ! function_exists( 'ttf_common_remove_password_posts' ) ) :
	/**
	 * Remove passworded posts from the normal listing
	 */
	function ttf_common_remove_password_posts( $where = '' ) {
		if ( ! is_admin() && ! is_singular() ) {
			// exclude password protected
			$where .= " AND post_password = ''";
		}

		return $where;
	}

endif; // ttf_common_remove_password_posts

if ( ! function_exists( 'ttf_common_list_pings' ) ) :
	/**
	 * Display for pings on a blog
	 */
	function ttf_common_list_pings( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
		<?php
	}

endif; // ttf_common_list_pings

if ( ! function_exists( 'ttf_common_body_class' ) ) :
	/**
	 * Adds classes to the body for compatibilty fixes
	 */
	function ttf_common_body_class( $classes = '' ) {
		if ( stristr( $_SERVER['HTTP_USER_AGENT'], "msie 7" ) ) {
			$classes[] = 'IE7';
		} else if ( stristr( $_SERVER['HTTP_USER_AGENT'], "msie 8" ) ) {
			$classes[] = 'IE8';
		} else if ( stristr( $_SERVER['HTTP_USER_AGENT'], "msie 9" ) ) {
			$classes[] = 'IE9';
		} else if ( stristr( $_SERVER['HTTP_USER_AGENT'], "opera" ) ) {
			$classes[] = 'opera';
		}

		return $classes;
	}

endif; // ttf_common_body_class

if ( ! function_exists( 'ttf_common_link_rel_next' ) ) :
	/**
	 * Add rel="next" to next links
	 */
	function ttf_common_link_rel_next( $attr ) {
		return implode( ' ', array( $attr, 'rel="next"' ) );
	}

endif; // ttf_common_link_rel_next

if ( ! function_exists( 'ttf_common_link_rel_prev' ) ) :
	/**
	 * Add rel="prev" to prev links
	 */
	function ttf_common_link_rel_prev( $attr ) {
		return implode( ' ', array( $attr, 'rel="prev"' ) );
	}

endif; // ttf_common_link_rel_prev

if ( ! function_exists( 'ttf_common_get_post_audio' ) ) :
	/**
	 * Get audio attachments for the current $post
	 */
	function ttf_common_get_post_audio() {
		$audio = get_children( array(
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'post_mime_type' => 'audio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC'
		) );

		return $audio;
	}

endif; // ttf_common_get_post_audio

if ( ! function_exists( 'ttf_get_font_styles' ) ) :
	/**
	 * Returns font CSS based on body and accent font inputs.
	 *
	 * @param string $body_font Body font to use in CSS
	 * @param string $accent_font Accent font to use in CSS
	 * @param string $accent_font_selectors Comma-separated selectors
	 * @param array $mono_fonts Array of fonts to use the mono fallback stack
	 * @param array $sans_serif_fonts Array of fonts to use the mono fallback stack
	 *
	 * @return string CSS for body and accent fonts
	 */
	function ttf_get_font_styles( $body_font, $accent_font, $accent_font_selectors, $mono_fonts, $sans_serif_fonts ) {
		$styles = '';

		// Fallback stacks for each style of font
		$mono_stack = "Monaco, 'Courier New', Courier, monospace";
		$sans_serif_stack = "'Helvetica Neue', Helvetica, Verdana, Tahoma, sans-serif";
		$serif_stack = "Georgia, Cambria, 'Times New Roman', Times, serif";

		if ( $accent_font ) {
			$accent_stack = $serif_stack;

			if ( in_array( $accent_font, $sans_serif_fonts ) ) {
				$accent_stack = $sans_serif_stack;
			} elseif ( in_array( $accent_font, $mono_fonts ) ) {
				$accent_stack = $mono_stack;
			}

			$styles .= "$accent_font_selectors { font-family: '$accent_font', $accent_stack; }\n";
		}

		if ( $body_font ) {
			$body_stack = $serif_stack;

			if ( in_array( $body_font, $sans_serif_fonts ) ) {
				$body_stack = $sans_serif_stack;
			} elseif ( in_array( $body_font, $mono_fonts ) ) {
				$body_stack = $mono_stack;
			}

			$styles .= "body { font-family: '$body_font', $body_stack; }\n";
		}

		return $styles;
	}

endif; // ttf_get_font_styles

if ( ! function_exists( 'ttf_common_add_admin_pointer' ) ) :
	/**
	 *
	 */
	function ttf_common_add_admin_pointer( $location_selector, $pagenow, $typenow, $content, $position, $auto_show=false, $handle=NULL ) {
		global $ttf_admin_pointers;

		if ( ! isset( $ttf_admin_pointers ) ) {
			$ttf_admin_pointers = array();
		}

		$new_pointer = array(
			'selector' => $location_selector,
			'pagenow' => $pagenow,
			'typenow' => $typenow,
			'pointer' => array(
				'content' => $content,
				'position' => $position
			),
			'auto_show' => $auto_show
		);

		if ( isset( $handle ) ) {
			$new_pointer['pointer']['handle'] = $handle;
		}

		$ttf_admin_pointers[] = $new_pointer;
	}

endif; // ttf_common_add_admin_pointer

if ( ! function_exists( 'ttf_common_admin_print_footer_pointer_scripts' ) ) :
	/**
	 * Print admin pointer JavaScript
	 */
	function ttf_common_admin_print_footer_pointer_scripts() {
		global $ttf_admin_pointers;
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		global $pagenow, $typenow;

		$current_page_pointers = array();

		if ( ! empty( $ttf_admin_pointers ) ) {
			foreach ( $ttf_admin_pointers as $admin_pointer ) {
				if ( in_array( $pagenow, $admin_pointer['pagenow'] ) && $admin_pointer['typenow'] == $typenow ) {
					$current_page_pointers[] = $admin_pointer;
				}
			}
		}

		if ( ! empty( $current_page_pointers ) ) :
	?>
		<script type="text/javascript">
		//<![CDATA[
		(function($){
			// window.load because we need to wait for TinyMCE to finish loading
			$(window).load( function() {
				$('html').click(function() {
					$('.ttf-pointer').fadeOut();
				});

				$('body').on('click', '.ttf-pointer', function() {
					$('.ttf-pointer').click(function(event){
						event.stopPropagation();
					});
				});

				$('body').on('click', '.ttf-pointer .close', function(){
					$(this).hide();
				});

				(function(){
					<?php ttf_common_common_pointer_javascript(); ?>
					<?php
						$counter = 0;
						foreach ( $current_page_pointers as $pointer ) {
							if ( in_array( $pointer['pointer']['handle'], $dismissed ) ) { continue; }
							?>
							var selector = <?php echo json_encode( $pointer['selector'] ); ?>,
								data = <?php echo json_encode( $pointer['pointer'] ); ?>,
								counter = <?php echo json_encode( $counter ); ?>,
								pointerClass = 'ttf-pointer-' + counter;

							<?php
							if ( $pointer['auto_show'] ) {
								ttf_common_auto_show_pointer_javascript();
							} else {
								ttf_common_click_to_show_pointer_javascript();
							}

							$counter++;
						}
					?>
				})();
			});
		})(jQuery);
		//]]>
		</script>
	<?php
		endif;
	}

endif; // ttf_common_admin_print_footer_pointer_scripts

if ( ! function_exists( 'ttf_common_common_pointer_javascript' ) ) :
	/**
	 *
	 */
	function ttf_common_common_pointer_javascript() {
		?>
		var onPointerClick = function(args){
			createAndOpenPointer(args);

			$('.ttf-pointer').not('.' + args.pointerClass).hide();

			return false;
		};

		var createAndOpenPointer = function(args) {
			var pointerArgs = {
				pointerClass: 'wp-pointer ttf-pointer ' + args.pointerClass,
				content: args.data.content,
				position: args.data.position
			};

			if ( args.isDismissable ) {
				pointerArgs.close = function() {
					jQuery.post( ajaxurl, {
						pointer: args.data.handle,
						action: 'dismiss-wp-pointer'
					});
					return false;
				};
			}

			$(args.selector).pointer(pointerArgs).pointer('open');
		};
		<?php
	}

endif; // ttf_common_common_pointer_javascript

if ( ! function_exists( 'ttf_common_auto_show_pointer_javascript' ) ) :
	/**
	 * JavaScript for powering admin pointers that automatically show
	 */
	function ttf_common_auto_show_pointer_javascript() {
		?>
		var pointerArgs = {
			pointerClass: pointerClass,
			selector: selector,
			data: data,
			isDismissable: true
		};

		createAndOpenPointer(pointerArgs);
		<?php
	}

endif; // ttf_common_auto_show_pointer_javascript

if ( ! function_exists( 'ttf_common_click_to_show_pointer_javascript' ) ) :
	/**
	 * JavaScript for powering admin pointers that require a click to show
	 */
	function ttf_common_click_to_show_pointer_javascript() {
		?>
		var pointerArgs = {
			pointerClass: pointerClass,
			selector: selector,
			data: data
		};

		// The link/button someone must click to show the pointer.
		// Uses some anon function callback madness that passes the current values to the onPointerClick function.
		$(selector).on('click', (function(args){
			return function() {
				onPointerClick(args);
				return false;
			};
		})(pointerArgs));

		// Hide this pointer
		$(pointerClass).hide();
		<?php
	}

endif; // ttf_common_click_to_show_pointer_javascript

if ( ! function_exists( 'ttf_common_get_truncated_string' ) ) :
	/**
	 * Returns $string truncated to $num_characters characters
	 */
	function ttf_common_get_truncated_string( $string, $num_characters ) {
		$string = str_replace( '&nbsp;', ' ', $string );

		if ( strlen( $string ) <= $num_characters ) {
			return $string;
		}

		$string = substr( $string, 0, $num_characters );
		if ( false !== strripos( $string, " " ) ) {
			// If there is a space, cut off there.
			$string = substr( $string, 0, strripos( $string, " " ) );
		}
		$string = trim( preg_replace( '/\s+/', ' ', $string) );
		return $string . '&hellip;';
	}

endif; // ttf_common_get_truncated_string

if ( ! function_exists( 'ttf_common_get_truncated_excerpt' ) ) :
	/**
	 * Get the excerpt with a specific number of characters.
	 */
	function ttf_common_get_truncated_excerpt( $num_characters ) {
		add_filter( 'excerpt_length', 'ttf_common_full_excerpt_length', 1000 );
		$excerpt = get_the_excerpt();
		remove_filter( 'excerpt_length', 'ttf_common_full_excerpt_length', 1000 );

		if ( empty( $excerpt ) ) {
			$excerpt = get_the_content();
		}

		$excerpt = preg_replace( " (\[.*?\])",'',$excerpt );
		$excerpt = strip_shortcodes( $excerpt );
		$excerpt = strip_tags( $excerpt );

		$excerpt = ttf_common_get_truncated_string( $excerpt, $num_characters );

		return '<p>' . $excerpt . '</p>';
	}

endif; // ttf_common_get_truncated_excerpt

if ( ! function_exists( 'ttf_common_full_excerpt_length' ) ) :
	/**
	 * Sets the excerpt length to a very long value, so we don't get cut short when
	 * trying to generate our own character-length snippet.
	 */
	function ttf_common_full_excerpt_length( $length ) {
		return 400;
	}

endif; // ttf_common_full_excerpt_length