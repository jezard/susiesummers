<article id="post-<?php the_ID(); ?>" <?php post_class( 'image' ); ?>>
	<?php if ( ! is_single() ) : ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
	<?php endif; ?>
	<figure class="post-feature">
		<?php the_post_thumbnail(); ?>
		<?php portfolio_post_thumbnail_caption(); ?>
	</figure>
	<?php if ( ! is_single() ) : ?>
		</a>
	<?php endif; ?>
	<?php get_template_part( '_post-content' ); ?>
</article>