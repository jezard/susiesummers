<?php
if ( ! function_exists( 'portfolio_add_page_option_box' ) ) :

	function portfolio_add_page_option_box() {
		add_meta_box(
			'portfolio_page_option_section',
			__( 'Portfolio Page Options', 'portfolio' ),
			'portfolio_page_option_box_html',
			'page',
			'side'
		);
	}

endif; // portfolio_add_page_option_box

if ( ! function_exists( 'portfolio_page_option_box_html' ) ) :
	/**
	 * Options for portfolio pages
	 */
	function portfolio_page_option_box_html( $page ) {
		// Use nonce for verification
		$portfolio_nonce = wp_nonce_field( 'portfolio_save_page', 'portfolio_nonce', true, false );

		// Save the original post so we can reset it if necessary
		global $post;
		$original_post = $post;

		$existing_items_query = portfolio_get_page_items_in_order( $page->ID );

		$other_items_query_args = array();

		$page_items_meta_key = PT_MetaKeys::PAGE_ITEMS;

		$items_on_page = get_post_meta( $page->ID, $page_items_meta_key, true );

		if ( empty( $items_on_page ) ) {
			$items_on_page = array();
		}

		if ( ! empty( $items_on_page ) ) {
			$other_items_query_args['post__not_in'] = $items_on_page;
		}

		$other_items_query = portfolio_get_portfolio_items_query( $other_items_query_args );

		// The actual fields for data entry
		?>
			<?php echo $portfolio_nonce; ?>
			<div class="portfolio-input-wrapper" id="portfolio-item-chooser">
				<h4 class="portfolio-header"><?php _e( 'Use this box to add, remove and sort your portfolio items on this page.', 'portfolio' ); ?></h4>
				<div class="no-items-at-all-section">
					<p><?php _e( 'You haven\'t created any portfolio items yet. To create a portfolio item, mark a standard Post as a <strong>portfolio item</strong> on the <em>Edit Post</em> screen.', 'portfolio' ); ?></p>
				</div>
				<div class="yes-on-page-section">
					<p id="yes-on-page-note"><?php _e( 'Drag-and-drop your portfolio items into order:', 'portfolio' ); ?></p>
					<p class="no-page-items"><?php _e( 'You don\'t have any portfolio items associated with this page. Available items are listed below.', 'portfolio' ); ?></p>
					<p class="no-page-items"><?php _e( 'If you don\'t see the item you\'re looking for, make sure it is marked as a <strong>portfolio item</strong> on the <em>Edit Post</em> screen.', 'portfolio' ); ?></p>
					<p id="drag-items-here-to-add"><?php _e( 'Drag items here to add them to the page:', 'portfolio' ); ?></p>
					<ul id="checked-page-items" class="droppable-list">
						<?php portfolio_output_page_portfolio_item_html( $existing_items_query, $items_on_page ); ?>
					</ul>
				</div>
				<div class="not-on-page-section">
					<hr id="page-item-separator">
					<h4 class="portfolio-header available-items"><?php _e( 'Available items', 'portfolio' ); ?></h4>
					<p id="not-on-page-note"><?php _e( 'Check to add to the list of active items above:', 'portfolio' ); ?></p>
					<p id="drag-items-here-to-remove">Drag items here to remove them from the page:</p>
					<ul id="unchecked-page-items" class="droppable-list">
						<?php portfolio_output_page_portfolio_item_html( $other_items_query, $items_on_page ); ?>
					</ul>
				</div>
			</div>
			<script type="text/javascript">
				var portfolio_page_items_meta_key = "<?php echo esc_js( $page_items_meta_key ); ?>";
			</script>
		<?php
		$post = $original_post; // We've modified the global post variable, so set it back here.
	}

endif; // portfolio_page_option_box_html

if ( ! function_exists( 'portfolio_output_page_portfolio_item_html' ) ) :
	/**
	 * Outputs the HTML for the lists of portfolio items in the page options meta box.
	 */
	function portfolio_output_page_portfolio_item_html( $query, $items_on_page ) {
	?>
		<?php if ( isset( $query ) && $query->have_posts() ) : ?>
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php
					$input_id = PT_MetaKeys::PAGE_ITEMS . "[" . get_the_ID() . "]";
					$is_checked = in_array( get_the_ID(), $items_on_page );

					$post_title = get_the_title();
					// We need a title for untitled posts
					if ( empty( $post_title ) ) {
						$post_title = __( 'Untitled', 'portfolio' ) . ' (ID ' . get_the_ID() . ')';
					}
				?>
				<li id="<?php echo "pageitem_" . get_the_ID(); ?>">
					<?php
						echo portfolio_get_meta_checkbox_html(
							$input_id,
							$is_checked,
							$post_title,
							false
						);
					?>
					<?php edit_post_link( __( 'edit', 'portfolio' ) ); ?>
				</li>
			<?php endwhile; ?>
		<?php endif; ?>
	<?php
	}

endif; // portfolio_output_page_portfolio_item_html

if ( ! function_exists( 'portfolio_save_page_meta' ) ) :
	/**
	 * Save meta data for a portfolio page
	 */
	function portfolio_save_page_meta( $page_id ) {

		// This function is only for pages
		if ( 'page' != get_post_type( $page_id ) )
			return;

		// Only save meta values for portfolio pages
		if ( 'portfolio.php' != get_post_meta( $page_id, '_wp_page_template', true ) )
			return;

		if ( ! portfolio_has_edit_permissions( $page_id, 'portfolio_save_page' ) )
			return;

		// OK, we're authenticated: we need to find and save the data

		if ( ! isset( $_POST[PT_MetaKeys::PAGE_ITEMS] ) ) {
			$page_item_data = array();
		} else {
			parse_str( $_POST[PT_MetaKeys::PAGE_ITEMS], $page_item_array );

			$page_item_data = $page_item_array['pageitem'];
		}

		portfolio_save_meta( $page_id, PT_MetaKeys::PAGE_ITEMS, $page_item_data );
	}

endif; // portfolio_save_page_meta

if ( ! function_exists( 'portfolio_prepend_portfolio_container' ) ) :
	/**
	 * Prepends Portfolio page content on save for portability reasons.
	 */
	function portfolio_prepend_portfolio_container( $post_id, $post ) {
		if ( portfolio_is_portfolio_page( $post_id ) )  {
			portfolio_update_portfolio_page_content( $post );
		}
	}

endif; // portfolio_prepend_portfolio_container

if ( ! function_exists( 'portfolio_filter_container_from_editor' ) ) :
	/**
	 * Filters the portfolio container from the_editor_content
	 */
	function portfolio_filter_container_from_editor( $content ) {
		if ( is_admin() && isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];

			if ( portfolio_is_portfolio_page( $post_id ) ) {
				$content = portfolio_strip_portfolio_container( $content );
			}
		}

		return $content;
	}

endif; // portfolio_filter_container_from_editor

if ( ! function_exists( 'portfolio_filter_container_from_content' ) ) :
	/**
	 * Filters the portfolio container from the_content
	 */
	function portfolio_filter_container_from_content( $content ) {
		if ( is_page_template( 'portfolio.php' ) ) {
			$content = portfolio_strip_portfolio_container( $content );
		}

		return $content;
	}

endif; // portfolio_filter_container_from_content

if ( ! function_exists( 'portfolio_strip_portfolio_container' ) ) :
	/**
	 * Strips the portfolio container (indicated by HTML comments) from $content
	 */
	function portfolio_strip_portfolio_container( $content ) {
		return preg_replace( '/<!--begin-portfolio-->.*<!--end-portfolio-->/s', '', $content );
	}

endif; // portfolio_strip_portfolio_container

if ( ! function_exists( 'portfolio_get_container_html' ) ) :
	/**
	 * Generates the HTML for the fallback container
	 */
	function portfolio_get_container_html( $page_id ) {
		$page_item_query = portfolio_get_page_items_in_order( $page_id, array( 'post_status' => 'publish' ) );

		$output = '';

		if ( $page_item_query && $page_item_query->have_posts() ) {
			$output .= "\n\n"; // Add a couple of line breaks to clear the existing content

			while ( $page_item_query->have_posts() ) {
				$page_item_query->the_post();

				if ( '' != get_the_post_thumbnail( get_the_ID() ) ) {
					$output .= "<a href='" . get_permalink() ."' class='portfolio-gallery-hidden'>";
					$output .= get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'attachment-thumbnail' ) );
					$output .= "</a>\n";
				}

				$output .= "<a href='" . get_permalink() ."' class='portfolio-gallery-hidden'>";
				$output .= get_the_title();
				$output .= "</a>\n\n";
			}
		}

		return $output;
	}

endif; // portfolio_get_container_html

if ( ! function_exists( 'portfolio_get_page_items_in_order' ) ) :
	/**
	 * Returns a WP_Query containing the page items for $page_id in the order they have been set.
	 * Returns NULL if no items are set.
	 */
	function portfolio_get_page_items_in_order( $page_id, $args = array() ) {
		$items_on_page = get_post_meta( $page_id, PT_MetaKeys::PAGE_ITEMS, true );

		if ( empty( $items_on_page ) ) {
			$items_on_page = array();
		}

		$existing_items_query = NULL;
		if ( ! empty( $items_on_page ) ) {
			$args = array_merge( $args, array( 'post__in' => $items_on_page ) );
			$existing_items_query = portfolio_get_portfolio_items_query( $args );

			$new_post_order = array();
			foreach ( $items_on_page as $item_id ) {
				foreach ( $existing_items_query->posts as $post ) {
					if ( $post->ID == $item_id ) {
						$new_post_order[] = $post;
						break;
					}
				}
			}

			$existing_items_query->posts = $new_post_order;
		}

		return $existing_items_query;
	}

endif; // portfolio_get_page_items_in_order

if ( ! function_exists( 'portfolio_update_portfolio_page_content' ) ) :
	/**
	 * Replaces the post_content value of $portfolio_page with the "hidden" container
	 * that will show if this theme is deactivated.
	 */
	function portfolio_update_portfolio_page_content( &$portfolio_page ) {
		// This function has been left empty to prevent conflicts with plugins.
		// This may be filled in with future updates if the conflicts can be resolved.
	}

endif; // portfolio_update_portfolio_page_content