<?php if ( ! is_single() ) : ?>
	<?php $has_comments = ( get_comments_number() > 0 || comments_open() ); ?>
	<?php if ( $has_comments ) : ?>
		<span class="comment-num"><?php comments_popup_link( __( 'Leave a comment', 'portfolio' ), __( '1 Comment', 'portfolio' ), __( '% Comments', 'portfolio' ) ); ?></span>
	<?php endif; ?>
<?php endif; ?>
<footer class="clear">
	<?php
		// We will hide tags/categories for this post if:
		// A: Portfolio items are being hidden from blog pages AND
		// B: This is a portfolio item
		$hide_taxonomies = ( portfolio_option( 'hide_portfolio_items' ) && portfolio_is_portfolio_item() );
	?>
	<?php if ( ! portfolio_option( 'hide_categories' ) && ! $hide_taxonomies ) : ?>
		<?php if  ( has_category() ) : ?>
			<div class="cat-links">
				<?php _e( 'Categories: ', 'portfolio' ); ?>
				<?php the_category( ', ' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( ! portfolio_option( 'hide_tags' ) && ! $hide_taxonomies ) : ?>
		<?php if  ( has_tag() ) : ?>
			<div class="tag-links">
				<?php the_tags( __( 'Tagged: ', 'portfolio' ) ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</footer>