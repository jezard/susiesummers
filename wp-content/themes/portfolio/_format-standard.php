<article id="post-<?php the_ID(); ?>" <?php post_class( 'text' ); ?>>
	<?php get_template_part( '_post-header' ); ?>
	<?php get_template_part( '_post-content' ); ?>
	<?php get_template_part( '_post-footer' ); ?>
</article>