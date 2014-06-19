<?php if ( '' != get_the_post_thumbnail() ) : ?>
	<figure class="page-feature">
		<?php the_post_thumbnail( 'portfolio_featured' ); ?>
		<?php portfolio_post_thumbnail_caption(); ?>
	</figure>
<?php endif; ?>