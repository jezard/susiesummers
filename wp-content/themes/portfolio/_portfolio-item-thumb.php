<a href="<?php the_permalink(); ?>" rel="bookmark">
	<div id="portfolio-item-<?php the_ID(); ?>" class="portfolio-thumb<?php echo portfolio_get_portfolio_item_class( $counter ); ?>">
		<div class="thumbnail-image">
			<span class="thumb-icon">E</span>
		</div>
		<?php if ( '' != get_the_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'portfolio_page_thumb' ); ?>
		<?php else : ?>
			<span class="portfolio-placeholder">
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php _e( "This is a placeholder. You can remove this placeholder by setting a featured image on the <em>Edit Post</em> screen for this post.", "portfolio" ); ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<div class="thumb-meta">
			<h4 class="thumb-title"><?php the_title(); ?></h4>
			<?php if ( $item_description = portfolio_get_filtered_description() ) : ?>
				<p><?php echo esc_html( $item_description ); ?></p>
			<?php else : ?>
				<?php
					// The views are slightly different, so we truncate at different lengths
					if ( is_page_template( 'homepage.php' ) ) {
						echo portfolio_get_truncated_excerpt( 70 );
					} else {
						echo portfolio_get_truncated_excerpt( 105 );
					}
				?>
			<?php endif; ?>
			<?php if ( is_page_template( 'homepage.php' ) ) : ?>
				<?php if ( $button_label_text = portfolio_option( 'portfolio_item_button_label' ) ) : ?>
					<span class="view-port-item"><?php echo esc_html( $button_label_text ); ?></span>
				<?php endif; ?>
			<?php elseif ( ! portfolio_option( 'hide_date_port' ) ) : ?>
				<span class="thumb-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
				<span class="view-port-item"></span>
			<?php endif; ?>
		</div>
	</div>
</a>