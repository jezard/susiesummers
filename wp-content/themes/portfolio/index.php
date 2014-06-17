<?php get_header(); ?>
<div id="wrapper" class="content<?php if ( ! is_active_sidebar( 'primary_sidebar' ) ) { echo " no-sidebar"; } ?>">
	<?php get_template_part( '_loop' ); ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>