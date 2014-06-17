<?php
/*
Template Name: Sitemap
*/
?>
<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
<?php get_template_part( '_featured-image' ); ?>
<div id="wrapper" class="content tmp-sitemap<?php if ( ! is_active_sidebar( 'primary_sidebar' ) ) { echo " no-sidebar"; } ?>">
	<?php get_template_part( '_page-header' ); ?>
	<?php the_content( '' ); ?>
	<section>
	<h4><?php _e( 'Pages', 'portfolio' ); ?></h4>
	<ul>
		<?php wp_list_pages( 'sort_column=menu_order&depth=0&title_li=' ); ?>
	</ul>
	<h4><?php _e( 'Recent posts', 'portfolio' ); ?></h4>
	<ul>
		<?php query_posts( 'showposts=25' ); ?>
		<?php if ( have_posts() ) : ?>
				<ul>
					<?php while ( have_posts() ) : the_post(); ?>
						<li>
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
								<?php echo get_the_title() ? get_the_title() : 'Untitled'; ?>
							</a>
							<time datetime="<?php the_time( 'Y-M-D\Th:m:sT' ); ?>" pubdate="pubdate">
								/ <?php the_time( get_option( 'date_format' ) ); ?>
							</time>
						</li>
					<?php endwhile; ?>
				</ul>
		<?php endif; ?>
	</ul>
	<h4><?php _e( 'Authors', 'portfolio' ); ?></h4>
	<ul>
		<?php wp_list_authors(); ?>
	</ul>
	<h4><?php _e( 'Categories', 'portfolio' ); ?></h4>
	<ul>
		<?php wp_list_categories( 'depth=0&title_li=&show_count=1' ); ?>
	</ul>
	<h4><?php _e( 'Archives', 'portfolio' ); ?></h4>
	<ul>
		<?php wp_get_archives( 'type=monthly' ); ?>
	</ul>
	<?php wp_reset_query(); rewind_posts(); the_post(); ?>
	<?php edit_post_link( __( 'Edit this page', 'portfolio' ), '<p class="clear">', '</p>' ); ?>
	<section>
	<?php comments_template( '', true ); ?>
</div>
<?php endwhile; ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>