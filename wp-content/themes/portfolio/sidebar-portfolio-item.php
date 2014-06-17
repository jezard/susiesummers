<aside class="colophon" role="complementary">
	<header>
		<h4><?php the_title(); ?></h4>
	</header>
	<?php if ( has_excerpt() ) : ?>
		<section>
			<?php the_excerpt(); ?>
		</section>
	<?php endif; ?>
</aside>