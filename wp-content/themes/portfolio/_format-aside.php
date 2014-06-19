<article id="post-<?php the_ID(); ?>" <?php post_class( 'aside' ); ?>>
	<?php get_template_part( '_post-content' ); ?>
	<a href="<?php the_permalink(); ?>" rel="bookmark">
		<span class="post-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
	</a>
</article>