<section>
<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( portfolio_get_format_template() );
	}
?>
</section>
<nav class="post-footer">
	<p>
		<?php next_posts_link( __( 'Older posts', 'portfolio' ) ); ?>
		<?php previous_posts_link( __( 'Newer posts', 'portfolio' ) ); ?>
	</p>
</nav>