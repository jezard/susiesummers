<?php
/*
Template Name: With Sidebar
*/
?>
<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<?php get_template_part( '_featured-image' ); ?>
<div id="wrapper" class="content<?php if ( ! is_active_sidebar( 'primary_sidebar' ) ) { echo " no-sidebar"; } ?>">
	<?php get_template_part( '_page-header' ); ?>
	<section class="main-content">
		<?php the_content(); ?>
		<?php edit_post_link( __( 'Edit this page', 'portfolio' ), '<p class="clear">', '</p>' ); ?>
	</section>
	<?php comments_template( '', true ); ?>
</div>
<?php endwhile; ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>