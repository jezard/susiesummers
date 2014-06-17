<?php
/*
Template Name: Homepage
*/
?>
<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<?php get_template_part( '_featured-slider' ); ?>
<div class="content">
	<section class="main-content clear">
		<?php the_content() ?>
	</section>
	<?php
		$portfolio_items_query = new WP_Query( array(
			'meta_query' => array(
				array(
					'key' => PT_MetaKeys::IS_PORTFOLIO_ITEM,
					'value' => true,
					'type' => 'BOOLEAN'
				)
			),
			'posts_per_page' => 3,
			'ignore_sticky_posts' => 1,
			'orderby' => 'date'
		) );
	?>
	<section class="recent-portfolio-items grid">
		<?php if ( $portfolio_items_query->have_posts() ) : ?>
			<?php $counter = 0; ?>
			<?php while ( $portfolio_items_query->have_posts() ) : $portfolio_items_query->the_post(); ?>
				<?php include( locate_template( '_portfolio-item-thumb.php' ) ); ?>
				<?php $counter++; ?>
			<?php endwhile; ?>
			<?php wp_reset_query(); rewind_posts(); the_post(); ?>
		<?php endif; ?>
	</section>
	<?php edit_post_link( __( 'Edit this page', 'portfolio' ), '<p class="clear">', '</p>' ); ?>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>