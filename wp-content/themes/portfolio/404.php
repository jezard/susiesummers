<?php get_header(); ?>
<h4><?php _e( '404: Page Not Found', 'portfolio' ); ?></h4>
<div id="wrapper" class="content">
	<p><?php _e( 'We are terribly sorry, but the URL you typed no longer exists. It might have been moved or deleted. Try searching the site:', 'portfolio' ); ?></p>
	<?php get_search_form(); ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>