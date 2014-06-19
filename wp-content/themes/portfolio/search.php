<?php get_header(); ?>
	<h4><?php printf( __( "Search results for '<em>%s</em>'", "portfolio" ), get_search_query() ); ?></h4>
	<div id="wrapper" class="content<?php echo ( ! is_active_sidebar( 'primary_sidebar' ) ) ? " no-sidebar" : ""; ?>">
		<?php if ( have_posts() ) : ?>
			<?php get_template_part( '_loop' ); ?>
		<?php else : ?>
			<div>
				<p><?php printf( __( 'Sorry, your search for "<em>%s</em>" did not turn up any results. Please try again.', 'portfolio' ), get_search_query());?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>