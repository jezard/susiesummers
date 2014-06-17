<?php

class PT_MetaKeys {
	const FEATURED_SLIDER = 'portfolio_featured_slider';
	const IS_PORTFOLIO_ITEM = 'portfolio_portfolio_item';
	const ITEM_DESCRIPTION = 'portfolio_portfolio_item_description';
	const PAGE_ITEMS = 'portfolio_page_portfolio_items';
}

if ( ! function_exists( 'portfolio_add_post_option_box' ) ) :

	function portfolio_add_post_option_box() {
		add_meta_box(
			'portfolio_post_option_section',
			__( 'Portfolio Theme Post Options', 'portfolio' ),
			'portfolio_post_option_box_html',
			'post',
			'side'
		);
	}

endif; // portfolio_add_post_option_box

if ( ! function_exists( 'portfolio_save_meta' ) ) :
	/**
	 * Small convenience function for saving boolean meta values
	 */
	function portfolio_save_meta( $post_id, $meta_key, $meta_value ) {
		add_post_meta( $post_id, $meta_key, $meta_value, true ) or update_post_meta( $post_id, $meta_key, $meta_value );
	}

endif; // portfolio_save_meta

if ( ! function_exists( 'portfolio_post_option_box_html' ) ) :
	/**
	 * Add a checkbox to include the post in the featured slider,
	 * and also some options for making this post a "portfolio item"
	 */
	function portfolio_post_option_box_html( $post ) {
		// Use nonce for verification
		$portfolio_nonce = wp_nonce_field( 'portfolio_save_post', 'portfolio_nonce', true, false );
		$portfolio_item_details = get_post_meta( $post->ID, PT_MetaKeys::ITEM_DESCRIPTION, true );
		$portfolio_pages = portfolio_get_portfolio_pages();

		// The actual fields for data entry
		?>
			<?php echo $portfolio_nonce; ?>
			<?php
				$is_featured_checked = get_post_meta( $post->ID, PT_MetaKeys::FEATURED_SLIDER, true );

				echo portfolio_get_meta_checkbox_html(
					PT_MetaKeys::FEATURED_SLIDER,
					$is_featured_checked,
					__( "Show in featured slider", 'portfolio' )
				);
			?>
			<div id="portfolio-options">
				<?php
					$is_portfolio_item_checked = portfolio_is_portfolio_item( $post->ID );

					echo portfolio_get_meta_checkbox_html(
						PT_MetaKeys::IS_PORTFOLIO_ITEM,
						$is_portfolio_item_checked,
						__( "This is a portfolio item", 'portfolio' ),
						true,
						'portfolio-item-info'
					);
				?>
				<div class="portfolio-section-wrapper" id="portfolio-item-meta">
					<div class="portfolio-input-wrapper">
						<label for="<?php echo esc_attr( PT_MetaKeys::ITEM_DESCRIPTION ); ?>">
							<?php _e( "Portfolio item description", 'portfolio' ); ?>
						</label>
						<a id="portfolio-item-description-info" class="portfolio-help-link" href="#">(?)</a>
						<textarea id="<?php echo esc_attr( PT_MetaKeys::ITEM_DESCRIPTION ); ?>" name="<?php echo esc_attr( PT_MetaKeys::ITEM_DESCRIPTION ); ?>"><?php echo esc_html( $portfolio_item_details ); ?></textarea>
					</div>
					<?php if ( count( $portfolio_pages ) > 0 ) : ?>
						<p><?php _e( 'Check to include this post in the following portfolio pages:', 'portfolio' ); ?></p>
						<?php
							// Assign $meta_key variable for readability
							$meta_key = PT_MetaKeys::PAGE_ITEMS;

							foreach ( $portfolio_pages as $portfolio_page ) {
								// Get the IDs of posts on this portfolio page
								$items_on_page = get_post_meta( $portfolio_page->ID, $meta_key, true );

								$is_checked = ! empty( $items_on_page ) && in_array( $post->ID, $items_on_page );

								// We need a title for untitled posts
								if ( empty( $portfolio_page->post_title ) ) {
									$page_title = __( 'Untitled', 'portfolio' ) . ' (ID ' . $portfolio_page->ID . ')';
								} else {
									$page_title = $portfolio_page->post_title;
								}

								echo portfolio_get_meta_checkbox_html(
									$meta_key . "[$portfolio_page->ID]",
									$is_checked,
									$page_title,
									false
								);

								echo "<br>";
							}
						?>
					<?php else: ?>
						<p><?php _e( 'You don\'t have any pages using the <strong>Portfolio</strong> page template yet!', 'portfolio' ); ?></p>
						<p>
							<?php
								$existing_pages_link = '<a href="' . esc_url( get_admin_url( 0, 'edit.php?post_type=page' ) ) . '">' . __( 'existing pages', 'portfolio' ) . '</a>';
								$create_page_link = '<a href="' . esc_url( get_admin_url( 0, 'post-new.php?post_type=page') ) . '">' . __( 'create a new page', 'portfolio' ) . '</a>';
								printf( __( 'Update one of your %s or %s and select the <strong>Portfolio</strong> page template, then return here to add your item.', 'portfolio' ), $existing_pages_link, $create_page_link );
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php
	}

endif; // portfolio_post_option_box_html

if ( ! function_exists( 'portfolio_get_meta_checkbox_html' ) ) :
	/**
	 * Generates the HTML for our standard meta checkboxes
	 */
	function portfolio_get_meta_checkbox_html( $input_name, $is_checked, $label_text, $add_wrapper=true, $help_link_id=false ) {
		$checked = $is_checked ? 'checked="checked"' : '';
	?>
		<?php if ( $add_wrapper ) : ?>
			<div class="portfolio-input-wrapper">
		<?php endif; ?>
				<input type="checkbox" id="<?php echo esc_attr( $input_name ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="1" <?php echo $checked; ?> />
				<label for="<?php echo esc_attr( $input_name ); ?>">
					<?php echo $label_text; ?>
				</label>
				<?php if ( $help_link_id ) : ?>
					<a id="<?php echo esc_attr( $help_link_id ); ?>" class="portfolio-help-link" href="#">(?)</a>
				<?php endif; ?>
		<?php if ( $add_wrapper ) : ?>
			</div>
		<?php endif; ?>
	<?php
	}

endif; // portfolio_get_meta_checkbox_html

if ( ! function_exists( 'portfolio_save_post_meta' ) ) :
	/**
	 * Check for and save our custom field for adding a post to the featured slider
	 */
	function portfolio_save_post_meta( $post_id ) {
		// This function is only for posts
		if ( 'post' != get_post_type( $post_id ) )
			return;

		if ( ! portfolio_has_edit_permissions( $post_id, 'portfolio_save_post' ) )
			return;

		// OK, we're authenticated: we need to find and save the data

		// Any post can be added to the featured slider
		portfolio_save_slider_meta( $post_id );

		// Only standard posts can be portfolio items
		if ( '' == get_post_format( $post_id ) ) {
			portfolio_save_portfolio_item_meta( $post_id );
		} else {
			// Delete the meta info if this post is not standard
			portfolio_delete_post_portfolio_item_meta( $post_id );
		}
	}

endif; // portfolio_save_post_meta

if ( ! function_exists( 'portfolio_save_slider_meta' ) ) :
	/**
	 * Save meta information for the featured slider
	 */
	function portfolio_save_slider_meta( $post_id ) {
		// Add or remove the post from the featured slider
		$set_featured_slider = isset( $_POST[PT_MetaKeys::FEATURED_SLIDER] ) && (boolean) $_POST[PT_MetaKeys::FEATURED_SLIDER];

		portfolio_save_meta( $post_id, PT_MetaKeys::FEATURED_SLIDER, $set_featured_slider );
	}

endif; // portfolio_save_slider_meta

if ( ! function_exists( 'portfolio_save_portfolio_item_meta' ) ) :
	/**
	 * Save the fields related to portfolio items on a post
	 */
	function portfolio_save_portfolio_item_meta( $post_id ) {
		$is_portfolio_item = isset( $_POST[PT_MetaKeys::IS_PORTFOLIO_ITEM] ) && (boolean) $_POST[PT_MetaKeys::IS_PORTFOLIO_ITEM];

		portfolio_save_meta( $post_id, PT_MetaKeys::IS_PORTFOLIO_ITEM, $is_portfolio_item );

		portfolio_save_meta( $post_id, PT_MetaKeys::ITEM_DESCRIPTION, $_POST[PT_MetaKeys::ITEM_DESCRIPTION] );

		$portfolio_pages = portfolio_get_portfolio_pages();

		if ( $is_portfolio_item ) {
			foreach ( $portfolio_pages as $portfolio_page ) {
				// If this is a portfolio item, we can save the related portfolio pages
				$meta_key = PT_MetaKeys::PAGE_ITEMS;

				$items_on_page = get_post_meta( $portfolio_page->ID, $meta_key, true );

				// If there are no values set, we need an empty array
				if ( '' == $items_on_page ) { $items_on_page = array(); }

				$page_is_changed = false;
				$page_was_checked = isset( $_POST[$meta_key][$portfolio_page->ID] ) && (boolean) $_POST[$meta_key][$portfolio_page->ID];

				// We only want to update the page meta and content if the values have changed
				if ( $page_was_checked && ! in_array( $post_id, $items_on_page ) ) {
					// Prepend new item
					array_unshift( $items_on_page, $post_id );
					$page_is_changed = true;
				} elseif ( ! $page_was_checked && in_array( $post_id, $items_on_page ) ) {
					$items_on_page = array_diff( $items_on_page, array( $post_id ) );
					$page_is_changed = true;
				}

				if ( $page_is_changed ) {
					portfolio_save_meta( $portfolio_page->ID, $meta_key, $items_on_page );
					portfolio_update_portfolio_page_content( $portfolio_page );
				}
			}
		} else {
			// Otherwise, we need to delete the portolio page meta as well
			portfolio_delete_post_portfolio_item_meta( $post_id );
		}
	}

endif; // portfolio_save_portfolio_item_meta

if ( ! function_exists( 'portfolio_delete_post_portfolio_item_meta' ) ) :
	/**
	 * Delete all portfolio item information - done for non-standard posts
	 * and standard posts that are no longer portfolio items.
	 */
	function portfolio_delete_post_portfolio_item_meta( $post_id ) {
		delete_post_meta( $post_id, PT_MetaKeys::IS_PORTFOLIO_ITEM );

		delete_post_meta( $post_id, PT_MetaKeys::ITEM_DESCRIPTION );

		$portfolio_pages = portfolio_get_portfolio_pages();

		portfolio_remove_post_from_portfolio_pages( $post_id, $portfolio_pages );
	}

endif; // portfolio_delete_post_portfolio_item_meta

if ( ! function_exists( 'portfolio_remove_post_from_portfolio_pages' ) ) :
	/**
	 * Convenience function for removing a post from ALL portfolio pages.
	 * Used when a standard post is no longer marked as a portfolio item.
	 */
	function portfolio_remove_post_from_portfolio_pages( $post_id, $portfolio_pages ) {
		foreach ( $portfolio_pages as $portfolio_page ) {
			$input_id = PT_MetaKeys::PAGE_ITEMS;

			$items_on_page = get_post_meta( $portfolio_page->ID, $input_id, true );

			// If there are no values set, we need an empty array
			if ( '' == $items_on_page ) { $items_on_page = array(); }

			// We only want to update the page meta and content if the values have changed
			if ( in_array( $post_id, $items_on_page ) ) {
				$items_on_page = array_diff( $items_on_page, array( $post_id ) );

				portfolio_save_meta( $portfolio_page->ID, $input_id, $items_on_page );

				portfolio_update_portfolio_page_content( $portfolio_page );
			}
		}
	}

endif; // portfolio_remove_post_from_portfolio_pages

if ( ! function_exists( 'portfolio_get_filtered_description' ) ) :
	/**
	 * Get description filtered for output in the details sidebar
	 * on individual portfolio items.
	 */
	function portfolio_get_filtered_description( $post_id=0 ) {
		if ( 0 == $post_id ) {
			$post_id = get_the_ID();
		}

		$description = get_post_meta( $post_id, PT_MetaKeys::ITEM_DESCRIPTION, true );

		$description = strip_tags( $description );

		return $description;
	}

endif; // portfolio_get_filtered_description

if ( ! function_exists( 'portfolio_show_portfolio_item_sidebar' ) ) :
	/**
	 * Returns a boolean that indicates whether or not
	 * the sidebar-portfolio-item should be shown on $post_id.
	 */
	function portfolio_show_portfolio_item_sidebar( $post_id=0 ) {
		return has_excerpt( $post_id );
	}

endif; // portfolio_show_portfolio_item_sidebar

if ( ! function_exists( 'portfolio_is_portfolio_item' ) ) :
	/**
	 * Returns true if $post_id is a portfolio item
	 */
	function portfolio_is_portfolio_item( $post_id=0 ) {
		if ( 0 == $post_id ) {
			$post_id = get_the_ID();
		}

		return (boolean) get_post_meta( $post_id, PT_MetaKeys::IS_PORTFOLIO_ITEM, true );
	}

endif; // portfolio_is_portfolio_item

if ( ! function_exists( 'portfolio_get_portfolio_items_in_order' ) ) :
	/**
	 * Gets the posts with IDs in $items_on_page *in the order they appear*
	 */
	function portfolio_get_portfolio_items_in_order( $items_on_page ) {
		$portfolio_query = NULL;

		// If there are values set, do a query
		if ( ! empty( $items_on_page ) ) {
			$portfolio_query = portfolio_get_portfolio_items_query( array( 'post__in' => $items_on_page ) );

			$new_post_order = array();
			foreach ( $items_on_page as $item_id ) {
				foreach ( $portfolio_query->posts as $post ) {
					if ( $post->ID == $item_id ) {
						$new_post_order[] = $post;
						break;
					}
				}
			}

			$portfolio_query->posts = $new_post_order;
		}

		return $portfolio_query;
	}

endif; // portfolio_get_portfolio_items_in_order

if ( ! function_exists( 'portfolio_set_post_thumbnail' ) ) :
	/**
	 * Updates the Portfolio pages' content if the post being updated is a portfolio item
	 */
	function portfolio_set_post_thumbnail( $meta_id, $object_id, $meta_key, $_meta_value ) {
		if ( '_thumbnail_id' == $meta_key && portfolio_is_portfolio_item( $object_id ) ) {
			$portfolio_pages = portfolio_get_portfolio_pages();

			foreach ( $portfolio_pages as $portfolio_page ) {
				$items_on_page = get_post_meta( $portfolio_page->ID, PT_MetaKeys::PAGE_ITEMS, true );

				// We only want to update the page meta and content if the values have changed
				if ( '' != $items_on_page && in_array( $object_id, $items_on_page ) ) {
					portfolio_update_portfolio_page_content( $portfolio_page );
				}
			}
		}
	}

endif; // portfolio_set_post_thumbnail