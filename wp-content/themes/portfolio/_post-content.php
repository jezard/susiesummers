<section class="main-content">
	<?php if ( is_search() ) : ?>
		<?php the_excerpt(); ?>
	<?php else : ?>
		<?php the_content( __( 'Continue reading&hellip;', 'portfolio' ) ); ?>
	<?php endif; ?>
	<?php edit_post_link( __( 'Edit this post', 'portfolio' ), '<p class="clear">', '</p>' ); ?>
		<div class="clear">
			<?php wp_link_pages( 'before=<p class="page-links">Page:&after=</p>' ); ?>
		</div>
</section>