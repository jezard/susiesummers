<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<?php // Portfolio items have a special sidebar and display ?>
<?php if ( portfolio_is_portfolio_item() ) : ?>
	<div id="wrapper" class="content<?php echo ( ! portfolio_show_portfolio_item_sidebar() ) ? " no-sidebar" : ""; ?>">
		<?php get_template_part( '_format-portfolio-item' ); ?>
		<?php comments_template( '', true ); ?>
	</div>
	<?php if ( portfolio_show_portfolio_item_sidebar() ) : ?>
		<?php get_sidebar( 'portfolio-item' ); ?>
	<?php endif; ?>
<?php else : ?>
	<div id="wrapper" class="content<?php echo ( ! is_active_sidebar( 'primary_sidebar' ) ) ? " no-sidebar" : ""; ?>">
		<?php get_template_part( portfolio_get_format_template() ); ?>
		<?php comments_template( '', true ); ?>
		<?php if ( ! portfolio_option( 'hide_post_nav' ) ) : ?>
			<nav>
				<?php if ( get_adjacent_post( false, '', true ) || get_adjacent_post( false, '', false ) ) : ?>
					<p><?php next_post_link( '%link', __( 'Newer post', 'portfolio' ) ); ?><?php previous_post_link( '%link', __( 'Older post', 'portfolio' ) ) ?></p>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
<?php endif; ?>
<?php endwhile; ?>
<?php get_footer(); ?>