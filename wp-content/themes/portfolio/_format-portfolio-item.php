<article id="post-<?php the_ID(); ?>" class="portfolio-single">
	<?php if ( ! portfolio_show_portfolio_item_sidebar() ) : ?>
		<?php get_template_part( '_post-header' ); ?>
	<?php endif; ?>
	<?php get_template_part( '_post-content' ); ?>
	<?php get_template_part( '_post-footer' ); ?>
</article>