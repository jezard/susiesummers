<?php get_header(); ?>
<?php if ( have_posts() ) : the_post(); ?>
	<h4><?php ttf_common_archives_title(); ?></h4>
<?php rewind_posts(); ?>
<?php endif; ?>
<div id="wrapper" class="content<?php echo ( ! is_active_sidebar( 'primary_sidebar' ) ) ? " no-sidebar" : ""; ?>">
	<?php if ( have_posts() ) : ?>
		<?php get_template_part( '_loop' ); ?>
	<?php else : ?>
		<p><?php _e( 'No posts found.', 'portfolio' ); ?></p>
	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>