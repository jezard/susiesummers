<?php

if ( ! function_exists( 'portfolio_get_portfolio_pages' ) ) :

	function portfolio_get_portfolio_pages() {
		return get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'portfolio.php',
			'post_status' => 'draft,publish,pending,private',
			'hierarchical' => 0
		) );
	}

endif; // portfolio_get_portfolio_pages

if ( ! function_exists( 'portfolio_is_portfolio_page' ) ) :
	/**
	 * Returns true if $post_id is a portfolio page. False otherwise.
	 */
	function portfolio_is_portfolio_page( $post_id ) {
		$is_page = 'page' === get_post_type( $post_id );
		return ( $is_page && 'portfolio.php' === get_post_meta( $post_id, '_wp_page_template', true ) );
	}

endif; // portfolio_is_portfolio_page

if ( ! function_exists( 'portfolio_body_class' ) ) :
	/**
	 * Adds some custom classes to the body, based on the browser and theme options
	 */
	function portfolio_body_class( $classes = '' ) {
		$isIE7 = stristr( $_SERVER['HTTP_USER_AGENT'], "msie 7" );
		$isIE8 = stristr( $_SERVER['HTTP_USER_AGENT'], "msie 8" );

		if ( $isIE7 ) {
			$classes[] = 'IE7';
		} else if ( $isIE8 ) {
			$classes[] = 'IE8';
		}

		if ( portfolio_use_lazyloading() ) {
			$classes[] = 'lazyload-on';
		}

		return $classes;
	}

endif; // portfolio_body_class

if ( ! function_exists( 'portfolio_use_lazyloading' ) ) :
	/**
	 * Returns true if lazyloading should be enabled
	 */
	function portfolio_use_lazyloading() {
		$isIE7 = stristr( $_SERVER['HTTP_USER_AGENT'], "msie 7" );
		$isIE8 = stristr( $_SERVER['HTTP_USER_AGENT'], "msie 8" );

		return portfolio_option( 'enable_lazyload' ) && ! $isIE7 && ! $isIE8;
	}

endif; // portfolio_use_lazyloading

if ( ! function_exists( 'portfolio_has_edit_permissions' ) ) :
	/**
	 * Checks if the current user has the right permissions and nonce
	 * to edit this post/page
	 */
	function portfolio_has_edit_permissions( $post_id, $nonce_name ) {
		// Check if this is an auto save routine.
		// If it is the form has not been submitted, so we don't do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return false;

		// Check nonce was submitted properly

		if ( ! isset( $_POST['portfolio_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_nonce'], $nonce_name ) ) {
			return false;
		}

		// Check permissions
		$has_edit_permission = ( current_user_can( 'edit_post', $post_id ) );

		if ( ! $has_edit_permission ) {
			return false;
		}

		return true;
	}

endif; // portfolio_has_edit_permissions

if ( ! function_exists( 'portfolio_get_portfolio_item_class' ) ) :
	/**
	 * Generates the classes used on the portfolio page items
	 */
	function portfolio_get_portfolio_item_class( $counter ) {
		$portfolio_item_class = '';
		// Skip the first item
		if ( $counter > 0 ) {
			// The first item in a row in the 2-per-row (narrow) view
			if ( $counter % 2 == 0 ) {
				$portfolio_item_class .= ' first-when-two-per-row';
			}
			// The first item in a row in the 3-per-row (full-width) view
			if ( $counter % 3 == 0 ) {
				$portfolio_item_class .= ' first-when-three-per-row';
			}
			// The last item in a row in the 2-per-row (narrow) view
			if ( ( $counter + 1) % 2 == 0 ) {
				$portfolio_item_class .= ' last-when-two-per-row';
			}
			// The last item in a row in the 3-per-row (full-width) view
			if ( ( $counter + 1 ) % 3 == 0 ) {
				$portfolio_item_class .= ' last-when-three-per-row';
			}
		}

		return $portfolio_item_class;
	}

endif; // portfolio_get_portfolio_item_class

if ( ! function_exists( 'portfolio_get_truncated_excerpt' ) ) :
 	/**
 	 * Print the excerpt with a specific number of characters.
 	 */
	function portfolio_get_truncated_excerpt( $characters = null ) {
		$excerpt = get_the_excerpt();

		if ( empty( $excerpt ) ) {
	    	$excerpt = get_the_content();
		}

		$excerpt = preg_replace( " (\[.*?\])",'',$excerpt );
		$excerpt = strip_shortcodes( $excerpt );
		$excerpt = strip_tags( $excerpt );

		if ( strlen( $excerpt ) <= $characters ) {
			return '<p>' . $excerpt . '</p>';
		}

		$excerpt = substr( $excerpt, 0, $characters );
		if ( false !== strripos( $excerpt, " " ) ) {
			// If there is a space, cut off there.
			$excerpt = substr( $excerpt, 0, strripos( $excerpt, " " ) );
		}
		$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt) );
		return '<p>' . $excerpt . '&hellip;' . '</p>';
	}

endif; // portfolio_get_truncated_excerpt