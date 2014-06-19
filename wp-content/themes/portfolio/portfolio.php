<?php
/*
Template Name: Portfolio
*/
?>
<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<?php get_template_part( '_featured-image' ); ?>
<div class="content tmp-portfolio">
	<?php if( get_the_content() != '' ) : ?>
		<section class="portfolio-content">
			<?php the_content(); ?>
		</section>
	<?php endif; ?>
	<section class="main-content grid clear">
		<?php
			$items_on_page = get_post_meta( get_the_ID(), PT_MetaKeys::PAGE_ITEMS, true );
			// Queries for all $items_on_page and puts them in order
			$portfolio_query = portfolio_get_portfolio_items_in_order( $items_on_page );
		?>
		<?php if ( ! empty( $items_on_page ) ) : ?>
			<?php if ( $portfolio_query->have_posts() ) : ?>
				<?php $counter = 0; ?>
				<?php while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>
					<?php include( locate_template( '_portfolio-item-thumb.php' ) ); ?>
					<?php $counter++; ?>
				<?php endwhile; ?>
			<?php elseif ( current_user_can( 'edit_pages' ) ) : ?>
				<section class="theme-help">
					<p><?php _e( "It looks like your portfolio items may not have featured images - make sure to set a featured image for each of your portfolio items, or they won't show up on this page!", 'portfolio' ); ?></p>
				</section>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		<?php elseif ( current_user_can( 'edit_pages' ) ) : ?>
			<section class="theme-help">
				<p><?php _e( "You don't have any portfolio items set for this page. Create a portfolio item by going to <em>Posts &rarr; Add New</em> on your admin panel and assign it to this page to showcase your work.", 'portfolio' ); ?></p>
				<p><?php _e( "If you've already created portfolio items, you can add them to this page from the <em>Edit Post</em> screen. Make sure you set a featured image for your portfolio items, or they will use a placeholder!", 'portfolio' ); ?></p>
			</section>
		<?php endif; ?>
		<?php edit_post_link( __( 'Edit this page', 'portfolio' ), '<p class="clear">', '</p>' ); ?>
	</section>
	<?php comments_template( '', true ); ?>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>